<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\UberStore;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleClient;

class UberController extends Controller
{
  public function syncUber()
  {
    try {

      $storeIds =  $this->syncStores();

      if (count($storeIds) > 0) {
        $data = Category::with('products')->limit(3)->get()->toArray();
        $categoryIds = [];
        $requestData = [
          'items' => [],
          'categories' => [],
          'menus' => [],
          'modifier_groups' => [],
          'display_options' => [
            'disable_item_instructions' => true
          ]
        ];

        if (count($data) > 0) {
          foreach ($data as $key => $category) {
            if (count($category['products']) > 0) {
              array_push($categoryIds, $category['category_name']);
              $tempCat = [
                'entities' => [],
                'id' => $category['category_name'],
                'title' => [
                  'translations' => [
                    'en_us' => $category['category_name']
                  ]
                ]
              ];
              foreach ($category['products'] as $key => $product) {

                $tempProduct = [
                  'id' => (string)$product['product_id'],
                  'title' => [
                    'translations' => [
                      'en' => $product['product_name'],
                    ],
                  ],
                  'description' => [
                    'translations' => [
                      'en' => $product['description'],
                    ],
                  ],
                  'image_url' => $product['image'],
                  'price_info' => [
                    'price' => (float)($product['product_price'] * 100),
                    'overrides' => [],
                  ],
                  'tax_info' => (object)[],
                  'dish_info' => [
                    'classifications' => [
                      'ingredients' => NULL,
                      'additives' => NULL,
                    ],
                  ],
                  'product_info' => [
                    'product_traits' => NULL,
                    'countries_of_origin' => NULL,
                  ],
                  'bundled_items' => NULL,
                ];

                array_push($requestData['items'], $tempProduct);
                array_push($tempCat['entities'], [
                  'type' => 'ITEM',
                  'id' => (string)$product['product_id']
                ]);
              }
              array_push($requestData['categories'], $tempCat);
            }
          }
          $menus = [
            0 => [
              'service_availability' => [
                0 => [
                  'time_periods' => [
                    0 => [
                      'start_time' => '00:00',
                      'end_time' => '23:59',
                    ],
                  ],
                  'day_of_week' => 'monday',
                ],
                1 => [
                  'time_periods' => [
                    0 => [
                      'start_time' => '00:00',
                      'end_time' => '23:59',
                    ],
                  ],
                  'day_of_week' => 'tuesday',
                ],
                2 => [
                  'time_periods' => [
                    0 => [
                      'start_time' => '00:00',
                      'end_time' => '23:59',
                    ],
                  ],
                  'day_of_week' => 'wednesday',
                ],
                3 => [
                  'time_periods' => [
                    0 => [
                      'start_time' => '00:00',
                      'end_time' => '23:59',
                    ],
                  ],
                  'day_of_week' => 'thursday',
                ],
                4 => [
                  'time_periods' => [
                    0 => [
                      'start_time' => '00:00',
                      'end_time' => '23:59',
                    ],
                  ],
                  'day_of_week' => 'friday',
                ],
                5 => [
                  'time_periods' => [
                    0 => [
                      'start_time' => '00:00',
                      'end_time' => '23:59',
                    ],
                  ],
                  'day_of_week' => 'saturday',
                ],
                6 => [
                  'time_periods' => [
                    0 => [
                      'start_time' => '00:00',
                      'end_time' => '23:59',
                    ],
                  ],
                  'day_of_week' => 'sunday',
                ],
              ],
              'category_ids' => $categoryIds,
              'id' => 'All-day',
              'title' => [
                'translations' => [
                  'en_us' => 'All day',
                ],
              ],
            ],
          ];
          $requestData['menus'] = $menus;
        }

        // Get eats.store token

        $ToeknReqData = [
          'client_id' => env('UBER_EATS_CLIENT_ID'),
          'client_secret' => env('UBER_EATS_CLIENT_SECRET'),
          'grant_type' => 'client_credentials',
          'scope' => 'eats.store',
        ];

        $client = new GuzzleClient([
          'form_params' => $ToeknReqData,
        ]);
        $url = "https://login.uber.com/oauth/v2/token";
        $tokenResponse = $client->request('POST', $url);

        // Error response if it has error on getting auth token
        if ($tokenResponse->getStatusCode() != 200) {
          return response()->json([
            'message' => 'Please try again later.',
            'status' => false,
            'data' => []
          ], 500);
        }

        $token = json_decode($tokenResponse->getBody()->getContents())->access_token;


        // Sync uber eats store's menu
        $headers = [
          'Content-Type' => 'application/json',
          'authorization' => 'Bearer ' . $token,
        ];
        foreach ($storeIds as $key => $storeId) {

          $client = new GuzzleClient([
            'body' => json_encode($requestData),
            'headers' => $headers,
          ]);
          $url = "https://api.uber.com/v2/eats/stores/" . $storeId . "/menus";

          $client->request('PUT', $url);
        }

        return response()->json([
          'message' => '',
          'status' => true,
          'data' => []
        ], 200);
      }
      return response()->json([
        'message' => 'No stores found.',
        'status' => true,
        'data' => []
      ], 200);
    } catch (\Throwable $th) {
      echo $th;
      return response()->json([
        'message' => 'Please try again later.',
        'status' => false,
        'data' => []
      ], 500);
    }
  }

