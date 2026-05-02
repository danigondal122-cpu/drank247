<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pool;
use App\Services\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerProfileController extends Controller
{
	public function updateProfile(Request $request): JsonResponse
	{
		$customer = auth('customer')->user();

		if (!$customer) {
			return response()->json([
				'status' => false,
				'type' => 'SYSTEM',
				'msg' => 'Something went wrong!',
			]);
		}

		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'contact_no' => 'required'
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'type' => 'VALIDATION',
				'errors' => $validator->errors()
			]);
		}

		$data = [
			'customer_name' => $request->name,
			'customer_contact_no' => $request->country_code . '-' . $request->contact_no,
			'customer_type' => $request->type,
			'profile' => $customer->profile->store(
				file: $request->image_file,
				closure: function ($file, $fileName, $path, $key) {
					if ($key == 'thumb') {
						$image = Image::read($file);
						$image->resize(150, 150)
							->save($path . $fileName);
					} else {
						$file->move($path, $fileName);
					}
				}
			)
		];

		$customer->update($data);

		return response()->json([
			'status' => true,
			'msg' => 'Profile updated!'
		]);
	}

	public function changePassword(Request $request): JsonResponse
	{
		$customer = auth('customer')->user();

		if (!$customer) {
			return response()->json([
				'status' => false,
				'type' => 'SYSTEM',
				'msg' => 'Something went wrong!',
			]);
		}

		if ($customer->login_type != 'NORMAL') {
			return response()->json([
				'status' => false,
				'type' => 'VALIDATION',
				'msg' => 'You cannot change password because your account is associated with your social account.',
			]);
		}

		$validator = Validator::make($request->all(), [
			'current_password' => 'required|current_password',
			'new_password' => [
				'required',
				'confirmed',
				Password::defaults()
			]
		], [
			'current_password' => 'Current password is incorrect.',
			'new_password.confirmed' => 'The password confirmation does not match.'
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'type' => 'VALIDATION',
				'errors' => $validator->errors()
			]);
		}

		$customer->update([
			'password' => Hash::make($request->new_password)
		]);

		return response()->json([
			'status' => true,
			'type' => 'SUCCESS',
			'page' => 'admin/dashboard',
			'msg' => 'Password changed successfully',
		]);
	}

	public function addressList(): View
	{
		$customer = auth('customer')->user();
		$data['addresses'] = $customer?->address()
			->orderBy('default', 'desc')
			->get();

		return view('modal.address', $data);
	}

	public function addUpdateFetchAddress(Request $request): JsonResponse
	{
		$customer = auth('customer')->user();

		if (!$customer) {
			return response()->json([
				'status' => false,
				'type' => 'SYSTEM',
				'msg' => 'Something went wrong!',
			]);
		}

		$validator = Validator::make($request->all(), [
			'houseno' => 'required',
			'postcode' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'type' => 'VALIDATION',
				'errors' => $validator->errors()
			]);
		}

		$postcode = preg_replace('/[^0-9.]+/', '', $request->postcode);
		$houseno = preg_replace('/[^0-9.]+/', '', $request->houseno);
		$pool = Pool::whereAttr($postcode)->first();

		if (!$pool && Pool::all()->count()) {
			return response()->json([
				'status' => false,
				'type' => 'NotValid',
				'msg' => 'We do not provide our service on your postcode area. Please call customer service for more details.',
			]);
		}

		$response = nlPostcode($request->postcode, $houseno);

		if (isset($response['error'])) {
			return response()->json([
				'status' => false,
				'type' => 'InvalidAddress',
				'msg' => "We couldn't fetch your address, please try again with correct details or enter it manually",
				'house_no' => $request->houseno,
				'postcode' => $request->postcode,
			]);
		}

		$isUpdate = false;
		$fullAddress = $request->houseno . ', ' . $response['street'] . ', ' . $response['city'] . ', ' . $response['province'];
		$data = [
			'address' => $fullAddress,
			'post_code' => $response['postcode'],
			'latitude' => $response['latitude'],
			'longitude' => $response['longitude'],
			'house_no' => $response['house_number'],
		];

		if ($request->address_id) {
			$isUpdate = true;
			$address = $customer->address()->find($request->address_id);

			if (!$address) {
				return response()->json([
					'status' => false,
					'type' => 'SYSTEM',
					'msg' => 'Something went wrong!',
				]);
			}

			$address->update($data);
			$msg = 'Address Updated successfully';
		} else {
			$data['default'] = !$customer->address()->get()->count() ? '1' : '0';
			$data['manual'] = '0';
			$address = $customer->address()->create($data);
			$msg = 'Address Added successfully';
		}

		return response()->json([
			'status' => true,
			'msg' => $msg,
			'address' => $fullAddress,
			'address_id' => $address->id,
			'postcode' => $address->post_code,
			'update_status' => $isUpdate,
			'total_count' => $customer->address()->get()->count(),
		]);
	}

	public function addUpdateManualAddress(Request $request): JsonResponse
	{
		$customer = auth('customer')->user();

		if (!$customer) {
			return response()->json([
				'status' => false,
				'type' => 'SYSTEM',
				'msg' => 'Something went wrong!',
			]);
		}

		$validator = Validator::make($request->all(), [
			'houseno' => 'required',
			'postcode' => 'required',
			'street' => 'required',
			'city' => 'required',
			'state' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'type' => 'VALIDATION',
				'errors' => $validator->errors()
			]);
		}

		$fullAddress = $request->houseno . ', ' . $request->street . ', ' . $request->city . ', ' . $request->state;
		$data = [
			'address' => $fullAddress,
			'post_code' => $request->postcode,
			'house_no' => $request->houseno,
		];

		if (!$request->id) {
			$data['default'] = !$customer->address()->get()->count() ? '1' : '0';
			$data['latitude'] = '';
			$data['longitude'] = '';
			$data['manual'] = '1';
		}

		$customer->address()->updateOrCreate([
			'id' => $request->id
		], $data);

		return response()->json([
			'status' => true,
			'msg' => 'Address Added successfully',
			'address' => $fullAddress,
		]);
	}

	public function setDefaultAddress(Request $request): JsonResponse
	{
		$customer = auth('customer')->user();

		if (!$customer) {
			return response()->json([
				'status' => false,
				'type' => 'SYSTEM',
				'msg' => 'Something went wrong!',
			]);
		}

		$validator = Validator::make($request->all(), [
			'address_id' => 'required'
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'type' => 'VALIDATION',
				'errors' => $validator->errors()
			]);
		}

		$customer->address()->update(['default' => '0']);
		$customer->update(['customer_address' => $request->address_id]);
		$address = $customer->address()->find($request->address_id);

		if (!$address) {
			return response()->json([
				'status' => false,
				'type' => 'SYSTEM',
				'msg' => 'Something went wrong!',
			]);
		}

		$address->update(['default' => '1']);
		$payment = Cart::payment(discount: [
			'type' => $request->Discount_type,
			'inper' => $request->Discount_inper
		], postcode: $request->postcode);

		return response()->json([
			'status' => true,
			'msg' => 'Default Address updated!',
			'data' => [
				'item_count' => Cart::count(),
				'cart_total_price' => Cart::subtotal(),
				'delivery_charge' => $payment['delivery_charge'],
				'finalamount' => $payment['total'],
				'discountamount' => $payment['discount_amount'],
				'finalamount_withdiscount' => $payment['total_with_discount'],
			]
		]);
	}

	public function addressDetails($id): JsonResponse
	{
		$customer = auth('customer')->user();

		if (!$customer) {
			return response()->json([
				'status' => false,
				'type' => 'SYSTEM',
				'msg' => 'Something went wrong!',
			]);
		}

		return response()->json([
			'status' => true,
			'details' => $customer->address()->find($id)
		]);
	}

	public function addressDelete(Request $request): JsonResponse
	{
		$customer = auth('customer')->user();

		if (!$customer) {
			return response()->json([
				'status' => false,
				'type' => 'SYSTEM',
				'msg' => 'Something went wrong!',
			]);
		}

		$validator = Validator::make($request->all(), [
			'address_id' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'type' => 'VALIDATION',
				'errors' => $validator->errors()
			]);
		}

		$customer->address()->where('id', $request->address_id)->delete();
		$update = !$customer->address()->whereDefault()->get()->count() && $customer->address()->get()->count();

		if ($update) {
			$customer->address()
				->orderBy('id', 'DESC')
				->take(1)
				->update(['default' => '1']);
		}

		return response()->json([
			'status' => true,
			'msg' => 'Address deleted!',
		]);
	}
}
