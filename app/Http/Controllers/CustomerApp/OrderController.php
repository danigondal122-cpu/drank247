<?php

namespace App\Http\Controllers\CustomerApp;

use App\Http\Controllers\Base\BaseController;

use App\Mail\PlaceOrder;
use App\Mail\frontendCustomerCredential;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\UsedPromoCode;
use App\Models\OrderPayment;
use App\Models\Cart as CartModel;
use App\Models\CustomerAddress;
use App\Models\Pool;
use App\Models\Franchise;
use App\Models\Product;
use Bluem\BluemPHP\Bluem;
use Endroid\QrCode\QrCode;
use Bluem\BluemPHP\Integration;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use stdClass;

class OrderController extends BaseController
{
  public function orderList(Request $request)
  {
    $id = $request->input('id');
    $token = $request->input('token');
    $page_no = $request->input('page_no');
    $start_from = ($page_no - 1) * 20;
    $per_page = 20;
    $search = $request->input('search');
    // $start_date=$request->input('start_date');
    // $end_date=$request->input('end_date');
    $query = Order::select('order_id', 'order_final_with_discount as order_amount', 'order_status', 'os_name as order_status', 'address as customer_address')
      ->join('order_status', 'order_status.os_id', 'orders.order_status')
      ->join('address', 'address.address_id', 'orders.order_address_id')
      ->where('order_customerid', $id)
      ->orderBy('orders.created_at', 'desc')
      ->where('order_status', '!=', '0')
      ->whereNull('orders.deleted_at');

    $rawQuery = '';
    $rawQueryDate = '';

    $column_search = ['order_id']; //set column field database for datatable searchable
    //Search
    if ($request->input('search') && $request->input('search') != '') {

      $search = $request->input('search');
      $i = 0;
      foreach ($column_search as $key => $value) {
        if ($i === 0) // first loop
        {
          $rawQuery .= '('; // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
          $rawQuery .= $value . ' LIKE "%' . $search . '%"';
        } else {
          $rawQuery .= ' OR ' . $value . ' LIKE "%' . $search . '%"';
        }
        if (count($column_search) - 1 == $i) {
          //last loop
          $rawQuery .= ')'; //close bracket
        }
        $i++;
      }
      $query = $query->whereRaw($rawQuery);
    }
    // if($request->input('start_date') && $request->input('start_date')!='' &&  $request->input('end_date') &&  $request->input('end_date')!=''){
    // $rawQueryDate .= 'DATE(created_at) between "' . $start_date . '" and "' . $end_date . '"';
    // $query = $query->whereRaw($rawQueryDate);
    // }

    $data = $query->skip($start_from)->limit($per_page)->get();

    $i = 0;


    return response()
      ->json([
        'status' => true,
        'data' => $data,
      ]);
  }

  public function orderDetail(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $order_id = $request->input('order_id');

    $data['order'] = Order::select('order_id', 'order_price', 'order_delivery_charge', 'order_discount', 'order_final_with_discount as order_amount', 'order_status', 'os_name as order_status', 'address as customer_address')
      ->join('order_status', 'order_status.os_id', 'orders.order_status')
      ->join('address', 'address.address_id', 'orders.order_address_id')
      ->where('order_id', $order_id)->first();

    $data['order']['qr_code'] = asset('uploads/qrcode/qrcode' . $order_id . '.png');
    $data['orderdetail'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $order_id)
      ->get(['od_qty as product_qty', 'od_vattotal as product_amount', 'product_name', 'image']);
    foreach ($data['orderdetail'] as $key => $value) {
      $data['orderdetail'][$key]['image'] = $value['image'] != "" ? asset('uploads/product/thumb') . '/' . $value['image'] : asset('img/247-Drank-Logo.png');
    }
    return response()->json(['status' => true, 'orderDetail' => $data['order'], 'orderItems' => $data['orderdetail']]);
  }

