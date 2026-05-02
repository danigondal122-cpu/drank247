<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerCartController extends Controller
{
	public function addToCart(Request $request): JsonResponse
	{
		Cart::add([
			'id' => $request->productid,
			'name' => $request->productname,
			'qty' => $request->productqty ?? 1,
			'vat_price' => $request->vatprice,
			'options' => [
				'image' => $request->productimage,
				'category_name' => $request->productcategory,
				'original_price' => $request->productprice,
			]
		]);
		
		$payment = Cart::payment();
		$cartData = [
			'item_count' => Cart::count(),
			'cart_total_price' => Cart::subtotal(),
			'delivery_charge' => $payment['delivery_charge'],
			'final_amount' => $payment['total'],
		];

		return response()->json([
			'status' => true,
			'message' => 'Product has been added in Cart',
			'data' => $cartData
		]);
	}

	public function removeItem(Request $request): JsonResponse
	{
		Cart::remove($request->rowid);

		$payment = Cart::payment(discount: [
			'type' => $request->Discount_type,
			'inper' => $request->Discount_inper
		], postcode: $request->postcode);

		$cartData = [
			'item_count' => Cart::count(),
			'cart_total_price' => Cart::subtotal(),
			'delivery_charge' => $payment['delivery_charge'],
			'finalamount' => $payment['total'],
			'discountamount' => $payment['discount_amount'],
			'finalamount_withdiscount' => $payment['total_with_discount'],
		];

		return response()->json([
			'status' => true,
			'message' => 'Item removed Successfully',
			'data' => $cartData
		]);
	}

	public function updateItemQty(Request $request): JsonResponse
	{
		Cart::update($request->cart_id, $request->cart_qty);

		$item_vat = $request->cart_qty * $request->vat_price - $request->cart_qty * $request->product_price;
		$item_vat = number_format($item_vat, 2);
		$item_total_cost = number_format($request->cart_qty * $request->vat_price, 2);
		$item_vat_cost = number_format($request->cart_qty * $request->vat_price, 2);

		$payment = Cart::payment(discount: [
			'type' => $request->Discount_type,
			'inper' => $request->Discount_inper
		], postcode: $request->postcode);
		
		$cartData = [
			'item_total_cost' => $item_total_cost,
			'item_vat' => $item_vat,
			'item_vat_cost' => $item_vat_cost,
			'item_count' => Cart::count(),
			'cart_total_price' => Cart::subtotal(),
			'delivery_charge' => $payment['delivery_charge'],
			'finalamount' => $payment['total'],
			'discountamount' => $payment['discount_amount'],
			'finalamount_withdiscount' => $payment['total_with_discount'],
		];

		return response()->json([
			'status' => true,
			'message' => 'Item Updated Successfully',
			'data' => $cartData
		]);
	}

	public function customizeProduct(Request $request): View
	{
		$mainProduct = Product::where('id', $request->id)
			->whereNull('deleted_at')
			->first();
		$extraProducts = $mainProduct->extraProducts()->get();

		$cartMainProduct = Cart::get($request->id)->first();
		$mainProduct->qty = $cartMainProduct ? $cartMainProduct->qty : 1;
		$mainProduct->vat_total = $cartMainProduct
			? $cartMainProduct->format('total:vat_price', 2)
			: $mainProduct->vat_price;
		$mainProduct->rowId = $cartMainProduct ? $cartMainProduct->id : 0;

		foreach ($extraProducts as $index => $value)
		{
			$cartExtraProduct = Cart::get($value->id)->first();

			$extraProducts[$index]->qty = $cartExtraProduct? $cartExtraProduct->qty : 1;
			$extraProducts[$index]->vat_total = $cartExtraProduct
				? $cartExtraProduct->format('total:vat_price', 2)
				: $value->vat_price;
			$extraProducts[$index]->rowId = $cartExtraProduct ? $cartExtraProduct->id : 0;
		}

		return view('modal.customize-product', [
			'mainProduct' => $mainProduct,
			'extraProducts' => $extraProducts,
		]);
	}

	public function customizedItemQty(Request $request): JsonResponse
	{
		$item = Cart::get($request->productid)->first();

		if (!$item)
		{
			Cart::add([
				'id' => $request->productid,
				'name' => $request->productname,
				'qty' => $request->productqty ?? 1,
				'vat_price' => $request->vatprice,
				'options' => [
					'image' => $request->productimage,
					'category_name' => $request->productcategory,
					'original_price' => $request->productprice,
				]
			]);
		}
		else
		{
			Cart::update($request->productid, $request->productqty);
		}

		$itemAmount = Cart::get($request->productid)->first()?->format('total:vat_price', 2);

		return response()->json([
			'status' => true,
			'message' => 'Product has been added in Cart',
			'data' => [
				'item_count' => Cart::count(),
				'cart_total_price' => Cart::subtotal(),
				'final_amount' => Cart::payment('total'),
				'show_amount' => $itemAmount,
				'rowId' => $request->productid,
			]
		]);
	}

	public function removeCustomizedItem(Request $request): JsonResponse
	{
		Cart::remove($request->productRowId);

		return response()->json([
			'status' => true,
			'message' => 'Product has been removed',
			'data' => [
				'item_count' => Cart::count(),
				'final_amount' => Cart::payment('total'),
			]
		]);
	}
}