  public function syncStores()
  {
    try {
      // Get eats.store token
      $ToeknReqData = [
        'client_id' => env('UBER_EATS_CLIENT_ID'),
        'client_secret' => env('UBER_EATS_CLIENT_SECRET'),
        'grant_type' => 'client_credentials',
        'scope' => 'eats.store',
      ];

      $client = new GuzzleClient([
        'form_params' => $ToeknReqData,
      ]);
      $url = "https://login.uber.com/oauth/v2/token";
      $tokenResponse = $client->request('POST', $url);

      // Error response if it has error on getting auth token
      if ($tokenResponse->getStatusCode() != 200) {
        return response()->json([
          'message' => 'Please try again later.',
          'status' => false,
          'data' => []
        ], 500);
      }

      $token = json_decode($tokenResponse->getBody()->getContents())->access_token;

      // Sync uber eats stores
      $headers = [
        'Content-Type' => 'application/json',
        'authorization' => 'Bearer ' . $token,
      ];

      $client = new GuzzleClient([
        'headers' => $headers,
      ]);
      $url = "https://api.uber.com/v1/eats/stores";
      $response = $client->request('GET', $url);

      // Error response if it has error on getting stores
      if ($response->getStatusCode() != 200) {
        return response()->json([
          'message' => 'Please try again later.',
          'status' => false,
          'data' => []
        ], 500);
      }

      $response = json_decode($response->getBody()->getContents())->stores;

      if (count($response) > 0) {
        foreach ($response as $key => $store) {
          UberStore::updateOrCreate([
            'store_id' => $store->store_id,
          ], [
            'name' => $store->name,
            'location' => json_encode($store->location),
            'status' => $store->status,
            'contact_emails' => count($store->contact_emails) > 0 ? implode(',', $store->contact_emails) : '',
          ]);
        }
      }
      return UberStore::get()->pluck('store_id')->toArray();
    } catch (\Throwable $th) {
      echo $th;
      return response()->json([
        'message' => 'Please try again later.',
        'status' => false,
        'data' => []
      ], 500);
    }
  }