  public function makeOrderPayment($order_id, $manualCall = false)
  {

    $bluem_config = new stdClass();
    // Fill in prod, test or acc for production, test or acceptance environment.
    $bluem_config->environment = env('BLUEM_ENVIRONMENT');

    // The sender ID, issued by BlueM. Starts with an S, followed by a number.
    $bluem_config->senderID = env('BLUEM_SENDERID');

    // The access token to communicate with BlueM, for the test environment.
    $bluem_config->test_accessToken = env('BLUEM_TEST_ACCESSTOKEN');

    // The access token to communicate with BlueM, for the production environment.
    $bluem_config->production_accessToken = env('BLUEM_PRODUCTION_ACCESSTOKEN');

    // the merchant ID, to be found on the contract you have with the bank for receiving direct debit mandates.
    $bluem_config->merchantID = env('BLUEM_MERCHANTID');

    // What's your BrandID? Set at BlueM
    $bluem_config->brandID = '247DrankPayment';

    $bluem_config->merchantReturnURLBase = env('MERCHANT_RETURN_URL_BASE');

    $bluem_object = new Bluem($bluem_config);
    $order = Order::where('order_uuid', $order_id)->first();

    /** Payment */
    $description = "Order";
    $amount = $order->order_final_with_discount;
    $currency = "EUR"; // if set to null, will default to EUR
    $debtorReference = 512;

    $dueDateTime = null; // set it automatically to two weeks in advance.
    // Or, to create and perform a request together in shorthand:


    $payment_response = $bluem_object->Payment(
      $description,
      $debtorReference,
      $amount,
      $dueDateTime,
      $currency
    );

    $payment_response = $payment_response->PaymentTransactionResponse;

    $orderPayment = OrderPayment::where('identity_entrance_code', $order->order_payment)->first();
    $orderPayment->iban_entrance_code = $payment_response[0]['entranceCode'];
    $orderPayment->iban_transaction_id = $payment_response->TransactionID;
    $orderPayment->iban_transaction_url = $payment_response->TransactionURL;
    $orderPayment->iban_transaction_short_url = $payment_response->ShortTransactionURL;
    $orderPayment->save();
    // echo $payment_response->TransactionURL;
    if ($manualCall) {
      return $payment_response->TransactionURL;
    } else {
      return redirect($payment_response->TransactionURL);
    }
  }

