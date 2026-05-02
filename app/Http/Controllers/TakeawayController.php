<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderDetail;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TakeawayController extends Controller
{
    public function getAddressDetails($postcode, $houseno)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $_ENV['ADDRESS_API_KEY']
        ];

        $client = new GuzzleClient([
            'headers' => $headers,
            'http_errors' => false,
            'verify' => false
        ]);
        $url = $_ENV['POST_CODE_API_URL'] . '?postcode=' . $postcode . '&number=' . $houseno;
        $response = $client->request('GET', $url);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function takeawayStoreData()
    {
        try {
            // Staging
            $store_id = 8914548;

            // Live
            // $store_id = 352711;

            $client = new GuzzleClient(['verify' => false]);
            $url = $_ENV['TAKEAWAY_ORDERS'] . $store_id;
            $response = $client->get($url, [
                'headers' => [
                    'Content-type' => 'application/json',
                    'Apikey' => $_ENV['TAKEAWAY_APIKEY']
                ],
                'auth' => [
                    $_ENV['TAKEAWAY_USERNAME'],
                    $_ENV['TAKEAWAY_PASSWORD']
                ]
            ]);

            // Error response if it has error on getting auth token
            if ($response->getStatusCode() != 200) {
                return response()->json([
                    'message' => 'Please try again later.',
                    'status' => false,
                    'data' => []
                ], 500);
            }

            $orderDetails = json_decode($response->getBody()->getContents());
            $orderDetails = $orderDetails->orders;

            foreach ($orderDetails as $orderval) {
                if ($orderval->id) {
                    $order_exist = Order::where('order_takeaway_id', $orderval->id)->first();
                    if (!$order_exist) {
                        // Add Customer
                        if ($orderval->customer) {
                            $customer_details = $orderval->customer;
                            $checkcustomer = '';

                            if ($customer_details->phoneNumber) {
                                $checkcustomer = Customer::where("customer_contact_no", $customer_details->phoneNumber)->first();
                            }

                            if ($checkcustomer) {
                                $cus_id = $checkcustomer['customer_id'];
                            } else {
                                $customer = new Customer();
                                $customer->customer_name = $customer_details->name;
                                $customer->customer_email = '';
                                $customer->customer_type = "0";
                                $customer->customer_from = "4";
                                $customer->login_type = "TAKEAWAY";
                                $customer->customer_contact_no = isset($customer_details->phoneNumber) ? $customer_details->phoneNumber : '';
                                $customer->save();
                                $cus_id = $customer->customer_id;
                            }

                            // Add Customer Address
                            if ($cus_id) {
                                if (is_numeric($customer_details->streetNumber)) {
                                    $houseno = $customer_details->streetNumber;

                                    $addressdata = $this->getAddressDetails(trim(str_replace(' ', '', $customer_details->postalCode)), trim(str_replace(' ', '', $houseno)));
                                    if ($addressdata) {
                                        $lat = $addressdata['geo']['lat'];
                                        $long = $addressdata['geo']['lon'];
                                    }
                                } else {
                                    $pattern = preg_match('/\s(\d.*)/', $customer_details->streetNumber, $match);
                                    if ($pattern) {
                                        $houseno = $match[0];

                                        $addressdata = $this->getAddressDetails(trim(str_replace(' ', '', $customer_details->postalCode)), trim(str_replace(' ', '', $houseno)));
                                        if ($addressdata) {
                                            $lat = $addressdata['geo']['lat'];
                                            $long = $addressdata['geo']['lon'];
                                        }
                                    }
                                }

                                $address = new CustomerAddress();
                                $checkfirst = CustomerAddress::where("customer_id", $cus_id)->get()->count();
                                $address->default = ($checkfirst == 0) ? 1 : 0;
                                $address->customer_id = $cus_id;
                                $address->address = $customer_details->street;
                                $address->post_code = $customer_details->postalCode;
                                $address->latitude  = $lat ? $lat : '';
                                $address->longitude = $long ? $long : '';
                                $address->house_no = $customer_details->streetNumber;
                                $address->save();
                                Customer::where('customer_id', $cus_id)->update(['customer_address' => $address->address_id]);
                            }
                        }

                        // Add Order
                        $totalPrice = isset($orderval->totalPrice) ? ($orderval->totalPrice) : 0;
                        $totalDiscount = isset($orderval->totalDiscount) ? ($orderval->totalDiscount) : 0;
                        $deliveryCosts = isset($orderval->deliveryCosts) ? ($orderval->deliveryCosts) : 0;

                        $order = new Order();
                        $order->order_uuid = Str::uuid();
                        $order->customer_id = $cus_id;
                        $order->order_store_id = $orderval->restaurantId;
                        $order->order_takeaway_id =  $orderval->id;
                        $order->order_takeaway_key =  $orderval->orderKey;
                        $order->order_takeaway_public_ref = $orderval->publicReference;
                        $order->order_channel_id = $orderval->restaurantId;
                        $order->order_address_id = 0;
                        $order->order_price = $totalPrice - $deliveryCosts;
                        $order->order_delivery_charge = $deliveryCosts;
                        $order->order_final_amount = $totalPrice;
                        $order->order_discount = $totalDiscount;
                        $order->order_final_with_discount = $totalPrice - $totalDiscount;
                        $order->order_status = '1';
                        $order->order_payment = "";
                        $order->order_payment_status = ($orderval->isPaid != false);
                        $order->payment_method = $orderval->paymentMethod;
                        $order->created_at = date('Y-m-d H:i:s', strtotime($orderval->orderDate));
                        $order->updated_at = date('Y-m-d H:i:s', strtotime($orderval->orderDate));
                        $order->save();

                        // Generate Receipt ID
                        if ($order) {
                            $token = mt_rand(1000, 9999);
                            $receipt_id = $order->order_id . $token;
                            Order::where('order_id', $order->order_id)->update(['order_receiptid' => $receipt_id]);

                            // Add Order Details
                            $products = $orderval->products;
                            foreach ($products as $product) {
                                $product_data = [];
                                $product_data = [
                                    'id' => $product->id,
                                    'name' => $product->name,
                                    'category' => $product->category,
                                    'count' => $product->count,
                                    'price' => $product->price
                                ];

                                $od = new OrderDetail();
                                $od->od_orderid = $order->order_id;
                                $od->od_productid = $product->id;
                                $od->od_qty = $product->count;
                                $od->od_itemprice = $product->price;
                                $od->od_total = ($product->price) * ($product->count);
                                $od->od_vatprice = $product->price;
                                $od->od_vattotal = ($product->price) * ($product->count);
                                $od->product_details = json_encode($product_data);
                                $od->save();
                            }
                        }
                    }
                }
            }

            return response()->json(['success' => 1, 'message' => 'Data saved.']);
        } catch (\Throwable $th) {
            echo $th;

            return response()->json([
                'message' => 'Please try again later.',
                'status' => false,
                'data' => []
            ], 500);
        }
    }

    public static function takeawayOrderStatus($order_takeaway_id, $status, $order_takeaway_key)
    {
        $status_array = [
            "1 " => "printed",
            "12 " => "confirmed_change_delivery_time",
            "4" => "kitchen",
            "6" => "in_delivery",
            "10" => "delivered",
            "11" => "error"
        ];

        $id = $order_takeaway_id;
        $status = $status;
        $key = $order_takeaway_key;

        $client = new GuzzleClient(['verify' => false]);

        if ($status == 11) {
            $data = array(
                "id" => $id,
                "key" => $key,
                "status" => $status_array[$status],
                "text" => "OUT_OF_ITEMS"
            );
        } elseif ($status == 12) {
            $orderDate = Order::select('created_at')->where(['order_takeaway_id' => $id, 'order_takeaway_key' => $key])->first();
            $orderDate = $orderDate->created_at;

            if ($orderDate) {
                // Original datetime string
                $originalDatetime = $orderDate;

                // Parse the original datetime string into a Carbon instance
                $carbonDatetime = Carbon::parse($originalDatetime);

                // Add 30 minutes to the Carbon instance
                $carbonDatetime->addHours(2)->addMinutes(30);

                // Format the Carbon instance in the desired format
                $formattedDatetime = $carbonDatetime->format('Y-m-d\TH:i:s');

                $deliveryDateTime = $formattedDatetime . "+02:00";
            } else {
                // Get the current date and time
                $currentDatetime = Carbon::now();

                // Add 30 minutes to the current date and time
                $currentDatetime->addHours(2)->addMinutes(30);

                // Format the current date and time in the desired format
                $deliveryDateTime = $currentDatetime->format('Y-m-d\TH:i:sP');
            }

            // dd($deliveryDateTime);

            $data = array(
                "id" => $id,
                "key" => $key,
                "status" => "confirmed_change_delivery_time",
                "changedDeliveryTime" => $deliveryDateTime
                // "changedDeliveryTime" => "2023-09-15T13:30:16+02:00"
            );
        } else {
            $data = array(
                "id" => $id,
                "key" => $key,
                "status" => $status_array[$status]
            );
        }

        $response = $client->post($url, [
            'headers' => [
                'Content-type' => 'application/json',
            ],
            'body' => json_encode($data)
        ]);

        // Error response if it has error on getting auth token
        if ($response->getStatusCode() != 200) {
            return response()->json([
                'message' => 'Please try again later.',
                'status' => false,
                'data' => []
            ], 500);
        }

        $statusDetails = json_decode($response->getBody()->getContents());

        if ($statusDetails->code == 200) {
            return response()->json(['success' => 1, 'message' => 'Status updated.']);
        } else {
            return response()->json(['success' => 0, 'message' => 'Something is wrong.']);
        }
    }
}
