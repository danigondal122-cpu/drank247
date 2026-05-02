<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Base\BaseController;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\UberStore;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

class UberController extends BaseController
{
    public LoggerInterface $uberLogger;

    public function __construct(
    ) {
        $this->uberLogger = logger()->driver('uber');
    }

    public function uberWebhook(Request $request)
    {
        try {
            $hookResponse = $request->all();

            $this->uberLogger->info('Uber request', [
                'data' => $hookResponse,
            ]);

            $orderId = $hookResponse['meta']['resource_id'];

            // Get eats.store token
            $tokenResponse = UberStore::getAccessToken(scope: 'eats.order');

            if ($tokenResponse['status'] == 1) {

                /**
                 * @see https://developer.uber.com/docs/eats/references/api/v2/get-eats-order-orderid
                 */
                $url = 'https://api.uber.com/v2/eats/order/'.$orderId;
                $response = Http::acceptJson()->withToken($tokenResponse['access_token'])->get($url);

                // Error response if it has error on getting stores
                if ($response->status() != 200) {
                    return response()->json([
                        'message' => 'Please try again later.',
                        'status'  => false,
                        'data'    => [],
                    ], 500);
                }

                /**
                 * @var object{
                 *   id: string,
                 *   display_id: string,
                 *   external_reference_id: string,
                 *   current_state: string,
                 *   store: object{
                 *     id: string,
                 *     name: string,
                 *     external_reference_id: string,
                 *     integrator_store_id: string,
                 *     integrator_brand_id: string,
                 *     merchant_store_id: string
                 *   },
                 *   eater: object{
                 *     first_name: string,
                 *     phone: string,
                 *     phone_code: string,
                 *     delivery: object{
                 *       location: object{
                 *         type: string,
                 *         street_address?: string,
                 *         latitude?: float,
                 *         longitude?: float,
                 *         google_place_id?: string,
                 *         unit_number?: string,
                 *         business_name?: string,
                 *         title?: string
                 *       },
                 *       notes?: string
                 *     },
                 *     eaters: array{
                 *       object{
                 *         id: string,
                 *         first_name: string
                 *       }
                 *     }
                 *   },
                 *   cart: object{
                 *     items: array{
                 *       object{
                 *         id: string,
                 *         instance_id: string,
                 *         title: string,
                 *         external_data: string,
                 *         quantity: int,
                 *         price: object{
                 *           unit_price: object{
                 *             amount: int,
                 *             currency_code: string,
                 *             formatted_amount: string
                 *           },
                 *           total_price: object{
                 *             amount: int,
                 *             currency_code: string,
                 *             formatted_amount: string
                 *           },
                 *           base_unit_price: object{
                 *             amount: int,
                 *             currency_code: string,
                 *             formatted_amount: string
                 *           },
                 *           base_total_price: object{
                 *             amount: int,
                 *             currency_code: string,
                 *             formatted_amount: string
                 *           }
                 *         },
                 *         selected_modifier_groups: array{
                 *           object{
                 *             id: string,
                 *             title: string,
                 *             external_data: string,
                 *             selected_items: array{
                 *               object{
                 *                 id: string,
                 *                 title: string,
                 *                 external_data: string,
                 *                 quantity: int,
                 *                 price: object{
                 *                   unit_price: object{
                 *                     amount: int,
                 *                     currency_code: string,
                 *                     formatted_amount: string
                 *                   },
                 *                   total_price: object{
                 *                     amount: int,
                 *                     currency_code: string,
                 *                     formatted_amount: string
                 *                   }
                 *                 }
                 *               }
                 *             },
                 *             eater_id: string
                 *           }
                 *         }
                 *       }
                 *     }
                 *   },
                 *   fulfillment_issues: array{
                 *     object{
                 *       fulfillment_issue_type: string,
                 *       fulfillment_action_type: string,
                 *       root_item: object{
                 *         id: string,
                 *         instance_id: string,
                 *         title: string,
                 *         quantity: int,
                 *         fulfillment_action: object{
                 *           fulfillment_action_type: string
                 *         }
                 *       },
                 *       item_availability_info: object{
                 *         items_requested: int,
                 *         items_available: int
                 *       }
                 *     }
                 *   },
                 *   payment: object{
                 *     charges: object{
                 *       total: object{
                 *         amount: int,
                 *         currency_code: string,
                 *         formatted_amount: string
                 *       },
                 *       sub_total: object{
                 *         amount: int,
                 *         currency_code: string,
                 *         formatted_amount: string
                 *       },
                 *       tax: object{
                 *         amount: int,
                 *         currency_code: string,
                 *         formatted_amount: string
                 *       },
                 *       total_fee: object{
                 *         amount: int,
                 *         currency_code: string,
                 *         formatted_amount: string
                 *       },
                 *       cash_amount_due: object{
                 *         amount: int,
                 *         currency_code: string,
                 *         formatted_amount: string
                 *       }
                 *     }
                 *   },
                 *   placed_at: string,
                 *   estimated_ready_for_pickup_at: string,
                 *   type: string,
                 *   brand: string,
                 *   order_manager_client_id: string,
                 *   deliveries: array{
                 *     object{
                 *       id: string,
                 *       first_name: string,
                 *       vehicle: object{
                 *         make: string,
                 *         model: string,
                 *         license_plate: string
                 *       },
                 *       picture_url: string,
                 *       estimated_pickup_time: string,
                 *       current_state: string,
                 *       phone: string,
                 *       phone_code: string
                 *     }
                 *   }
                 * }
                 *
                 * Document generated by ChatGPT
                 */
                $orderDetails = $response->object();

                $this->uberLogger->info('Uber response ', [
                    '$orderDetails' => $orderDetails,
                ]);

                /**
                 * @see https://developer.uber.com/docs/eats/references/api/order_suite#tag/WebhookEvents
                 */
                switch ($hookResponse['event_type']) {
                    case 'orders.notification':

                        if ($orderDetails->current_state == 'CREATED') {
                            if (isset($orderDetails->eater)) {
                                $customer_detail = $orderDetails->eater;
                                $customer = new Customer;

                                if (isset($customer_detail->phone)) {
                                    $customer = Customer::query()->firstOrNew(['customer_contact_no' => $customer_detail->phone]);
                                }

                                $customer_first_name = isset($customer_detail->first_name) ? $customer_detail->first_name : '';
                                $customer_last_name = isset($customer_detail->last_name) ? ' '.$customer_detail->last_name : '';

                                $customer->customer_name = $customer_first_name.$customer_last_name;
                                $customer->customer_email = '';
                                $customer->customer_type = '0';
                                $customer->customer_from = '3';
                                $customer->login_type = 'UBER';
                                $customer->phone_code = isset($customer_detail->phone_code) ? $customer_detail->phone_code : '';
                                $customer->customer_contact_no = isset($customer_detail->phone) ? $customer_detail->phone : '';
                                $customer->save();
                                $Cus_id = $customer->id;

                                // Add Customer Address
                                if (isset($orderDetails->eater->delivery)) {
                                    $address_detail = $orderDetails->eater->delivery->location;
                                    if ($address_detail->type == 'STREET_ADDRESS') {

                                        if ($Cus_id != '') {
                                            $lat = $address_detail->latitude;
                                            $long = $address_detail->longitude;

                                            $address = new CustomerAddress;
                                            $fulladdress = $address_detail->street_address;
                                            $address->customer_id = $Cus_id;
                                            $address->address = $fulladdress;
                                            $address->post_code = '';
                                            $address->latitude = $lat;
                                            $address->longitude = $long;
                                            $address->house_no = '';
                                            $address->save();
                                        }
                                    } elseif ($address_detail->type == 'GOOGLE_PLACE') {

                                        if ($Cus_id != '') {

                                            // TO DO Get lat long from place_id
                                            $google_place_id = $address_detail->google_place_id;

                                            // Replace YOUR_API_KEY with your actual API key
                                            $apiKey = config('services.google.map_key');

                                            $apiURL = config('services.google.map_api_url');
                                            $parameters = ['place_id' => $google_place_id, 'key' => $apiKey];

                                            $client = new GuzzleClient;
                                            $response = $client->request('GET', $apiURL, ['query' => $parameters]);

                                            // $statusCode = $response->getStatusCode();
                                            $json = json_decode($response->getBody(), true);

                                            $this->uberLogger->info('geolocation', [
                                                ['data' => $json],
                                            ]);

                                            // Extract the address from the response
                                            $fulladdress = isset($json['result']['formatted_address']) ? $json['result']['formatted_address'] : '';
                                            $lat = isset($json['result']['geometry']['location']['lat']) ? $json['result']['geometry']['location']['lat'] : '';
                                            $long = isset($json['result']['geometry']['location']['lng']) ? $json['result']['geometry']['location']['lng'] : '';

                                            $postcode = '';
                                            if (isset($json['result']['address_components'][6]['types']) && $json['result']['address_components'][6]['types']['0'] == 'postal_code') {
                                                $postcode = $json['result']['address_components'][6]['long_name'];
                                            }

                                            $address = new CustomerAddress;
                                            $address->customer_id = $Cus_id;
                                            $address->address = $fulladdress;
                                            $address->post_code = $postcode;
                                            $address->latitude = $lat;
                                            $address->longitude = $long;
                                            $address->house_no = '';
                                            $address->save();

                                            // try{

                                            // } catch(\Exception $e) {
                                            //   // echo $e->getMessage();
                                            //   $this->uberLogger->error($e);
                                            // }
                                        }
                                    }

                                    $customer_address = isset($address) ? $address->id : 0;
                                    $customer->update(['customer_address' => $customer_address]);
                                }

                                /// Add Order///
                                $paymentCharges = $orderDetails->payment->charges;
                                $total = isset($paymentCharges->total->amount) ? ($paymentCharges->total->amount) / 100 : 0;
                                $sub_total = isset($paymentCharges->sub_total->amount) ? ($paymentCharges->sub_total->amount) / 100 : 0;
                                $total_fee = isset($paymentCharges->total_fee->amount) ? ($paymentCharges->total_fee->amount) / 100 : 0;
                                $delivery_fee = isset($paymentCharges->delivery_fee->amount) ? ($paymentCharges->delivery_fee->amount) / 100 : 0;
                                $total_promo_applied = isset($paymentCharges->total_promo_applied->amount) ? ($paymentCharges->total_promo_applied->amount) / 100 : 0;

                                $order = new Order;
                                $order->uuid = Str::uuid();
                                $order->customer_id = $Cus_id;
                                $order->order_store_id = $orderDetails->store->id;
                                $order->order_uber_id = $orderDetails->id;
                                $order->order_uber_display_id = $orderDetails->display_id;
                                $order->order_address_id = $customer_address ?? null;
                                // $order->order_address_id = $address->address_id;

                                $order->order_price = $sub_total;
                                $order->order_delivery_charge = $delivery_fee;
                                $order->order_service_charge = $total_fee - $delivery_fee;
                                $order->order_final_amount = $sub_total + $total_fee;
                                $order->order_discount = $total_promo_applied;
                                $order->order_final_with_discount = $total;
                                $order->order_status = '0';
                                $order->order_payment = '';
                                $order->order_note = $orderDetails->eater->delivery->notes ?? '';
                                $order->order_payment_status = ! (isset($paymentCharges->cash_amount_due->amount) && $paymentCharges->cash_amount_due->amount > 0);
                                $order->order_deliverytime = isset($paymentCharges->deliveryTime) ? $paymentCharges->deliveryTime : null;
                                $order->created_at = date('Y-m-d H:i:s', strtotime($orderDetails->placed_at));
                                $order->updated_at = date('Y-m-d H:i:s', strtotime($orderDetails->placed_at));
                                $order->uber_order_delivery_type = $orderDetails->type;
                                $order->uber_order_delivery_status = $orderDetails->current_state;
                                $order->save();

                                //# Generate Receipt ID ##

                                if ($order) {
                                    $token = mt_rand(1000, 9999);
                                    $receipt_id = $order->id.$token;
                                    Order::where('id', $order->id)->update(['order_receipt_id' => $receipt_id]);
                                }

                                /// Add Order Detail///
                                $total_amount = 0;
                                if ($order->save()) {
                                    $postData = $orderDetails->cart;
                                    $pro = 0;
                                    foreach ($postData->items as $pkey => $value) {

                                        $od = new OrderDetail;
                                        $order_items = $value;
                                        $detail = Product::where('id', $order_items->id)->first();
                                        if ($detail) {
                                            $pro++;
                                            $od->order_id = $order->id;
                                            $od->product_id = $detail['id'];
                                            $od->od_qty = $order_items->quantity;
                                            $od->od_item_price = ($order_items->price->unit_price->amount) / 100;
                                            $od->od_total = (($order_items->price->unit_price->amount) / 100) * $order_items->quantity;
                                            $od->od_vat_price = ($order_items->price->unit_price->amount) / 100;
                                            $od->od_vat_total = ($order_items->price->unit_price->amount) / 100 * $order_items->quantity;
                                            $od->save();
                                        }
                                    }
                                }

                                $this->uberLogger->info('Save order', [
                                    ['order' => $order],
                                ]);

                                // TODO!: kodingan ini bisa buat error. Nanti hilangkan $od->save()
                                if ($order->save() && $od->save()) {
                                    // if ($order->save() && $od->save() && count($postData->items)==$pro) {
                                    //PREPARING = 3
                                    $this->AcceptUberOrder($orderDetails->id, $tokenResponse['access_token']);
                                    Order::where('id', $order->id)->update(['order_status' => 1]);
                                    if ($orderDetails->type == 'DELIVERY_BY_RESTAURANT') {
                                        $this->OrderAssignment($order->id);
                                    }
                                } else {
                                    UberStore::cancelUberOrder($orderDetails->id);
                                    // $this->denyUberOrder($orderDetails->id, $tokenResponse['access_token']);
                                    Order::where('id', $order->id)->update(['order_status' => 11]);
                                }
                            }
                        } else {
                            //accepted = 12
                            //denied = 7
                            //finished = 10-22
                            //canceled = 11
                            //unknown = 11
                            $status_array = ['ACCEPTED' => 12, 'DENIED' => 7, 'FINISHED' => 10, 'CANCELED' => 11, 'UNKNOWN' => 11];
                            $this->uberLogger->info('Update order status', [
                                'status'  => $orderDetails->current_state,
                                'updated' => Order::query()->where('order_uber_id', $orderId)->update(['order_status' => $status_array[$orderDetails->current_state], 'uber_order_delivery_status' => $orderDetails->current_state]),
                            ]);
                        }

                        break;

                    case 'orders.cancel':

                        Order::where('order_uber_id', $orderId)->update(['order_status' => 11]);
                        $order = Order::where('order_uber_id', $orderId)->first();
                        $this->uberLogger->info('Cancel order', [
                            ['order_id' => $order['id']],
                        ]);

                        break;
                }

                return false;
            }
        } catch (\Throwable $th) {
            $this->uberLogger->error($th);

            return response()->json([
                'message' => 'Please try again later.',
                'status'  => false,
                'data'    => [],
            ], 500);
        }
    }

