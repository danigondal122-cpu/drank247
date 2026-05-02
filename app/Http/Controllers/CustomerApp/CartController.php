<?php

namespace App\Http\Controllers\CustomerApp;

use App\Http\Controllers\Controller;

use App\Models\Cart;
use App\Models\CustomerAddress;
use App\Models\Pool;
use App\Models\Product;
use App\Models\PromoCode;

use Illuminate\Http\Request;

class CartController extends Controller
{
  public function cartList(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $promocode = $request->input('promocode');

    $data['cartItems'] = Cart::leftJoin('products', 'products.product_id', 'cart.cart_itemid')
      ->leftJoin('categories', 'categories.category_id', 'products.category_id')
      ->where('cart_custid', $id)
      ->get(['cart_id', 'cart_itemid', 'cart_qty', 'cart_itemprice', 'cart_total', 'cart_vatprice', 'cart_vattotal', 'product_id', 'product_name', 'products.image', 'products.category_id', 'category_name']);

    foreach ($data['cartItems'] as $key => $value) {
      $data['cartItems'][$key]['category_name'] = $value['category_name'] == null ? "" : $value['category_name'];
      $data['cartItems'][$key]['image'] = $value['image'] != "" ? asset('uploads/product') . '/' . $value['image'] :  asset('img/247-Drank-Logo.png');
    }
    $address = CustomerAddress::where('customer_id', $id)->where('default', '1')->first();
    $data['cart_total_price'] = Cart::where('cart_custid', $id)->get()->sum('cart_vattotal');

    if ($address != null && $address != '') {
      $pool = Pool::whereNull('deleted_at')->get();
      $array = [];

      foreach ($pool as $value) {
        $code = preg_replace('/[^0-9.]+/', '', $address['post_code']);
        if ($code >= $value->from_postcode && $code <= $value->to_postcode) {
          $array[] = $value['pool_id'];
        }
      }
      if (count($array) > 0) {

        $pool_id = $array['0'];
        $pooldetail = Pool::find($pool_id);
        if ($data['cart_total_price'] != "0" && $data['cart_total_price'] <= $pooldetail['delivery_freefrom']) {
          $data['delivery_charge'] = (int)$pooldetail['delivery_charge'];
        } else if ($data['cart_total_price'] >= $pooldetail['delivery_freefrom']) {
          $data['delivery_charge'] = 0.00;
        } else {
          $data['delivery_charge'] = 0.00;
        }
        $data['delivery_charge'] = ($address != null && $address != '') ? $data['delivery_charge'] : 0.00;
        $data['final_amount'] = $data['delivery_charge'] + $data['cart_total_price'];
      } else {

        if ($data['cart_total_price'] > 75) {
          $data['delivery_charge'] = 0.00;
        } else {
          $data['delivery_charge'] = 2.50;
        }
        $data['final_amount'] = $data['delivery_charge'] + $data['cart_total_price'];
      }
    } else {
      $data['delivery_charge'] = 2.50;
      $data['final_amount'] = $data['delivery_charge'] + $data['cart_total_price'];
    }

    $data['cart_total_price'] = number_format($data['cart_total_price'], 2);
    $data['delivery_charge'] = number_format($data['delivery_charge'], 2);
    $data['final_amount'] = number_format($data['final_amount'], 2);
    if ($promocode != '') {
      $code_detail = PromoCode::where('code_text', "$promocode")->where('code_status', '1')->first();
      $discount_type = $code_detail['discount_type'];
      $discount = $code_detail['discount'];
      if ($discount_type == 0) {
        $discount = $discount;
      } else {
        $discount = str_replace(',', '', $data['final_amount']) * $discount / 100;
      }
      $final_amount_with_discount = str_replace(',', '', $data['final_amount']) - $discount;
      if ($final_amount_with_discount <= 0) {
        $data['discount'] = '0.00';
        $data['final_amount_with_discount'] = $data['final_amount'];
      } else {
        $data['discount'] = number_format($discount, 2);
        $data['final_amount_with_discount'] = number_format($final_amount_with_discount, 2);
      }
    } else {
      $data['discount'] = '0.00';
      $data['final_amount_with_discount'] = $data['final_amount'];
    }
    //fetch Address Detail
    $address = CustomerAddress::where('customer_id', $id)->where('default', '1')->first();
    $data['address_id'] = $address != null ? $address['address_id'] : 0;
    $data['address'] = $address != null ? $address['address'] : '';
    $data['post_code'] = $address != null ? $address['post_code'] : '';

    return response()->json(['status' => true, 'data' => $data]);
  }
  public function updateQty(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $product_id = $request->input('product_id');
    $cart_qty = $request->input('qty');
    $isExist = Cart::where('cart_itemid', $product_id)->where('cart_custid', $id)->first();
    $cart_total = $cart_qty * $isExist['cart_itemprice'];
    $cart_vattotal = $cart_qty * $isExist['cart_vatprice'];
    Cart::where('cart_itemid', $product_id)->where('cart_custid', $id)->update(['cart_qty' => $cart_qty, 'cart_total' => $cart_total, 'cart_vattotal' => $cart_vattotal]);

    $data['cart_qty'] = $cart_qty;
    $data['cart_total'] = (number_format($cart_total, 2));
    $data['cart_vattotal'] = (number_format($cart_vattotal, 2));

    return response()->json(['status' => true, 'data' => $data]);
  }
  public function addToCart(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $product_id = $request->input('product_id');
    $language = $request->input('language');

    $Detail = Product::find($product_id);

    $product_price = $Detail['product_price'];
    $vat_price = $Detail['vat_price'];
    $isExist = Cart::where('cart_custid', $id)->where('cart_itemid', $product_id)->first();
    if ($isExist) {
      $newQty = $isExist->cart_qty;
      $cart = Cart::find($isExist->cart_id);
      $cart->cart_qty = ($newQty) + 1;
      $cart->cart_vattotal = ($newQty + 1) * $vat_price;
      $cart->cart_total = ($newQty + 1) * $product_price;
      $cart->save();
    } else {
      $cart = new Cart();
      $cart->cart_custid = $id;
      $cart->cart_itemid = $product_id;
      $cart->cart_qty = 1;
      $cart->cart_itemprice = $product_price;
      $cart->cart_total =  $product_price;
      $cart->cart_vatprice = $vat_price;
      $cart->cart_vattotal = $vat_price;
      $cart->save();
    }
    $message = ($language == 'nl') ?  'Product toegevoegd in winkelwagen'  : 'Product Added In Cart';
    return response()->json(['status' => true, 'message' => $message]);
  }
  public function removeFromCart(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $product_id = $request->input('product_id');
    $language = $request->input('language');

    Cart::where('cart_itemid', $product_id)->where('cart_custid', $id)->delete();

    $message = ($language == 'nl') ?  'Product verwijderd uit winkelwagen'  : 'Product Removed From Cart';
    return response()->json(['status' => true, 'message' => $message]);
  }
  public function getCartCounts(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $count = Cart::where('cart_custid', $id)->get()->count();
    return response()->json(['status' => true, 'cart_count' => $count]);
  }
  public function customizedAddToCart(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $product_id = $request->input('product_id');
    $qty = $request->input('qty');
    $language = $request->input('language');


    $Detail = Product::find($product_id);
    $product_price = $Detail['product_price'];
    $vat_price = $Detail['vat_price'];
    $isExist = Cart::where('cart_custid', $id)->where('cart_itemid', $product_id)->first();
    $qty = isset($qty) && $qty != 0 ? $qty : 1;
    if ($isExist) {
      $cart = Cart::find($isExist->cart_id);
      $cart->cart_qty = ($qty);
      $cart->cart_vattotal = ($qty) * $vat_price;
      $cart->cart_total = ($qty) * $product_price;
      $cart->save();
    } else {
      $cart = new Cart();
      $cart->cart_custid = $id;
      $cart->cart_itemid = $product_id;
      $cart->cart_qty = $qty;
      $cart->cart_vattotal = $qty * $vat_price;
      $cart->cart_total =  $qty * $product_price;
      $cart->cart_itemprice = $product_price;
      $cart->cart_vatprice = $vat_price;
      $cart->save();
    }

    $message = ($language == 'nl') ?  'Product toegevoegd in winkelwagen'  : 'Product Added In Cart';
    return response()->json(['status' => true, 'message' => $message]);
  }
  public function addToCartAfterLogin(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $cart_data = json_decode(stripslashes($request->input('cart_data')));
    $language = $request->input('language');

    foreach ($cart_data as $key => $value) {
      $product_id = $value->product_id;
      $qty = $value->qty;
      $Detail = Product::find($product_id);
      if ($Detail) {
        $product_price = $Detail['product_price'];
        $vat_price = $Detail['vat_price'];
        $isExist = Cart::where('cart_custid', $id)->where('cart_itemid', $product_id)->first();
        // dd($isExist);
        if ($isExist) {
          $newQty = $isExist->cart_qty;
          $cart = Cart::find($isExist->cart_id);
          $cart->cart_qty = ($newQty) + $qty;
          $cart->cart_vattotal = ($newQty + $qty) * $vat_price;
          $cart->cart_total = ($newQty + $qty) * $product_price;
          $cart->save();
        } else {
          $cart = new Cart();
          $cart->cart_custid = $id;
          $cart->cart_itemid = $product_id;
          $cart->cart_qty = $qty;
          $cart->cart_itemprice = $product_price;
          $cart->cart_total =  $qty * $product_price;
          $cart->cart_vatprice = $vat_price;
          $cart->cart_vattotal = $qty * $vat_price;
          $cart->save();
        }
      }
    }
    $message = ($language == 'nl') ?  'Product toegevoegd in winkelwagen'  : 'Product Added In Cart';
    return response()->json(['status' => true, 'message' => $message]);
  }
}