  public function orderStatus(Request $request)
  {
    $orderId = $request->get('orderId');
    // $order = Order::find($orderId);
    $api_url = env('CMTEST_API_URL');
    $merchant_key = env('MERCHANT_KEY');
    $orderPayment = OrderPayment::where('order_id', $orderId)->first();
    $order = Order::find($orderId);
    if ($order->payment_method == 'Cash' || $order->payment_method == 'Pin at Door') {
      $order = Order::find($orderId);
      // $order->order_status = 9;
      // $order->save();
      $orderPayment->status_code = 'Pending';
      $orderPayment->save();
      $data['status'] = 'Pending';
      $data['message'] = 'Your Payment is in Pending';
      return response()->json(['status' => true, 'type' => $data['status'], 'message' => $data['message']]);
    } else if ($order->payment_method == 'BitPay') {

      $bitpayUrl = env('BITPAY_TESTAPI_URL');
      $bitpay_token = env('BITPAY_TOKRN');
      $data = "token=" . env('BITPAY_TOKRN');
      $id = $orderPayment->paymentid;

      $headerData =  array(
        'x-accept-version: 2.0.0',
        'Content-Type: application/x-www-form-urlencoded'
      );

      $payment_response = $this->callCurlApi($bitpayUrl . '/invoices/' . $id, $data, 'GET', $headerData);
      $result = json_decode($payment_response, true);
      $paymentStatus = $result['data']['status'];

      switch ($paymentStatus) {
        case 'paid':
        case 'confirmed':
          $orderPayment->payment_status = 1;
          $orderPayment->status_code = $paymentStatus;
          $orderPayment->save();

          $order = Order::find($orderId);
          $order->order_status = 9;
          $order->order_payment_status = 'YES';
          $order->save();


          $detail = Order::leftJoin('customers', function ($join) {
            $join->on('customers.customer_id', '=', 'orders.order_customerid');
          })->where('order_id', $orderId)->first();

          $customer_id = $detail['order_customerid'];
          $customer_email = $detail['customer_email'];
          $customer_name = $detail['customer_name'];

          CartModel::where('cart_custid', $detail['order_customerid'])->whereNull('deleted_at')->delete();
          ## promo code count
          if ($detail['order_promocode'] != "0" && $detail['order_discount'] != 0.00) {
            $custom_used = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id', $detail['order_promocode'])->get()->count();
            if ($custom_used) {
              $custuser = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->first();
              UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->update(['used_count' => $custuser['used_count'] + 1]);
            } else {
              $newusedcode = new UsedPromoCode();
              $newusedcode->pcode_id =  $detail['order_promocode'];
              $newusedcode->c_id = auth('customer')->user()->customer_id;
              $newusedcode->used_count = 1;
              $newusedcode->save();
            }
          }

          ## Send Email##

          $maildata = [];
          $maildata['order_id'] = $orderPayment->order_id;
          $maildata['name'] = $customer_name;
          $maildata['scan'] = 'qrcode' . $orderPayment->order_id . '.png';
          $maildata['order'] = Order::leftJoin('customers', function ($join) {
            $join->on('customers.customer_id', '=', 'orders.order_customerid');
          })->leftJoin('address', function ($join) {
            $join->on('address.address_id', '=', 'orders.order_address_id');
          })->find($orderPayment->order_id);

          $maildata['orderdetail'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $orderPayment->order_id)
            ->get(['od_qty', 'od_vattotal', 'product_name', 'image']);
          foreach ($maildata['orderdetail'] as $key => $value) {
            if ($value['image'] != "") {
              $maildata['orderdetail'][$key]['image'] = asset('uploads/product/thumb') . '/' . $value['image'];
            } else {
              $maildata['orderdetail'][$key]['image'] = asset('img/logo.png');
            }
          }
          Mail::to($customer_email)
            ->send(new PlaceOrder($maildata));

          $this->OrderAssignment($orderId);


          $data['status'] = 'Success';
          $data['message'] = 'Your Amount has been successfully paid';
          return response()->json(['status' => true, 'type' => $data['status'], 'message' => $data['message']]);

          // return redirect('/');

          break;

        default:
          $data['status'] = 'Error';
          $data['message'] = 'Something went wrong !!';
          // unexpected status returned, show an error
          return response()->json(['status' => true, 'type' => $data['status'], 'message' => $data['message']]);
          break;
      }
    } else {
      $order_key = $orderPayment->order_key;
      // echo '<pre>'; print_r($orderPayment->order_key);die;
      $reponse = $this->callCurlApi($api_url . $merchant_key . '/orders/' . $order_key, '', 'GET');
      $reponse = json_decode($reponse, true);
      if (isset($reponse['payments'])) {
        $paymentData = $reponse['payments'][0];
        // print_r($paymentData);die;
        $orderPayment->paymentid = $paymentData['id'];
        // $orderPayment->payment_method = $paymentData['method'];
        $orderPayment->save();

        $order->payment_method = $paymentData['method'];
        $order->save();

        $paymentStatus = $paymentData['authorization']['state'];
        switch ($paymentStatus) {
          case 'AUTHORIZED':
          case 'CAPTURED':
          case 'NEW':

            if ($paymentStatus == 'AUTHORIZED' && $paymentData['authorization']['confidence'] == 'ACQUIRER_APPROVED') {
              $orderPayment->payment_status = 1;
              $orderPayment->status_code = $paymentStatus;
              $orderPayment->save();

              $order = Order::find($orderId);
              $order->order_status = 9;
              $order->order_payment_status = 'YES';
              $order->save();

              $detail = Order::leftJoin('customers', function ($join) {
                $join->on('customers.customer_id', '=', 'orders.order_customerid');
              })->where('order_id', $orderId)->first();

              $customer_id = $detail['order_customerid'];
              $customer_email = $detail['customer_email'];
              $customer_name = $detail['customer_name'];

              CartModel::where('cart_custid', $detail['order_customerid'])->whereNull('deleted_at')->delete();
              ## promo code count
              if ($detail['order_promocode'] != "0" && $detail['order_discount'] != 0.00) {
                $custom_used = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id', $detail['order_promocode'])->get()->count();
                if ($custom_used) {
                  $custuser = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->first();
                  UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->update(['used_count' => $custuser['used_count'] + 1]);
                } else {
                  $newusedcode = new UsedPromoCode();
                  $newusedcode->pcode_id =  $detail['order_promocode'];
                  $newusedcode->c_id = auth('customer')->user()->customer_id;
                  $newusedcode->used_count = 1;
                  $newusedcode->save();
                }
              }

              ## Send Email##

              $maildata = [];
              $maildata['order_id'] = $orderPayment->order_id;
              $maildata['name'] = $customer_name;
              $maildata['scan'] = 'qrcode' . $orderPayment->order_id . '.png';
              $maildata['order'] = Order::leftJoin('customers', function ($join) {
                $join->on('customers.customer_id', '=', 'orders.order_customerid');
              })->leftJoin('address', function ($join) {
                $join->on('address.address_id', '=', 'orders.order_address_id');
              })->find($orderPayment->order_id);

              $maildata['orderdetail'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $orderPayment->order_id)
                ->get(['od_qty', 'od_vattotal', 'product_name', 'image']);
              foreach ($maildata['orderdetail'] as $key => $value) {
                if ($value['image'] != "") {
                  $maildata['orderdetail'][$key]['image'] = asset('uploads/product/thumb') . '/' . $value['image'];
                } else {
                  $maildata['orderdetail'][$key]['image'] = asset('img/logo.png');
                }
              }
              Mail::to($customer_email)
                ->send(new PlaceOrder($maildata));

              $this->OrderAssignment($orderId);

              $data['status'] = 'Success';
              $data['message'] = 'Your Amount has been successfully paid';
              return response()->json(['status' => true, 'type' => $data['status'], 'message' => $data['message']]);

              // return redirect('/');
            }

            break;

          case 'CANCELED':
            $order = Order::find($orderId);
            $order->order_status = 11;
            $order->save();

            $data['status'] = 'Cancelled';
            $data['message'] = 'Your order Cancelled !!';
            return response()->json(['status' => true, 'type' => $data['status'], 'message' => $data['message']]);

            break;

          default:
            $data['status'] = 'Error';
            $data['message'] = 'Something went wrong !!';
            // unexpected status returned, show an error
            return response()->json(['status' => true, 'type' => $data['status'], 'message' => $data['message']]);
            break;
        }
      }
    }
  }


