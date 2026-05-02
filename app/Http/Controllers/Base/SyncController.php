<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Base\BaseController;

use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Allergen;
use App\Models\AssignAllergen;
use App\Models\CustomerAddress;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SyncController extends BaseController
{
  public function syncProduct()
  {

    $DELIVERECT_TOKEN_URL = env('DELIVERECT_TOKEN_URL');
    $DELIVERECT_CLIENT_ID = env('DELIVERECT_CLIENT_ID');
    $DELIVERECT_CLIENT_SECRET = env('DELIVERECT_CLIENT_SECRET');
    $DELIVERECT_ACCOUNT_ID = env('DELIVERECT_ACCOUNT_ID');
    $DELIVERECT_LOCATION_ID = env('DELIVERECT_LOCATION_ID');
    $DELIVERECT_PRODUCT_CATEGORY_URL = env('DELIVERECT_PRODUCT_CATEGORY_URL');
    $DELIVERECT_ORDER_STATUS_URL = env('DELIVERECT_ORDER_STATUS_URL');

    $proArray = [];
    $catArray = [];
    $Productall = Product::get();
    foreach ($Productall as $key => $value) {
      // dd($value['product_id']);
      $data['allergense_array'] = AssignAllergen::where('product_id', $value['product_id'])
        ->leftJoin('allergen', function ($join) {
          $join->on('allergen.allergen_id', '=', 'assign_allergen.allergen_id');
        })
        ->pluck('deliverect_value')->toarray();

      $id = $value['product_id'];
      $cid = $value['category_id'];
      $proArray[$key]['productType'] = 1;
      $proArray[$key]['plu'] = $value['product_article_number'];
      $proArray[$key]['price'] = $value['product_price'] * 100;
      $proArray[$key]['name'] = $value['product_name'];
      $proArray[$key]['posProductId'] = "$id";
      $proArray[$key]['posCategoryIds'] = "$cid";
      $proArray[$key]['imageUrl'] = $value['image'];
      $proArray[$key]['description'] = $value['description'];
      $proArray[$key]['deliveryTax'] = $value['vat'] * 1000;
      $proArray[$key]['takeawayTax'] = $value['vat'] * 100;
      $proArray[$key]['productTags'] = $data['allergense_array'];
    }
    $categoryAll = Category::all()->toArray();
    foreach ($categoryAll as $key => $value) {
      $cid = $value['category_id'];
      $catArray[$key]['name'] = $value['category_name'];
      $catArray[$key]['posCategoryId'] = "$cid";
      $catArray[$key]['imageUrl'] = $value['image'];
    }


    // $categoryAll = Category::where('is_show','1')->where('category_id','0')->get();
    // $answers=[];
    // foreach ($categoryAll as $key => $value) {
    //   $count= Category::where('is_show','1')->where('category_id',$value['category_id'])->get();
    //   if($count->count()>0){

    //   foreach($count as $key =>$v2){
    //     $answers[]=$v2['category_id'];
    //   }

    //   }else{
    //     $answers[]=$value['category_id'];
    //   }
    // }
    // $categoryAll = Category::where('is_show','1')->whereIn('category_id', $answers)->get();
    // foreach ($categoryAll as $key => $value) {
    //   $cid = $value['category_id'];
    //   $catArray[$key]['name'] = $value['category_name'];
    //   $catArray[$key]['posCategoryId'] = "$cid";
    //   $catArray[$key]['imageUrl'] = $value['image'];
    // }


    $ExpiredTime = Session::get('Expired_time');
    $currenttime = strtotime(date('Y-m-d H:i:s'));
    $gettoken = Session::get('Access_token');

    // if($currenttime>$ExpiredTime || $gettoken==""){
    $client = new Client([
      'headers' => ['Content-Type' => 'application/json']
    ]);

    $response = $client->post(
      $DELIVERECT_TOKEN_URL,
      ['body' => json_encode(
        [
          'client_id' => $DELIVERECT_CLIENT_ID,
          'client_secret' => $DELIVERECT_CLIENT_SECRET,
          'audience' => 'https://api.deliverect.com',
          'grant_type' => 'client_credentials',

        ]
      )]
    );
    $response = json_decode($response->getBody()->getContents(), true);


    $token = $response['access_token'];
    Session::put('Expired_time', strtotime(date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +1 day'))));
    Session::put('Access_token', $token);

    // }else{
    //   $token=Session::get('Access_token');
    // }

    $client = new \GuzzleHttp\Client();
    $url   = $DELIVERECT_PRODUCT_CATEGORY_URL;
    $data   = [
      "accountId"      => $DELIVERECT_ACCOUNT_ID,
      "locationId"   => $DELIVERECT_LOCATION_ID,
      'products' => $proArray,
      'categories' => $catArray,
    ];
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $token,
    ];
    $requestAPI = $client->post($url, [
      'headers' => $headers,
      'body' => json_encode($data),

    ]);

    $responsepro = json_decode($requestAPI->getBody()->getContents(), true);

    return response()
      ->json([
        'status' => true,
        'msg' => 'Product Sync!!',
        'page' => 'admin/product/list',
        'Access_token'  => $token,

      ]);
  }
  public function syncOrder(Request $request)
  {

    $postData = $request->all();
    $customer_detail = $postData['customer'];
    $address_detail = $postData['deliveryAddress'];
    $items = $postData['items'];
    DB::table('temp')->insert([
      ['order_id' => 100, 'data' => json_encode($postData)]
    ]);
    /// Add Customer///

    $checkcustomer = Customer::where("customer_email", $customer_detail['email'])->first();
    if ($checkcustomer) {
      $Cus_id = $checkcustomer['customer_id'];
    } else {
      $customer = new Customer();
      $customer->customer_name = $customer_detail['name'];
      $customer->customer_email = $customer_detail['email'];
      $customer->customer_type = "2";
      $customer->customer_contact_no = isset($customer_detail['phoneNumber']) ? $customer_detail['phoneNumber'] : '';
      $customer->save();
      $Cus_id = $customer->customer_id;
    }

    /// Add Customer Address///
    $expl_streetNumber = explode(" ", $address_detail['street']);

    $str_no = isset($address_detail['streetNumber']) ? $address_detail['streetNumber'] : $expl_streetNumber[0];

    if ($Cus_id != "") {
      if (isset($address_detail['street']) && isset($address_detail['postalCode'])) {
        $getlatlong = $this->getAddressDetails(trim(str_replace(' ', '', $address_detail['postalCode'])), $str_no);
        if (isset($getlatlong['city']) && isset($getlatlong['street']) && trim($getlatlong['city']) != "" && trim($getlatlong['city']) != "") {
          $lat = $getlatlong["geo"]["lat"];
          $long = $getlatlong["geo"]["lon"];
        } else {
          $lat = "";
          $long = "";
        }
      } else {
        $lat = "";
        $long = "";
      }

      $address = new CustomerAddress();
      $fulladdress = $str_no . ', ' . $address_detail['street'] . ', ' . $address_detail['city'];
      $address->customer_id = $Cus_id;
      $address->address = $fulladdress;
      $address->post_code = $address_detail['postalCode'];
      $address->latitude = $lat;
      $address->longitude = $long;
      $address->house_no = $str_no;
      $address->save();
    }

    Customer::where('customer_id', $Cus_id)->update(['customer_address' => $address->address_id]);

    /// Add Order///
    $order = new Order();
    $order->order_uuid = Str::uuid();
    $order->order_deliverect_id = $postData['_id'];
    $order->order_channel_id = $postData['channelLink'];
    $order->order_channel_order_id = $postData['channelOrderDisplayId'];
    $order->order_customerid = $Cus_id;
    $order->franchise_id = "";
    $order->order_address_id = $address->address_id;
    $order->order_price = ($postData['payment']['amount'] - $postData['deliveryCost'] - $postData['serviceCharge'] - $postData['discountTotal']) / 100;
    $order->order_delivery_charge = $postData['deliveryCost'] / 100;
    $order->order_servicecharge = $postData['serviceCharge'] / 100;
    $order->order_final_amount = $postData['payment']['amount'] - $postData['discountTotal'] / 100;
    $order->order_discount = $postData['discountTotal'] / 100;
    $order->order_final_with_discount = $postData['payment']['amount'] / 100;
    $order->order_status = '1';
    $order->order_payment = "";
    $order->order_note = $postData['note'];
    $order->order_payment_status = $postData['orderIsAlreadyPaid'] == 'true';
    $order->order_deliverytime = $postData['deliveryTime'];
    $order->created_at = $postData['_created'];
    $order->updated_at = $postData['_updated'];
    $order->save();


    ## Generate Receipt ID ##

    if ($order) {
      $token = mt_rand(1000, 9999);
      $receipt_id = $order->order_id . $token;
      Order::where('order_id', $order->order_id)->update(['order_receiptid' => $receipt_id]);
    }

    /// Add Order Detail///
    $total_amount = 0;
    if ($order->save()) {
      foreach ($postData['items'] as $pkey => $value) {

        $od = new OrderDetail();

        $order_items = $postData['items'][$pkey];
        $detail = Product::where('product_article_number', $order_items['plu'])->first();
        $od->od_orderid = $order->order_id;
        $od->od_productid = $detail['product_id'];
        $od->od_qty = $order_items['quantity'];
        $od->od_itemprice = $order_items['price'] / 100;
        $od->od_total = ($order_items['price'] / 100) * $order_items['quantity'];
        $od->od_vatprice = $order_items['price'] / 100;
        $od->od_vattotal = $order_items['price'] / 100;
        $od->save();
        $total_amount += ($order_items['quantity'] * ($order_items['price'] / 100));
      }
    }

    $order_update = Order::find($order->order_id);
    $order_update->order_price = $total_amount;
    $order_update->order_final_amount = $total_amount + ($postData['deliveryCost'] / 100) + ($postData['serviceCharge'] / 100);
    $order_update->order_final_with_discount = $total_amount + ($postData['deliveryCost'] / 100) + ($postData['serviceCharge'] / 100) + ($postData['discountTotal'] / 100);

    $order_update->save();
    DB::table('temp')->insert([
      ['order_id' => $order->order_id, 'data' => json_encode($postData)]
    ]);
    $this->OrderAssignment($order->order_id);
  }
  public function deliverectOrderStatus($order_id, $receipt_id, $status)
  {

    $DELIVERECT_TOKEN_URL = env('DELIVERECT_TOKEN_URL');
    $DELIVERECT_CLIENT_ID = env('DELIVERECT_CLIENT_ID');
    $DELIVERECT_CLIENT_SECRET = env('DELIVERECT_CLIENT_SECRET');
    $DELIVERECT_ACCOUNT_ID = env('DELIVERECT_ACCOUNT_ID');
    $DELIVERECT_LOCATION_ID = env('DELIVERECT_LOCATION_ID');
    $DELIVERECT_PRODUCT_CATEGORY_URL = env('DELIVERECT_PRODUCT_CATEGORY_URL');
    $DELIVERECT_ORDER_STATUS_URL = env('DELIVERECT_ORDER_STATUS_URL');

    $status_array = [
        '2' => '10',
        '12' => '20',
        '7' => '110',
        '3' => '50',
        '4' => '60',
        '5' => '70',
        '6' => '80',
        '10' => '90',
        '8' => '110',
        '11' => '110',
        '1' => '2'
    ];
    //1 =Order Placed               1=PARSED
    //2 =Approved                   2=RECEIVED
    //12 =Accepted
    //3 =Preparing                  10=NEW
    //4 =Prepared                   20=ACCEPTED
    //5 =Ready For Pickedup         30=DUPLICATE
    //6 =Delivered                  40=PRINTED
    //7 =Rejected                   50=PREPARING
    //8 =Failed                     60=PREPARED
    //9 =Pending                    70=READY FOR PICKUP
    //10 =Finalized                 80=IN DELIVERY
    //11 =Cancelled                 90=FINALIZED
    //  95=AUTO FINALIZED
    // 100=CANCEL
    //  110=CANCELED

    $status = $status_array[$status];


    $ExpiredTime = Session::get('Expired_time');
    $currenttime = strtotime(date('Y-m-d H:i:s'));
    $gettoken = Session::get('Access_token');

    if ($currenttime > $ExpiredTime || $gettoken == "") {
      $client = new Client([
        'headers' => ['Content-Type' => 'application/json']
      ]);

      $response = $client->post(
        $DELIVERECT_TOKEN_URL,
        ['body' => json_encode(
          [
            'client_id' => $DELIVERECT_CLIENT_ID,
            'client_secret' => $DELIVERECT_CLIENT_SECRET,
            'audience' => 'https://api.deliverect.com',
            'grant_type' => 'client_credentials',

          ]
        )]
      );
      $response = json_decode($response->getBody()->getContents(), true);
      $token = $response['access_token'];
      Session::put('Expired_time', strtotime(date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +1 day'))));
      Session::put('Access_token', $token);
    } else {
      $token = Session::get('Access_token');
    }
    $date = date('yy-m-d H:i:s');

    $client = new \GuzzleHttp\Client();
    $url   = $DELIVERECT_ORDER_STATUS_URL . '/' . $order_id;
    $data   = [
      "orderId"      => $order_id,
      "status"   => (int)$status,
      'reason' => '',
      'timeStamp' => $date . '.000Z',
      // 'timeStamp' => '2019-09-05 07:44:15.000Z',
      'receiptId' => $receipt_id,
    ];

    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $token,

    ];
    $requestAPI = $client->post($url, [
      'headers' => $headers,
      'body' => json_encode($data),

    ]);

    return  $responsepro = json_decode($requestAPI->getBody()->getContents(), true);
  }
  public function syncMenu(Request $request)
  {

    $postData = $request->all();
    DB::table('temp')->insert([
      ['data' => json_encode($postData)]
    ]);
  }

  public function getAllergenceFromDeliverect()
  {
    $DELIVERECT_TOKEN_URL = env('DELIVERECT_TOKEN_URL');
    $DELIVERECT_CLIENT_ID = env('DELIVERECT_CLIENT_ID');
    $DELIVERECT_CLIENT_SECRET = env('DELIVERECT_CLIENT_SECRET');
    $DELIVERECT_ACCOUNT_ID = env('DELIVERECT_ACCOUNT_ID');
    $DELIVERECT_LOCATION_ID = env('DELIVERECT_LOCATION_ID');
    $DELIVERECT_PRODUCT_CATEGORY_URL = env('DELIVERECT_PRODUCT_CATEGORY_URL');
    $DELIVERECT_ORDER_STATUS_URL = env('DELIVERECT_ORDER_STATUS_URL');
    $DELIVERECT_GET_ALLERGENCE_URL = env('DELIVERECT_GET_ALLERGENCE_URL');

    $client = new Client([
      'headers' => ['Content-Type' => 'application/json']
    ]);

    $response = $client->post(
      $DELIVERECT_TOKEN_URL,
      ['body' => json_encode(
        [
          'client_id' => $DELIVERECT_CLIENT_ID,
          'client_secret' => $DELIVERECT_CLIENT_SECRET,
          'audience' => 'https://api.deliverect.com',
          'grant_type' => 'client_credentials',
        ]
      )]
    );
    $response = json_decode($response->getBody()->getContents(), true);

    $token = $response['access_token'];

    $client = new \GuzzleHttp\Client();
    $url   = $DELIVERECT_GET_ALLERGENCE_URL;

    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $token,

    ];
    $requestAPI = $client->get($url, [
      'headers' => $headers,
    ]);
    $responsepro = json_decode($requestAPI->getBody()->getContents(), true);

    foreach ($responsepro as $key => $value) {
      $allergen = Allergen::where('name', $value['name'])->first();
      if ($allergen) {
        $allergen->name = $value['name'];
        $allergen->deliverect_value = $value['allergenId'];
        $allergen->save();
      } else {
        $allergen = new Allergen();
        $allergen->name = $value['name'];
        $allergen->deliverect_value = $value['allergenId'];
        $allergen->save();
      }
    }
    return response()->json(['status' => true, 'msg' => 'Successfully added!!', 'page' => 'admin/allergen/list']);
  }
}
