<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Mail\ContactUsMail;
use App\Mail\CustomerCredentialsMail;
use App\Models\ContactUs;
use App\Models\Customer;
use App\Models\Franchise;
use App\Models\Pool;
use App\Models\Product;
use App\Rules\ReCaptcha;
use App\Services\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GuestAjaxController extends Controller
{
	public function checkPostCode(Request $request): JsonResponse
	{
		$validator = Validator::make($request->all(), [
			'postcode' => 'required',
		]);

		if ($validator->fails())
		{
			return response()->json([
				'status' => false,
				'type' => 'validation',
				'errors' => $validator->errors()
			]);
		}
		
		$postcode = $request->postcode;
        $poolsArr = Franchise::poolsArray();
        $pools = Pool::whereIn('id', $poolsArr)
            ->whereAttr($postcode)
			->orWhere('area', 'LIKE', "%$postcode%")
            ->get()
			->count();

		if (!$pools)
		{
			return response()->json([
				'status' => false,
				'type' => 'invalidPostcode',
				'message' => 'We do not provide our service on your postcode area. Please call customer service for more details.',
			]);
		}
		
		return response()->json([
			'status' => true,
			'type' => 'success',
			'message' => 'We deliver at your postcode!',
		]);
	}

	public function autocomplete(Request $request): JsonResponse
	{
		$response = Product::where('product_name', 'LIKE', "%%{$request->search}%%")
			->get()
			->map(function ($product) {
				return [
					'value' => $product->category_id ?: 'extra_product',
					'label' => $product->product_name,
				];
			})
			->toArray();

		return response()->json($response);
	}

	public function productDetail(Request $request): View
	{
		$data['product'] = Product::where('id', $request->id)
			->whereNull('deleted_at')
			->first();

		return view('modal.product-detail', $data);
	}

	public function contactUs(Request $request): JsonResponse
	{
		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'email' => 'required|email',
			'contact_no' => 'required',
			'subject' => 'required',
			'other_subject' => 'required_if:subject,other',
			'message' => 'required',
			// 'g-recaptcha-response' => ['required', new ReCaptcha()]
		], [
			'g-recaptcha-response.required' => 'Invalid captcha code.'
		]);

		if ($validator->fails())
		{
			return response()->json([
				'status' => false,
				'type' => 'VALIDATION',
				'errors' => $validator->errors(),
			]);
		}

		$to_send = 'Customer Service';
		$to_mail = env('CUSTOMERCARE_EMAIL');
		$admin_name = $to_send;

		if ($request->subject == '0')
		{
			$subject = 'Question about, Delivery, Price, Work, Allergies, other ..';
		}
		else if ($request->subject == '1')
		{
			$subject = 'How long will it take for your order to arrive';
		}
		else if ($request->subject == '2')
		{
			$subject = 'Whether the order has arrived safely';
		}
		else if ($request->subject == '3')
		{
			$subject = 'Something went wrong with the payment or other system error';
			$to_send = 'Admin';
			$to_mail = env('ADMIN_EMAIL');
			$admin_name = $to_send;
		}
		else if ($request->subject == '4')
		{
			$subject = 'interested in franchise or delivery options';
			$to_send = 'Admin';
			$to_mail = env('ADMIN_EMAIL');
			$admin_name = $to_send;
		}
		else
		{
			$subject = $request->other_subject;
		}

		$page = '';
		$contact = ContactUs::create([
			'name' => $request->name,
			'email' => $request->email,
			'contact_no' => $request->contact_no,
			'subject' => $subject,
			'to_send' => $to_send,
			'message' => $request->message,
		]);

		$mailData = $contact->toArray();
		$mailData['admin_name'] = $admin_name;

		if (app()->environment('production'))
        {
			Mail::to($to_mail)->send(new ContactUsMail($mailData));
		}
		else
        {
            $page = 'email-render';
            session()->flash('mail', [
                'class' => ContactUsMail::class,
                'data' => $mailData
            ]);
        }

		return response()->json([
			'status' => true,
			'page' => $page,
			'msg' => 'Message sent successfully!'
		]);
	}

	public function getDeliveryCharge(Request $request): JsonResponse
    {
		$payment = Cart::payment(postcode: $request->post_code);
        $data = [
            'delivery_charge' => $payment['delivery_charge'],
            'final_amount' => $payment['total'],
            'withDiscount_FinalAmount' => $payment['total_with_discount'],
        ];

        return response()->json([
			'status' => true,
			'data' => $data
		]);
    }

	public function checkout(Request $request): JsonResponse
    {
		$validator = Validator::make($request->all(), [
			'customer_name' => 'required',
			'customer_email' => 'required|email|unique:customers,customer_email',
			'post_code' => 'required',
			'house_no' => 'required',
		], [
			'customer_name.required' => 'Please add customer Name.',
			'customer_email.required' => 'Please add customer Email.',
			'customer_email.unique' => 'Email is already used.',
			'post_code.required' => 'Please add Your Address.',
			'house_no.required' => 'Please add Your Address.',
		]);

		if ($validator->fails())
		{
			return response()->json([
				'status' => false,
				'type' => 'VALIDATION',
				'message' => $validator->errors()->first(),
			]);
		}

        $poolsArr = Franchise::poolsArray();
        $pool = Pool::whereIn('id', $poolsArr)
            ->whereAttr($request->post_code)
            ->first();

        if (!$pool)
        {
            return response()->json([
                'status' => false,
                'type' => 'invalidPostcode',
                'message' => 'We do not provide our service on your postcode area. Please call customer service for more details.',
            ]);
        }

		$houseno = preg_replace('/[^0-9.]+/', '', $request->house_no);
		$response = nlPostcode($request->post_code, $houseno);

		if (isset($response['error']))
		{
			return response()->json([
				'status' => false,
				'type' => 'InvalidAddress',
				'message' => "We couldn't fetch your address, please try again with correct details or enter it manually",
			]);
		}
		
		$paymentAmount = $request->promo_code 
            ? $request->withDiscount_FinalAmount
            : $request->final_amount;
        
        if ($paymentAmount <= $pool->delivery_start_from)
        {
            return response()->json([
                'status' => false,
                'type' => 'InvalidAmount',
                'message' => 'Minimum order amount is € ' . $pool->delivery_start_from
            ]);
        }

		$password = Str::random(8);
		$customer = Customer::create([
			'customer_name' => $request->customer_name,
			'customer_email' => $request->customer_email,
			'password' => Hash::make($password),
			'customer_type' => 0,
		]);

		$fullAddress = $request->houseno . ', ' . $response['street'] . ', ' . $response['city'] . ', ' . $response['province'];
		$customer->address()->create([
			'default' => !$customer->address()->whereDefault()->get()->count() ? '1' : '0',
			'address' => $fullAddress,
			'post_code' => $response['postcode'],
			'latitude' => $response['latitude'],
			'longitude' => $response['longitude'],
			'house_no' => $response['house_number'],
			'manual' => '0',
		]);

		auth('customer')->attempt([
			'customer_email' => $request->customer_email,
			'password' => $password
		]);

		if (app()->environment('production'))
        {
			Mail::to($request->customer_email)->send(
				new CustomerCredentialsMail([
					'name' => $request->customer_name,
					'email' => $request->customer_email,
					'password' => $password,
				])
			);
		}

		return response()->json(['status' => true]);
    }
}
