<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\Cart as AppCart;
use App\Models\Category;
use App\Models\CustomerAddress;
use App\Models\DeliveryPerson;
use App\Models\DeliveryTimeSchedule;
use App\Models\Favourite;
use App\Models\Franchise;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Pool;
use App\Models\Schedule;
use App\Models\Setting;
use Edujugon\PushNotification\PushNotification;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class BaseController extends Controller
{
    public function __construct()
    {
        view()->composer('*', function ($view) {
            $html = '';
            $data['cart_item_total'] = 0;
            $data['favourite_item_total'] = 0;
            $data['fav_array'] = [];
            if (auth('customer')->check()) {
                $data['cart_item_total'] = AppCart::where('customer_id', auth('customer')->user()->id)->get()->count();
                $data['fav_array'] = Favourite::where('customer_id', auth('customer')->user()->id)->pluck('product_id')->toArray();

                $data['favourite_item_total'] = Favourite::where('customer_id', auth('customer')->user()->id)->get()->count();
            }

            $data['settings'] = Setting::find('1');
            $data['dayschedule'] = DeliveryTimeSchedule::get()->all();
            $href_extra = url('products/extra_product');
            $html .= "<li class=''><a href='$href_extra'>Other Category</a></li>";
            $data['html'] = $html;
            $amount_value = $this->setFinalAmountInFooter();
            $data['final_amount_footer'] = $amount_value['final_amount'];
            $view->with('global', $data);
        });
    }

    public function has_sub($id)
    {
        $category = Category::wherenull('deleted_at')->where('is_show', '1')->where('category_id', $id)->get();
        $total = $category->count();

        return $total >= 1 ? true : false;
    }

    public function getAddressDetails($postcode, $houseno)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'token'        => $_ENV['ADDRESS_API_TOKEN'],
        ];

        $client = new GuzzleClient([
            'headers'     => $headers,
            'http_errors' => false,
            'verify'      => false,
        ]);
        $url = $_ENV['POST_CODE_API_URL'].'?postcode='.$postcode.'&number='.$houseno;
        $response = $client->request('GET', $url);

        return json_decode($response->getBody()->getContents(), true);
    }

    public static function setFinalAmountInFooter()
    {

        if (Auth::guard('customer')->check()) {
            $customer_id = auth('customer')->user()->customer_id;
            $data['contact_no'] = auth('customer')->user()->customer_contact_no;
            $data['address'] = CustomerAddress::where('customer_id', $customer_id)->where('default', '1')->first();
            $data['cart_contents'] = AppCart::leftJoin('products', function ($join) {
                $join->on('products.product_id', '=', 'cart.cart_itemid');
            })->leftJoin('categories', function ($join) {
                $join->on('categories.category_id', '=', 'products.category_id');
            })->where('cart_custid', $customer_id)->get(['cart_id', 'cart_itemid', 'cart_qty', 'cart_itemprice', 'cart_total', 'cart_vatprice', 'cart_vattotal', 'product_id', 'product_name', 'product_price', 'products.image', 'vat', 'vat_price', 'products.category_id', 'category_name']);

            foreach ($data['cart_contents'] as $key => $value) {
                $data['cart_contents'][$key]['image_source'] = ($value['image'] != '') ? asset('uploads/product/'.$value->image) : asset('img/247-Drank-Logo.png');
            }
            $data['cart_total_price'] = AppCart::where('cart_custid', $customer_id)->get()->sum('cart_vattotal');
            if ($data['address'] != null && $data['address'] != '') {
                $pool = Pool::whereNull('deleted_at')->get();
                $array = [];

                foreach ($pool as $value) {
                    $code = preg_replace('/[^0-9.]+/', '', $data['address']['post_code']);
                    if ($code >= $value->from_postcode && $code <= $value->to_postcode) {
                        $array[] = $value['pool_id'];
                    }
                }
                if (count($array) > 0) {

                    $pool_id = $array['0'];
                    $pooldetail = Pool::find($pool_id);
                    if ($data['cart_total_price'] != '0' && $data['cart_total_price'] <= $pooldetail['delivery_freefrom']) {
                        $data['delivery_charge'] = $pooldetail['delivery_charge'];
                    } elseif ($data['cart_total_price'] >= $pooldetail['delivery_freefrom']) {
                        $data['delivery_charge'] = 0.00;
                    } else {
                        $data['delivery_charge'] = 0.00;
                    }
                    $data['delivery_charge'] = ($data['address'] != null && $data['address'] != '') ? $data['delivery_charge'] : 0.00;
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
                if ($data['cart_total_price'] > 75) {
                    $data['delivery_charge'] = 0.00;
                } else {
                    $data['delivery_charge'] = 2.50;
                }
                $data['final_amount'] = $data['delivery_charge'] + $data['cart_total_price'];
            }
        } else {
            $total = Cart::subtotal();
            $data['cart_total_pricemain'] = str_replace(',', '', $total);
            $data['cart_total_price'] = $total;

            if ($data['cart_total_pricemain'] > 75) {
                $data['delivery_charge'] = 0.00;
                $data['final_amount'] = $data['delivery_charge'] + $data['cart_total_pricemain'];
            } else {
                $data['delivery_charge'] = $total != 0.00 ? '2.50' : '0';
                $data['final_amount'] = $data['delivery_charge'] + $data['cart_total_pricemain'];
            }
            $data['final_amount'] = number_format($data['final_amount'], 2);
        }

        return $data;
    }

    public static function OrderAssignment($order_id)
    {

        try {
            $data['oid'] = $order_id;

            /** @var Order $orderdetail */
            $orderdetail = Order::with('customer')->with('address')->where('order_id', $data['oid'])->whereNull('deleted_at')->first();

            $postcode = $orderdetail['address']['post_code'];
            $postcode = (int) preg_replace('/[^0-9]/i', '', $postcode);
            $lat = $orderdetail->address?->latitude;
            $long = $orderdetail->address?->longitude;

            $pool_id = Pool::where('from_postcode', '<=', $postcode)->where('to_postcode', '>=', $postcode)->whereNull('deleted_at')->pluck('pool_id');

            if (count($pool_id) > 0) {

                $pool_id = $pool_id['0'];
                $onfranchise = Franchise::whereNull('deleted_at')->where('fs_on_off', 'online')->get();
                if (count($onfranchise) > 0) {

                    $franchisesidon = Franchise::whereNull('deleted_at')->where('fs_on_off', 'online')->whereRaw('FIND_IN_SET(?, franchise_pool) > 0', [$pool_id])->get(['franchise_id', 'franchises_name']);
                    $stdate = date('Y-m-d', time());
                    $starttime = date('H:i:s', time());
                    if (count($franchisesidon) == 0) {

                        $franchisesid = Franchise::whereNull('deleted_at')->where('fs_on_off', 'online')->get(['franchise_pool'])->toArray();
                        $arr = [];
                        foreach ($franchisesid as $value) {
                            $arr[] = $value['franchise_pool'];
                        }
                        $implode = implode(',', $arr);
                        $poolarray = array_unique(explode(',', $implode));
                        $asspool = Pool::whereNull('deleted_at')->whereIn('pool_id', $poolarray)->get(['pool_id', 'from_postcode'])->toArray();
                        $smallest = [];
                        foreach ($asspool as $value) {
                            $smallest[$value['pool_id']] = abs($value['from_postcode'] - preg_replace('/[^0-9]/i', '', $postcode));
                        }
                        $poolarray = (array_keys($smallest, min($smallest)));
                        $franchisesid = Franchise::whereNull('deleted_at')->where('fs_on_off', 'online')->whereRaw('FIND_IN_SET(?, franchise_pool) > 0', [$poolarray[0]])->get(['franchise_id', 'franchises_name']);
                        $franchisesid = $franchisesid['0']['franchise_id'];
                    } else {

                        $franchisesid = $franchisesidon[0]['franchise_id'];
                    }

                    //Assign delievry person
                    $deliverypersonid = Schedule::whereNull('schedule.deleted_at')->where('s_status', '2')->where('s_pool', $pool_id)
                        ->leftJoin('deliveryperson', function ($join) {
                            $join->on('deliveryperson.dp_id', '=', 'schedule.s_dpid');
                        })
                        ->whereRaw('("'.$stdate.'" >= DATE(s_startdate) AND "'.$stdate.'" <= DATE(s_enddate)) && ( CAST(s_startdate AS TIME) <= "'.$starttime.'" AND   CAST(s_enddate AS TIME) >= "'.$starttime.'")')
                        ->where('s_fid', $franchisesid)
                        ->get(['dp_id', 'dp_name']);

                    if (count($deliverypersonid) == 0) {

                        $deliverypersonid = DeliveryPerson::select('deliveryperson_sub.*', 'deliveryperson.*')->where('deliveryperson.dp_onoff', 'online')->whereNull('deliveryperson_sub.deleted_at')->whereNull('deliveryperson.deleted_at')
                            ->leftJoin('deliveryperson_sub', function ($join) {
                                $join->on('deliveryperson_sub.s_dpid', '=', 'deliveryperson.dp_id')->where('deliveryperson.dp_onoff', 'online');
                            })->where('s_fid', $franchisesid)->orWhereRaw('FIND_IN_SET(?,s_pool)', [$pool_id])->groupBy('dp_id')->get(['dp_id', 'dp_name']);
                        $arr_dp = $deliverypersonid->pluck('dp_id')->toArray();
                        if (count($deliverypersonid) == 0) {
                            // if($lat!='' && $long!='' ){
                            //     $deliverypersonid = DeliveryPerson::select(DB::raw("*,SQRT(POW(69.1  * (dp_lat - ' . $lat . '), 2) + POW(69.1 * (' . $long . ' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance"))
                            //     ->where('dp_onoff', 'online')
                            //     ->orderBy('distance', 'ASC')
                            //     ->get();
                            // }

                        } else {
                            if (count($deliverypersonid) > 1) {
                                $odrer = Order::orderBy('order_id', 'desc')->whereIn('delivery_person_id', $arr_dp)->first();
                                $dp_order = $odrer['delivery_person_id'];
                                if ($lat != '' && $long != '') {
                                    $deliverypersonid = DeliveryPerson::select(DB::raw("*,SQRT(POW(69.1 *  (dp_lat - ' . $lat . '), 2) + POW(69.1 * (' . $long . ' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance"))
                                        ->where('dp_onoff', 'online')
                                        ->orderBy('distance', 'ASC')
                                        ->whereIn('dp_id', $arr_dp)
                                        ->whereNotIn('dp_id', [$dp_order])
                                        ->get();
                                } else {
                                    $deliverypersonid = DeliveryPerson::where('dp_onoff', 'online')
                                        ->whereIn('dp_id', $arr_dp)
                                        ->whereNotIn('dp_id', [$dp_order])
                                        ->get();
                                }
                            } else {

                                $deliverypersonid[0]['dp_id'];
                            }
                        }
                    }
                    $data['deliverypersonid'] = count($deliverypersonid) > 0 ? $deliverypersonid[0]['dp_id'] : '0';
                    $data['franchisesid'] = $franchisesid;
                } else {

                    $deliverypersonid = DeliveryPerson::select('deliveryperson_sub.*', 'deliveryperson.*')->where('dp_onoff', 'online')->whereNull('deliveryperson_sub.deleted_at')->whereNull('deliveryperson.deleted_at')
                        ->leftJoin('deliveryperson_sub', function ($join) {
                            $join->on('deliveryperson_sub.s_dpid', '=', 'deliveryperson.dp_id')->where('deliveryperson.dp_onoff', 'online');
                        })->whereRaw('FIND_IN_SET(?,s_pool)', [$pool_id])->groupBy('dp_id')->get(['dp_id', 'dp_name']);
                    $arr_dp = $deliverypersonid->pluck('dp_id')->toArray();
                    if (count($deliverypersonid) == 0) {
                        if ($lat != '' && $long != '') {
                            $deliverypersonid = DeliveryPerson::select(DB::raw("*,SQRT(POW(69.1 * (dp_lat - ' . $lat . '), 2) + POW(69.1 * (' . $long . ' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance"))
                                ->where('dp_onoff', 'online')
                                ->orderBy('distance', 'ASC')
                                ->get();
                        }
                    } else {
                        if (count($deliverypersonid) > 1) {
                            $odrer = Order::orderBy('order_id', 'desc')->whereIn('delivery_person_id', $arr_dp)->first();
                            $dp_order = $odrer['delivery_person_id'];
                            if ($lat != '' && $long != '') {
                                $deliverypersonid = DeliveryPerson::select(DB::raw("*,SQRT(POW(69.1  * (dp_lat - ' . $lat . '), 2) + POW(69.1 * (' . $long . ' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance"))
                                    ->where('dp_onoff', 'online')
                                    ->orderBy('distance', 'ASC')
                                    ->whereIn('dp_id', $arr_dp)
                                    ->whereNotIn('dp_id', [$dp_order])
                                    ->get();
                            } else {
                                $deliverypersonid = DeliveryPerson::where('dp_onoff', 'online')
                                    ->whereIn('dp_id', $arr_dp)
                                    ->whereNotIn('dp_id', [$dp_order])
                                    ->get();
                            }
                        } else {
                            $deliverypersonid[0]['dp_id'];
                        }
                    }
                    $data['deliverypersonid'] = count($deliverypersonid) > 0 ? $deliverypersonid[0]['dp_id'] : '0';
                    $data['franchisesid'] = '0';
                }
            } else {
                $data['deliverypersonid'] = '0';
                $data['franchisesid'] = '0';
            }

            if ($data['deliverypersonid'] != '0' || $data['franchisesid'] != '0') {

                Order::where('order_id', $order_id)->update(['order_status' => '2', 'franchise_id' => $data['franchisesid'], 'delivery_person_id' => $data['deliverypersonid'], 'od_assignedtime' => now()]);
                if ($data['deliverypersonid'] != '0') {
                    if ($data['franchisesid'] != '0') {

                        $frname = Franchise::find($data['franchisesid']);
                        $mess = $frname['franchises_name'].' has assigned order order no. '.$order_id.' to you';
                    } else {
                        $mess = 'order order no. '.$order_id.' to you';
                    }

                    /** notification To Franchise*/
                    $message = 'Order No '.$order_id.' Assigned to you';
                    $notification = new Notification;
                    $notification->user_type = 'franchise';
                    $notification->to_id = $data['franchisesid'];
                    $notification->text = $message;
                    $notification->save();

                    /** notification to Delivery Person*/
                    $notification = new Notification;
                    $notification->user_type = 'delivery_person';
                    $notification->to_id = $data['deliverypersonid'];
                    $notification->text = $message;
                    $notification->save();

                    /* push notification */
                    $devpersondetail = DeliveryPerson::find($data['deliverypersonid']);

                    $push = new PushNotification('fcm');
                    $push->setMessage([
                        'data' => [
                            'notification' => [
                                'title'        => 'Order Status',
                                'message'      => $mess,
                                'sound'        => 'default',
                                'order_id'     => $order_id,
                                'schedule_id'  => 0,
                                'message_type' => 'order',

                                // 'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            ],
                        ],
                    ])

                        ->setUrl(env('PUSH_NOTIFICATION_URL'))
                        ->setApiKey(env('PUSH_NOTIFICATION_APIKEY'))
                        ->setDevicesToken($devpersondetail['dp_devicetoken'])
                        ->send()
                        ->getFeedback();
                }
            } else {
                Order::where('order_id', $order_id)->update(['order_status' => '1']);
            }
        } catch (\Exception $e) {
        }
    }

    public function OrderTest($order_id)
    {

        $data['oid'] = $order_id;

        $orderdetail = Order::with('customer')->with('address')->where('order_id', $data['oid'])->whereNull('deleted_at')->first();

        $postcode = $orderdetail['address']['post_code'];
        $postcode = (int) preg_replace('/[^0-9]/i', '', $postcode);
        $lat = $orderdetail['address']['latitude'] ?? null;
        $long = $orderdetail['address']['longitude'] ?? null;

        $pool_id = Pool::where('from_postcode', '<=', $postcode)->where('to_postcode', '>=', $postcode)->whereNull('deleted_at')->pluck('pool_id');

        if (count($pool_id) > 0) {

            $pool_id = $pool_id['0'];
            $onfranchise = Franchise::whereNull('deleted_at')->where('fs_on_off', 'online')->get();
            if (count($onfranchise) > 0) {

                $franchisesidon = Franchise::whereNull('deleted_at')->where('fs_on_off', 'online')->whereRaw('FIND_IN_SET(?, franchise_pool) > 0', [$pool_id])->get(['franchise_id', 'franchises_name']);
                $stdate = date('Y-m-d', time());
                $starttime = date('H:i:s', time());
                if (count($franchisesidon) == 0) {

                    $franchisesid = Franchise::whereNull('deleted_at')->where('fs_on_off', 'online')->get(['franchise_pool'])->toArray();
                    $arr = [];
                    foreach ($franchisesid as $value) {
                        $arr[] = $value['franchise_pool'];
                    }
                    $implode = implode(',', $arr);
                    $poolarray = array_unique(explode(',', $implode));
                    $asspool = Pool::whereNull('deleted_at')->whereIn('pool_id', $poolarray)->get(['pool_id', 'from_postcode'])->toArray();
                    $smallest = [];
                    foreach ($asspool as $value) {
                        $smallest[$value['pool_id']] = abs($value['from_postcode'] - preg_replace('/[^0-9]/i', '', $postcode));
                    }
                    $poolarray = (array_keys($smallest, min($smallest)));
                    $franchisesid = Franchise::whereNull('deleted_at')->where('fs_on_off', 'online')->whereRaw('FIND_IN_SET(?, franchise_pool) > 0', [$poolarray[0]])->get(['franchise_id', 'franchises_name']);
                    $franchisesid = $franchisesid['0']['franchise_id'];
                } else {

                    $franchisesid = $franchisesidon[0]['franchise_id'];
                }

                //Assign delievry person
                $deliverypersonid = Schedule::whereNull('schedule.deleted_at')->where('s_status', '2')->where('s_pool', $pool_id)
                    ->leftJoin('deliveryperson', function ($join) {
                        $join->on('deliveryperson.dp_id', '=', 'schedule.s_dpid');
                    })
                    ->whereRaw('("'.$stdate.'" >= DATE(s_startdate) AND "'.$stdate.'" <= DATE(s_enddate)) && ( CAST(s_startdate AS TIME) <= "'.$starttime.'" AND   CAST(s_enddate AS TIME) >= "'.$starttime.'")')
                    ->where('s_fid', $franchisesid)
                    ->get(['dp_id', 'dp_name']);

                if (count($deliverypersonid) == 0) {

                    $deliverypersonid = DeliveryPerson::select('deliveryperson_sub.*', 'deliveryperson.*')->where('deliveryperson.dp_onoff', 'online')->whereNull('deliveryperson_sub.deleted_at')->whereNull('deliveryperson.deleted_at')
                        ->leftJoin('deliveryperson_sub', function ($join) {
                            $join->on('deliveryperson_sub.s_dpid', '=', 'deliveryperson.dp_id')->where('deliveryperson.dp_onoff', 'online');
                        })->where('s_fid', $franchisesid)->orWhereRaw('FIND_IN_SET(?,s_pool)', [$pool_id])->groupBy('dp_id')->get(['dp_id', 'dp_name']);
                    $arr_dp = $deliverypersonid->pluck('dp_id')->toArray();
                    if (count($deliverypersonid) == 0) {
                        // if($lat!='' && $long!='' ){
                        //     $deliverypersonid = DeliveryPerson::select(DB::raw("*,SQRT(POW(69.1  * (dp_lat - ' . $lat . '), 2) + POW(69.1 * (' . $long . ' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance"))
                        //     ->where('dp_onoff', 'online')
                        //     ->orderBy('distance', 'ASC')
                        //     ->get();
                        // }

                    } else {
                        if (count($deliverypersonid) > 1) {
                            $odrer = Order::orderBy('order_id', 'desc')->whereIn('delivery_person_id', $arr_dp)->first();
                            $dp_order = $odrer['delivery_person_id'];
                            if ($lat != '' && $long != '') {
                                $deliverypersonid = DeliveryPerson::select(DB::raw("*,SQRT(POW(69.1 *  (dp_lat - ' . $lat . '), 2) + POW(69.1 * (' . $long . ' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance"))
                                    ->where('dp_onoff', 'online')
                                    ->orderBy('distance', 'ASC')
                                    ->whereIn('dp_id', $arr_dp)
                                    ->whereNotIn('dp_id', [$dp_order])
                                    ->get();
                            } else {
                                $deliverypersonid = DeliveryPerson::where('dp_onoff', 'online')
                                    ->whereIn('dp_id', $arr_dp)
                                    ->whereNotIn('dp_id', [$dp_order])
                                    ->get();
                            }
                        } else {

                            $deliverypersonid[0]['dp_id'];
                        }
                    }
                }
                $data['deliverypersonid'] = count($deliverypersonid) > 0 ? $deliverypersonid[0]['dp_id'] : '0';
                $data['franchisesid'] = $franchisesid;
            } else {

                $deliverypersonid = DeliveryPerson::select('deliveryperson_sub.*', 'deliveryperson.*')->where('dp_onoff', 'online')->whereNull('deliveryperson_sub.deleted_at')->whereNull('deliveryperson.deleted_at')
                    ->leftJoin('deliveryperson_sub', function ($join) {
                        $join->on('deliveryperson_sub.s_dpid', '=', 'deliveryperson.dp_id')->where('deliveryperson.dp_onoff', 'online');
                    })->whereRaw('FIND_IN_SET(?,s_pool)', [$pool_id])->groupBy('dp_id')->get(['dp_id', 'dp_name']);
                $arr_dp = $deliverypersonid->pluck('dp_id')->toArray();
                if (count($deliverypersonid) == 0) {
                    if ($lat != '' && $long != '') {
                        $deliverypersonid = DeliveryPerson::select(DB::raw("*,SQRT(POW(69.1 * (dp_lat - ' . $lat . '), 2) + POW(69.1 * (' . $long . ' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance"))
                            ->where('dp_onoff', 'online')
                            ->orderBy('distance', 'ASC')
                            ->get();
                    }
                } else {
                    if (count($deliverypersonid) > 1) {
                        $odrer = Order::orderBy('order_id', 'desc')->whereIn('delivery_person_id', $arr_dp)->first();
                        $dp_order = $odrer['delivery_person_id'];
                        if ($lat != '' && $long != '') {
                            $deliverypersonid = DeliveryPerson::select(DB::raw("*,SQRT(POW(69.1  * (dp_lat - ' . $lat . '), 2) + POW(69.1 * (' . $long . ' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance"))
                                ->where('dp_onoff', 'online')
                                ->orderBy('distance', 'ASC')
                                ->whereIn('dp_id', $arr_dp)
                                ->whereNotIn('dp_id', [$dp_order])
                                ->get();
                        } else {
                            $deliverypersonid = DeliveryPerson::where('dp_onoff', 'online')
                                ->whereIn('dp_id', $arr_dp)
                                ->whereNotIn('dp_id', [$dp_order])
                                ->get();
                        }
                    } else {
                        $deliverypersonid[0]['dp_id'];
                    }
                }
                $data['deliverypersonid'] = count($deliverypersonid) > 0 ? $deliverypersonid[0]['dp_id'] : '0';
                $data['franchisesid'] = '0';
            }
        } else {
            $data['deliverypersonid'] = '0';
            $data['franchisesid'] = '0';
        }
        echo $data['deliverypersonid'];
        echo $data['franchisesid'];
    }

    public function setDeliverycharge($getdata)
    {
        $post_code = $getdata['post_code'];
        $pool = Pool::whereNull('deleted_at')->get();
        $array = [];

        foreach ($pool as $value) {
            $code = preg_replace('/[^0-9.]+/', '', $post_code);
            if ($code >= $value->from_postcode && $code <= $value->to_postcode) {
                $array[] = $value['pool_id'];
            }
        }

        if (count($array) > 0) {

            $pool_id = $array['0'];
            $pooldetail = Pool::find($pool_id);
            if ($getdata['final_amount'] != '0' && $getdata['final_amount'] <= $pooldetail['delivery_freefrom']) {
                $data['delivery_charge'] = $pooldetail['delivery_charge'];
            } elseif ($getdata['final_amount'] >= $pooldetail['delivery_freefrom']) {
                $data['delivery_charge'] = 0.00;
            } else {
                $data['delivery_charge'] = 0.00;
            }
            $data['delivery_charge'] = $data['delivery_charge'];
            $data['final_amount'] = $data['delivery_charge'] + $getdata['final_amount'];
        } else {

            if ($getdata['final_amount'] > 75) {
                $data['delivery_charge'] = 0.00;
            } else {
                $data['delivery_charge'] = 2.50;
            }
            $data['final_amount'] = $data['delivery_charge'] + $getdata['final_amount'];
        }

        return $data;
    }
}