  public function callCurlApi($url, $data, $method, $headerData = '')
  {
    //  print_r($data);die;
    if ($headerData == '') {
      $headerData = array(
        'Content-Type:application/json',
        'Authorization:Basic ' . env('CM_AUTHORIZATION_TOKEN'),
        'Cookie:BISCUIT=chocolatechip|YepOl'
      );
      $data = json_encode($data);
    }


    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => $headerData,
    ));

    $response = curl_exec($curl);
    $error_msg  = '';
    if (curl_errno($curl)) {
      $error_msg = curl_error($curl);
    }
    if (isset($error_msg)) {
      echo $error_msg;
    }
    curl_close($curl);
    return $response;
  }

  public function getdeliverycharge(Request $request)
  {
    $data = [];
    $data = [
      'post_code' => $request->input('post_code'),
      'final_amount' => $request->input('final_amount'),
    ];

    $result = $this->setDeliverycharge($data);

    $result['final_amount'] = number_format(($request->final_amount - $request->delivery_charge + $result['delivery_charge']), 2);

    return response()->json(['status' => true, 'data' => $result]);
  }

  public function guestCheckout(Request $request)
  {

    $email = $request->input('email');
    $device = $request->input('device');
    $name = $request->input('name');
    $postcode = $request->input('post_code');
    $house_no = $request->input('house_no');
    $products = $request->input('products');
    // $withDiscount_FinalAmount = $request->input('withDiscount_FinalAmount');
    $promo_code = $request->input('promo_code');
    $final_amount = $request->input('final_amount');

    $products = json_decode($products, true);

    if ($name == '') {
      return response()->json(['status' => false, 'type' => 'Notvalid', 'message' => 'Please add customer Name']);
    }
    if ($email == '') {
      return response()->json(['status' => false, 'type' => 'Notvalid', 'message' => 'Please add customer Email']);
    }

    if ($postcode == '' || $house_no == '') {
      return response()->json(['status' => false, 'type' => 'Notvalid', 'message' => 'Please add Your Address']);
    }

    $rules = [
      'email' => 'email|unique:customers,customer_email',
    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()
        ->json([
          'status' => false,
          'message' => 'Email is already used.',
        ]);
    }

    // new

    $assign_pool = Franchise::select('franchise_pool')->where('fs_on_off', 'online')->whereNull('deleted_at')->get();

    $ass_pools = [];
    foreach ($assign_pool as $key => $value) {
      $value = explode(',', $value['franchise_pool']);
      $ass_pools[] = $value;
    }

    $total_ass = call_user_func_array("array_merge", $ass_pools);

    $pool = Pool::whereIN('pool_id', $total_ass)->whereNull('deleted_at')->get();


    $array = [];

    foreach ($pool as $value) {
      $code = preg_replace('/[^0-9.]+/', '', $postcode);
      if ($code >= $value->from_postcode && $code <= $value->to_postcode) {
        $array[] = $value['pool_id'];
      }
    }

    $count = count($array);

    if ($count == 0) {
      return response()
        ->json([
          'status' => false,
          'type' => 'invalidPostcode',
          'message' => 'we do not provie our service on your postcode area. Please call customer service for more details.',
        ]);
    } else {

      $pool_start = Pool::where('pool_id', $array[0])->whereNull('deleted_at')->first();
      $start_from = $pool_start['delivery_startfrom'];
      // if ($promo_code != "") {
      //   $com_amount = $withDiscount_FinalAmount;
      // } else {
      $com_amount = $final_amount;
      // }

      if ($com_amount < $start_from) {
        return response()->json(['status' => false, 'type' => 'InvalidAmount', 'message' => 'Minimum order amount is € ' . $start_from]);
      }
    }

    // new end
    $customer_hash = time();
    $pass_gen = Str::random(8);
    $newRegister = new Customer();
    $newRegister->customer_name = $name;
    $newRegister->customer_email = $email;
    $newRegister->login_type = 'NORMAL';
    $newRegister->customer_from = '2';
    $newRegister->customer_device = $device;
    $newRegister->customer_hash = $customer_hash;
    $newRegister->password = Hash::make($pass_gen);

    $newRegister->save();
    $insertedId = $newRegister->customer_id;

    if ($newRegister) {
      $mail_data = [
        'name' => $name,
        'email' => $email,
        'password' => $pass_gen,
      ];
      Mail::to($email)
        ->send(new frontendCustomerCredential($mail_data));

      foreach ($products as $key => $row) {
        $product_id = $row['product_id'];
        $qty = $row['qty'];

        $productdetails = Product::find($product_id);

        $product_price = $productdetails['product_price'];
        $vat_price = $productdetails['vat_price'];

        $cart = new Cart();
        $cart->cart_custid = $insertedId;
        $cart->cart_itemid = $product_id;
        $cart->cart_qty = $qty;
        $cart->cart_itemprice = $product_price;
        $cart->cart_total =  $qty * $product_price;
        $cart->cart_vatprice = $vat_price;
        $cart->cart_vattotal = $qty * $vat_price;
        $cart->save();
      }
      $detail = Customer::where('customer_id', $newRegister->customer_id)->whereNull('deleted_at')->first(['customer_id', 'customer_name', 'customer_email', 'customer_type', 'customer_phone', 'password', 'profile', 'customer_address', 'customer_devicetoken', 'customer_device', 'customer_hash']);
      $detail['customer_address'] = $detail['customer_address'] == null ? "" : $detail['customer_address'];
      // if (Auth::guard('customer')->attempt(['customer_email' => $email, 'password' => $pass_gen])) {
      //     if (Cart::content()->count()) {
      //         $this->addToCart(Cart::content());
      //     }
      // }


      $is_update = false;
      $pool = Pool::whereNull('deleted_at')->get();
      $array = [];

      foreach ($pool as $value) {
        $code = preg_replace('/[^0-9.]+/', '', $postcode);
        $houseno = preg_replace('/[^0-9.]+/', '', $house_no);
        if ($code >= $value->from_postcode &&  $code <= $value->to_postcode) {
          $array[] = $value['pool_id'];
        }
      }
      if (count($array) > 0) {
        $addressdata = $this->getAddressDetails(trim(str_replace(' ', '', $postcode)), $house_no);
        if (isset($addressdata['city']) && isset($addressdata['street']) && trim($addressdata['city']) != "" && trim($addressdata['city']) != "") {
          $fulladdress = $house_no . ', ' . $addressdata['street'] . ', ' . $addressdata['city'] . ', ' . $addressdata['province'];
          //check if first address
          $checkfirst = CustomerAddress::where("customer_id", $insertedId)->get()->count();


          $customer = new CustomerAddress();
          $customer->default = ($checkfirst == 0) ? '1' : '0';
          $msg = 'Address Added successfully';

          $customer->customer_id = $insertedId;
          $customer->address = $fulladdress;
          $customer->post_code = $addressdata['postcode'];
          $customer->latitude = $addressdata["geo"]["lat"];
          $customer->longitude = $addressdata["geo"]["lon"];
          $customer->house_no = $addressdata['number'];
          $customer->save();

          $detail['customer_address'] = $customer->address_id;

          $total_count = CustomerAddress::where("customer_id", $insertedId)->get()->count();
          return response()
            ->json([
              'status' => true,
              'data' => $detail
            ]);
        } else {
          return response()
            ->json([
              'status' => false,
              'type' => 'InvalidAddress',
              'msg' => "We couldn't fetch your address, please try again with correct details or enter it manually",
            ]);
        }
      } else {

        return response()
          ->json([
            'status' => false,
            'type' => 'NotValid',
            'msg' => 'we do not provie our service on your postcode area. Please call customer service for more details.',
          ]);
      }
    }
  }

  public function checkOut(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $address_id = $request->input('address_id');
    $delivery_charge = $request->input('delivery_charge');
    $final_amount = $request->input('final_amount');
    $discount = $request->input('discount');
    $withdiscount_finalamount = $request->input('withdiscount_finalamount');
    $promo_code = $request->input('promo_code');
    $language = $request->input('language');
    $note = isset($request->note) ?  $request->note : '';
    if (isset($request->contact_no) && $request->contact_no != "") {
      Customer::where('customer_id', $id)->update(['customer_contact_no' => $request->contact_no]);
    }

    $getaddress =  CustomerAddress::where('customer_id', $id)->where('default', '1')->first();
    // if ($address_id == "" || $address_id == null || $address_id == 0) {
    //   return response()->json(['status' => false, 'message' => 'Please add Address']);
    // } else{
    if ($getaddress != "") {

      $postcode = $getaddress['post_code'];
      $assign_pool = Franchise::select('franchise_pool')->whereNull('deleted_at')->get();

      $ass_pools = [];
      foreach ($assign_pool as $key => $value) {
        $value = explode(',', $value['franchise_pool']);
        $ass_pools[] = $value;
      }

      $total_ass = call_user_func_array("array_merge", $ass_pools);
      // $total_ass=implode(',',$total_ass);

      $pool = Pool::whereIN('pool_id', $total_ass)->whereNull('deleted_at')->get();
      $array = [];

      foreach ($pool as $value) {
        $code = preg_replace('/[^0-9.]+/', '', $postcode);
        if ($code >= $value->from_postcode && $code <= $value->to_postcode) {
          $array[] = $value['pool_id'];
        }
      }

      $count = count($array);

      if ($count == 0) {
        return response()
          ->json([
            'status' => false,
            'type' => 'invalidPostcode',
            'message' => 'we do not provide our service on your postcode area. Please call customer service for more details.',
          ]);
      } else {

        $pool_start = Pool::where('pool_id', $array[0])->whereNull('deleted_at')->first();
        $start_from = $pool_start['delivery_startfrom'];
        if ($promo_code != "") {
          $com_amount = $withdiscount_finalamount;
        } else {
          $com_amount = $final_amount;
        }

        if ($com_amount >= $start_from) {

          $orderdetail = Cart::where('cart_custid', $id)->whereNull('deleted_at')->get();
          $cart_total_price = Cart::where('cart_custid', $id)->whereNull('deleted_at')->get()->sum('cart_vattotal');
          $order = new Order();
          $order->order_uuid = Str::uuid();
          $order->order_customerid = $id;
          $order->franchise_id = "";
          $order->order_address_id = $address_id;
          $order->order_price = str_replace(',', '', number_format($cart_total_price, 2));
          $order->order_delivery_charge = str_replace(',', '', $delivery_charge);
          $order->order_finalamount = str_replace(',', '', $final_amount);
          $order->order_status = '0';
          $order->order_payment = "";
          $order->order_note = $note;
          $order->order_channel_order_id = strtoupper(Str::random(6));
          if ($request->promo_code != "" && ($discount != 0.00 || $discount != 0)) {
            $order->order_discount = str_replace(',', '', $discount);
            $order->order_final_with_discount = str_replace(',', '', $withdiscount_finalamount);
            $order->order_promocode = $promo_code;
          } else {
            $order->order_discount = 0.00;
            $order->order_final_with_discount = str_replace(',', '', $final_amount);
            $order->order_promocode = "";
          }
          $order->save();
          foreach ($orderdetail as $value) {
            $od = new OrderDetail();
            $od->od_orderid = $order->order_id;
            $od->od_productid = $value['cart_itemid'];
            $od->od_qty = $value['cart_qty'];
            $od->od_itemprice = $value['cart_itemprice'];
            $od->od_total = $value['cart_total'];
            $od->od_vatprice = $value['cart_vatprice'];
            $od->od_vattotal = $value['cart_vattotal'];
            $od->save();
          }
          ## Generate Receipt ID ##
          if ($order) {
            $token = mt_rand(1000, 9999);
            $receipt_id = $order->order_id + $token;
            Order::where('order_id', $order->order_id)->update(['order_receiptid' => $receipt_id]);
          }

          ## Generate Qr Code ##
          if ($order) {
            $builder = new Builder(
              writer: new PngWriter(),
              data: $order->order_id,
              encoding: new Encoding('UTF-8'),
              errorCorrectionLevel: ErrorCorrectionLevel::Low,
              size: 150,
              margin: 10,
            );
            $result = $builder->build();
            $result->saveToFile(public_path('uploads/qrcode/qrcode' . $order->order_id . '.png'));
          }

          // $data = [
          //   'delivery_charge' => $delivery_charge,
          //   'final_amount'  => $final_amount,
          //   'discount' => $discount,
          //   'withdiscount_finalamount' => $withdiscount_finalamount,
          //   'promo_code' => $promo_code,
          //   'orderId' => $order->order_id,
          //   'type' => 'app',
          //   'cust_id' => $id,
          //   'language' => $language
          // ];

          // Session::put('OrderData', $data);
          $redirect[] = url('paymentmethod/' . $order->order_id);
          $redirect = (object)$redirect;

          return response()->json(['status' => true,  'message' => '', 'redirect_url' => $redirect]);

          // return view('frontend.payment_method', $data);
        } else {
          return response()->json(['status' => false, 'type' => 'InvalidAmount', 'message' => 'Minimum order amount is € ' . $start_from]);
        }
      }
    } else {
      return response()->json(['status' => false, 'type' => 'NoAddress', 'message' => 'Please add your address']);
    }
  }
}
