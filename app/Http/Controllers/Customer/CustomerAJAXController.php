<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\RateAndReview;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CustomerAJAXController extends Controller
{
	public function favourite(Request $request): JsonResponse
	{
		$customer = auth('customer')->user();
		$request->validate([
			'productid' => 'required|integer',
		]);

		$favourite = $customer
			?->favourites()
			->where('product_id', $request->productid)
			->first();

		if ($favourite) {
			$favourite->delete();
			$message = 'Product has been removed from Favourite';
		} else {
			$customer
				?->favourites()
				->create(['product_id' => $request->productid]);
			$message = 'Product has been added to Favourite';
		}

		$itemCount = $customer
			?->favourites()
			->count();
		return response()->json([
			'status' => true,
			'message' => $message,
			'data' => [
				'item_count' => $itemCount,
			],
		]);
	}

	public function selectAddress(): View
	{
		$customer = auth('customer')->user();

		return view('modal.select-address', [
			'addresses' => $customer?->address()->orderBy('default', 'desc')->get()
		]);
	}

	public function getOrderDetail(Request $request)
	{
		$order = Order::find($request->id);

		return view('customer.order-detail', [
			'order' => $order,
			'status' => $order->orderStatus,
			'details' => $order->orderDetails()->get(),
			'review' => $order->rateAndReviews,
		]);
	}

	public function checkPromoCode(Request $request): JsonResponse
	{
		$validator = Validator::make($request->all(), [
			'promocode' => 'required'
		]);

		if ($validator->fails())
		{
			return response()->json([
				'status' => false,
				'type' => 'validation',
				'errors' => $validator->errors()
			]);
		}

		$date = (Carbon::now())->toDateString();
		$code = PromoCode::where('code_text', $request->promocode)
			->where('code_status', '1')
			->where(function ($query) use ($date) {
				return $query->where('expiration_type', 0)
					->where('start_date', '<=', $date);
			})
			->orWhere(function ($query) use ($date) {
				return $query->where('expiration_type', 1)
					->where('start_date', '<=', $date)
					->where('end_date', '>=', $date);
			})
			->first();

		if (!$code)
		{
			return response()->json([
				'status' => false,
				'type' => 'invalidPromoCode',
				'message' => 'Please Enter Valid Promo Code!',
			]);
		}

		$customer = auth('customer')->user();
		$usedCode = $customer?->usedPromoCodes()
			->where('promo_code_id', $code->id)
			->first();

		$customerCount = $usedCode?->used_count ?? 0;

		$usedLimit = $code->usedPromoCodes()
			->get()
			->sum('used_count');

		$isValid = $customerCount < $code->max_per_user && ($code->expiration_type == 1
			? $usedLimit < $code->max_users
			: $code->expiration_type == 0
		);

		if (!$isValid)
		{
			return response()->json([
				'status' => false,
				'type' => 'invalidPromoCode',
				'message' => 'Please Enter Valid Promo Code!',
			]);
		}

		if ($code->discount_type == 0 && $request->finalamount < $code->discount)
		{
			return response()->json([
				'status' => false,
				'type' => 'invalidPromoCode',
				'message' => 'Final Price should be greater than € ' . $code->discount,
				'discount_type' => $code->discount_type,
				'discount' => $code->discount,
				'promo_code' => $code->id,
			]);
		}

		return response()->json([
			'status' => true,
			'type' => 'valid',
			'message' => 'Promo code applied successfully!',
			'discount_type' => $code->discount_type,
			'discount' => $code->discount,
			'promo_code' => $code->id,
		]);
	}

	public function rateAndReview(Request $request): JsonResponse
	{
		$customer = auth('customer')->user();
		$order = Order::find($request->order_id);

		if (!$order)
		{
			return response()->json([
				'status' => false,
				'type' => 'SYSTEM',
				'errors' => 'Something went wrong!'
			]);
		}

		$data = [
			'delivery_person_id' => $request->dp_id,
			'review' => '',
		];

		if (!$request->currentRating)
		{
			$validator = Validator::make($request->all(), [
				'review' => 'required'
			]);
	
			if ($validator->fails())
			{
				return response()->json([
					'status' => false,
					'type' => 'VALIDATION',
					'errors' => $validator->errors()
				]);
			}

			$data['review'] = $request->review;
			$data['rate'] = $request->rate;
		}
		else
		{
			$data['rate'] = $request->currentRating;
		}

		RateAndReview::updateOrCreate([
			'order_id' => $order->id,
			'customer_id' => $customer->id,
		], $data);

		return response()->json([
			'status' => true,
			'message' => 'Success!'
		]);
	}
}