    public function acceptUberOrder($order_id, $eatsOrderToken)
    {

        // Set order status
        $headers = [
            'Content-Type'  => 'application/json',
            'authorization' => 'Bearer '.$eatsOrderToken,
        ];
        $requestData = [
            'reason'                => 'Accepted by Heny.',
            'external_reference_id' => 'Check #146',
            'fields_relayed'        => [
                'order_special_instructions' => true,
                'promotions'                 => true,

            ],

        ];

        $client = new GuzzleClient([
            'headers' => $headers,
            'body'    => json_encode($requestData),
        ]);
        $url = 'https://api.uber.com/v1/eats/orders/'.$order_id.'/accept_pos_order';
        $response = $client->request('POST', $url);

        // Error response if it has error on getting stores
        // if ($response->getStatusCode() != 200) {
        //   return response()->json([
        //     'message' => 'Please try again later.',
        //     'status' => false,
        //     'data' => []
        //   ], 500);
        // }
        $this->uberLogger->info('acceptUberOrder', [
            ['order_id' => $order_id, 'data' => $response->getStatusCode()],
        ]);
    }

    protected function denyUberOrder($order_id, $eatsOrderToken)
    {

        // Set order status
        $headers = [
            'Content-Type'  => 'application/json',
            'authorization' => 'Bearer '.$eatsOrderToken,
        ];
        $requestData = [
            'reason' => [
                'explanation' => 'failed to submit order',
                'code'        => 'ITEM_AVAILABILITY',
            ],
        ];

        $client = new GuzzleClient([
            'headers' => $headers,
            'body'    => json_encode($requestData),
        ]);
        $url = env('UBER_EATS_ORDER_URL').$order_id.'/deny_pos_order';
        $response = $client->request('POST', $url);

        // Error response if it has error on getting stores
        // if ($response->getStatusCode() != 200) {
        //   return response()->json([
        //     'message' => 'Please try again later.',
        //     'status' => false,
        //     'data' => []
        //   ], 500);
        // }
        $this->uberLogger->info('denyUberOrder', [
            ['order_id' => $order_id, 'data' => $response->getStatusCode()],
        ]);
    }
}