  public function uberWebhook(Request $request)
  {
    try {
      DB::table('uber')->insert([
        ['data' => json_encode($request->all())]
      ]);
      $hookResponse = $request->all();
      $orderId = $hookResponse['meta']['resource_id'];

      $ToeknReqData = [
        'client_id' => env('UBER_EATS_CLIENT_ID'),
        'client_secret' => env('UBER_EATS_CLIENT_SECRET'),
        'grant_type' => 'client_credentials',
        'scope' => 'eats.order',
      ];

      $client = new GuzzleClient([
        'form_params' => $ToeknReqData,
      ]);
      $url = "https://login.uber.com/oauth/v2/token";
      $tokenResponse = $client->request('POST', $url);

      // Error response if it has error on getting auth token
      if ($tokenResponse->getStatusCode() != 200) {
        return response()->json([
          'message' => 'Please try again later.',
          'status' => false,
          'data' => []
        ], 500);
      }

      $eatsOrderToken = json_decode($tokenResponse->getBody()->getContents())->access_token;

      // Get order details
      $headers = [
        'Content-Type' => 'application/json',
        'authorization' => 'Bearer ' . $eatsOrderToken,
      ];

      $client = new GuzzleClient([
        'headers' => $headers,
      ]);
      $url = "https://api.uber.com/v2/eats/order/" . $orderId;
      $response = $client->request('GET', $url);

      // Error response if it has error on getting stores
      if ($response->getStatusCode() != 200) {
        return response()->json([
          'message' => 'Please try again later.',
          'status' => false,
          'data' => []
        ], 500);
      }

      $orderDetails = json_decode($response->getBody()->getContents());
      DB::table('uber')->insert([
        ['data' => json_encode($orderDetails)]
      ]);

      switch ($hookResponse['event_type']) {
        case 'orders.notification':
          // Add Customer
          if (isset($orderDetails['eater'])) {
            $checkcustomer = '';
            $customer_detail = $orderDetails['eater'];
            if ($customer_detail['phone']) {
              $checkcustomer = Customer::where("customer_contact_no", $customer_detail['phone'])->first();
            }
            if ($checkcustomer) {
              $Cus_id = $checkcustomer['customer_id'];
            } else {
              $customer = new Customer();
              $customer->customer_name = $customer_detail['first_name'] . ' ' . $customer_detail['last_name'];
              $customer->customer_email = '';
              $customer->customer_type = "0";
              $customer->customer_from = "3";
              $customer->customer_contact_no = $customer_detail['phone'] ? $customer_detail['phone'] : '';
              $customer->save();
              $Cus_id = $customer->customer_id;
            }

            // Add Customer Address
            if (isset($orderDetails['delivery'])) {

              $address_detail = $orderDetails['delivery']['location'];
              if ($address_detail['type'] == 'STREET_ADDRESS') {

                if ($Cus_id != "") {
                  $lat = $address_detail['latitude'];
                  $long = $address_detail['longitude'];

                  $address = new CustomerAddress();
                  $fulladdress = $address_detail['street_address'];
                  $address->address_custid = $Cus_id;
                  $address->address = $fulladdress;
                  $address->post_code = '';
                  $address->address_latitude = $lat;
                  $address->address_longitude = $long;
                  $address->house_no = '';
                  $address->save();
                }
              } elseif ($address_detail['type'] == 'GOOGLE_PLACE') {
                // TO DO Get lat long from place_id
              }
              Customer::where('customer_id', $Cus_id)->update(['customer_address' => $address->address_id]);

              /// Add Order///
              $paymentCharges = $orderDetails['payment']['charges'];
              $order = new Order();
              $order->order_uuid = Str::uuid();
              $order->order_customerid = $Cus_id;
              $order->franchise_id = "";
              $order->order_address_id = $address->address_id;
              $order->order_price = isset($paymentCharges['sub_total']['amount']) ? $paymentCharges['sub_total']['amount'] / 100 : 0;
              $order->order_delivery_charge = isset($paymentCharges['delivery_fee']['amount']) ? $paymentCharges['delivery_fee']['amount'] / 100 : 0;
              $order->order_servicecharge = isset($paymentCharges['total_fee']['amount']) ? $paymentCharges['total_fee']['amount'] / 100 : 0;
              $order->order_finalamount = isset($paymentCharges['total']['amount']) ? $paymentCharges['total']['amount'] / 100 : 0;
              $order->order_discount = isset($paymentCharges['total_promo_applied']['amount']) ? ($paymentCharges['total']['amount'] - $paymentCharges['total_promo_applied']['amount']) / 100 : 0;
              $order->order_final_with_discount = isset($paymentCharges['total_promo_applied']['amount']) ? $paymentCharges['total_promo_applied']['amount'] / 100 : 0;
              $order->order_status = '1';
              $order->order_payment = "";
              $order->order_note = $orderDetails['delivery']['notes'];
              $order->order_payment_status = isset($paymentCharges['cash_amount_due']['amount']) && $paymentCharges['cash_amount_due']['amount'] > 0 ? 'NO' : 'YES';
              $order->order_deliverytime = $paymentCharges['deliveryTime'];
              $order->created_at = $orderDetails['placed_at'];
              $order->updated_at = $orderDetails['placed_at'];
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
                $postData = $orderDetails['cart'];
                foreach ($postData['items'] as $pkey => $value) {

                  $od = new OrderDetail();

                  $order_items = $postData['items'][$pkey];
                  $detail = Product::where('product_id', $order_items['id'])->first();
                  $od->od_orderid = $order->order_id;
                  $od->od_productid = $detail['product_id'];
                  $od->od_qty = $order_items['quantity'];
                  $od->od_itemprice = $order_items['price']['total_price'] / 100;
                  $od->od_total = ($order_items['price']['total_price'] / 100) * $order_items['quantity'];
                  $od->od_vatprice = $order_items['price']['total_price'] / 100;
                  $od->od_vattotal = $order_items['price']['total_price'] / 100;
                  $od->save();
                  $total_amount += ($order_items['quantity'] * ($order_items['price']['total_price'] / 100));
                }
              }

              $order_update = Order::find($order->order_id);
              $order_update->order_price = $total_amount;
              $order_update->order_finalamount = $total_amount + (isset($paymentCharges['delivery_fee']['amount']) ? $paymentCharges['delivery_fee']['amount'] / 100 : 0) + (isset($paymentCharges['total_fee']['amount']) ? $paymentCharges['total_fee']['amount'] / 100 : 0);
              $order_update->order_final_with_discount = $total_amount + (isset($paymentCharges['delivery_fee']['amount']) ? $paymentCharges['delivery_fee']['amount'] / 100 : 0) + (isset($paymentCharges['total_fee']['amount']) ? $paymentCharges['total_fee']['amount'] / 100 : 0) + (isset($paymentCharges['total_promo_applied']['amount']) ? ($paymentCharges['total']['amount'] - $paymentCharges['total_promo_applied']['amount']) / 100 : 0);

              $order_update->save();
            }
          }

          break;

        case 'orders.cancel':

          break;
      }
    } catch (\Throwable $th) {
      echo $th;
      DB::table('uber')->insert([
        ['data' => $th]
      ]);
      return response()->json([
        'message' => 'Please try again later.',
        'status' => false,
        'data' => []
      ], 500);
    }
  }
}
