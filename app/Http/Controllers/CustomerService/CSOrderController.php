<?php

namespace App\Http\Controllers\CustomerService;

use App\Enums\OrderStatusEnum;
use App\Models\CustomerAddress;
use App\Models\DeliveryPerson;
use App\Models\Franchise;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Pool;
use App\Models\Stock;
use App\Services\Deliverect;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CSOrderController extends CSNotificationController
{
    public function orderList()
    {
        $data['Franchise'] = Franchise::whereNull('deleted_at')->get();
        $data['Delivery_person'] = DeliveryPerson::whereNull('deleted_at')->get();
        $data['status_list'] = OrderStatus::get();

        return view('customerservice.order.list', $data);
    }

    public function getList(Request $request)
    {
        session()->forget('cs_frs_id');
        session()->forget('cs_status');
        session()->forget('cs_dp_id');
        $query = Order::select(
            'orders.id',
            'orders.created_at',
            'customer_name',
            'franchises_name',
            'dp_name',
            'order_channel_order_id',
            'order_payment_status',
            'order_status',
            'order_cancelled_reason',
            'os_name',
            'order_final_with_discount',
            'orders.channel_id',
            'channel_image',
            'channel_name',
            'payment_method',
            'customers.customer_contact_no',
            'customers.customer_email',
            'order_address_id',
            'customer_addresses.post_code',
            'customer_addresses.house_no',
            'name',
            'order_store_id',
            'order_uber_id',
            'order_uber_display_id',
            'order_takeaway_id',
            'order_takeaway_key',
            'order_takeaway_public_ref'
        )
            // ->where('phone_code', '!=', '')
            // ->where('order_uber_id', '')
            // ->whereNull('order_takeaway_id')
            ->leftJoin('franchises', function ($join) {
                $join->on('franchises.id', '=', 'orders.franchise_id');
            })->leftJoin('customer_addresses', function ($join) {
                $join->on('customer_addresses.id', '=', 'orders.order_address_id');
            })->leftJoin('customers', function ($join) {
                $join->on('customers.id', '=', 'orders.customer_id');
            })->leftJoin('delivery_people', function ($join) {
                $join->on('delivery_people.id', '=', 'orders.delivery_person_id');
            })->leftJoin('channels', function ($join) {
                $join->on('channels.id', '=', 'orders.channel_id');
            })->leftJoin('uber_stores', function ($join) {
                $join->on('uber_stores.store_id', '=', 'orders.order_store_id');
            })->join('order_statuses', 'order_statuses.id', 'orders.order_status')->where('order_status', '!=', '0')->whereNull('orders.deleted_at');

        if ($request->get('frs_id') && $request->get('frs_id') != '' && $request->get('frs_id') != null) {
            Session::put('cs_frs_id', $request->get('frs_id'));
            $query = $query->where('orders.franchise_id', $request->get('frs_id'));
        }
        if ($request->get('status') && $request->get('status') != '') {
            Session::put('cs_status', $request->get('status'));
            $query = $query->where('orders.order_status', $request->get('status'));
        }

        if ($request->get('dp_id') && $request->get('dp_id') != '') {
            Session::put('cs_dp_id', $request->get('dp_id'));
            $query = $query->where('delivery_people.id', $request->get('dp_id'));
        }

        $column_order = ['channel_image', 'orders.id', 'orders.created_at', 'customer_name', 'franchises_name', 'dp_name', 'order_channel_order_id', 'order_final_with_discount']; //set column field database for datatable orderable
        $column_search = ['orders.id', 'orders.created_at', 'customer_name', 'franchises_name', 'dp_name', 'order_channel_order_id', 'order_final_with_discount', 'customers.customer_contact_no', 'customers.customer_email', 'address', 'customer_addresses.post_code', 'customer_addresses.house_no']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;
        //Search
        if ($request->search['value'] && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }
        //Sorting
        if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('id', 'DESC');
        }

        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function orderView($id)
    {
        $data['row'] = [];
        if ($id) {
            $data['row'] = Order::select(
                'orders.id',
                'orders.created_at',
                'customer_name',
                'franchises_name',
                'franchise_id',
                'delivery_person_id',
                'dp_name',
                'order_channel_order_id',
                'order_payment_status',
                'order_status',
                'order_cancelled_reason',
                'order_price',
                'order_delivery_charge',
                'order_final_amount',
                'promo_code_id',
                'order_discount',
                'os_name',
                'order_final_with_discount',
                'order_receipt_id',
                'customer_email',
                'phone_code',
                'orders.customer_id',
                'customer_contact_no',
                'customer_addresses.post_code',
                'customer_addresses.house_no',
                'customer_addresses.address',
                'franchises_email',
                'dp_email',
                'dp_contact_no',
                'order_discount',
                'order_note',
                'channel_image',
                'od_start_time',
                'od_end_time',
                'order_delivery_time',
                'payment_method',
                'failed_reason',
                'rejected_reason',
                'od_rejected_id',
                'name',
                'order_store_id',
                'order_uber_id',
                'order_uber_display_id',
                'order_takeaway_id',
                'order_takeaway_key',
                'order_takeaway_public_ref'
            )->leftJoin('franchises', function ($join) {
                $join->on('franchises.id', '=', 'orders.franchise_id');
            })->leftJoin('customers', function ($join) {
                $join->on('customers.id', '=', 'orders.customer_id');
            })->leftJoin('customer_addresses', function ($join) {
                $join->on('customer_addresses.id', '=', 'orders.order_address_id');
            })->leftJoin('delivery_people', function ($join) {
                $join->on('delivery_people.id', '=', 'orders.delivery_person_id');
            })->leftJoin('channels', function ($join) {
                $join->on('channels.id', '=', 'orders.channel_id');
            })->leftJoin('uber_stores', function ($join) {
                $join->on('uber_stores.store_id', '=', 'orders.order_store_id');
            })->join('order_statuses', 'order_statuses.id', 'orders.order_status')
                ->where('orders.id', $id)
                ->firstOrFail();

            $explode = explode(',', $data['row']['od_rejected_id']);
            $data['last_dp_id'] = $explode[array_key_last($explode)];

            $data['order_details'] = $data['row']->orderDetails->load('product');
            $data['rejected_by'] = DeliveryPerson::whereIn('id', $explode)->get();
        }

        return view('customerservice.order.view', $data);
    }

    /**
     * @deprecated Hapus ini nanti karena tidak dipakai
     */
    public function deleteOrder(Request $request)
    {
        $rules = [
            'id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        } else {
            Stock::where('order_id', $request->id)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Order deleted !',
                ]);
        }
    }

    public function orderCancelled(Request $request)
    {
        if ($request->cancelledreason == 'other') {
            $request->validate([
                'other' => 'required',
            ]);
            $order_cancelled_reason = $request->other;
        }

        $order_cancelled_reason = $request->cancelledreason;

        $order_id = $request->order_id;
        /** @var null|Order $orderdetail */
        $orderdetail = Order::find($order_id);
        $orderdetail?->update(['order_status' => OrderStatusEnum::REJECTED, 'order_cancelled_reason' => $order_cancelled_reason]);

        if ($order_cancelled_reason == $request->cancelledreason) {

            if ($orderdetail?->order_deliverect_id != '') {
                Deliverect::deliverectOrderStatus($orderdetail->order_deliverect_id, $orderdetail->order_receipt_id, OrderStatusEnum::REJECTED->value);
            }
        }

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Order Cancelled!!',
                'page'   => 'customer_service/order/view/'.$order_id,
            ]);
    }

    public function showCancelledPopup(Request $request)
    {
        $data['oid'] = $request->oid;

        return view('modal.showCancelledPopup', $data);
    }

    public function orderapprovedPopup(Request $request)
    {

        $data['oid'] = $request->id;

        /** @var Order $orderdetail */
        $orderdetail = Order::with('customer')->where('id', $data['oid'])->firstOrFail();
        $Address = CustomerAddress::withTrashed()->where('id', $orderdetail->order_address_id)->firstOrFail();
        $postcode = $Address->post_code;
        $postcode = (int) preg_replace('/[^0-9]/i', '', $postcode);
        $lat = $Address->latitude;
        $long = $Address->longitude;
        $pool = Pool::query()->where('from_postcode', '<=', $postcode)->where('to_postcode', '>=', $postcode)->first('id');
        $pool_id = $pool?->id;
        if ($pool && $pool_id) {
            $onlineFranchiseExists = Franchise::where('fs_on_off', 'online')->exists();
            if ($onlineFranchiseExists) {
                /** @var \Illuminate\Database\Eloquent\Collection<int,Franchise> $franchisesid */
                $franchisesid = $pool->franchises()->where('fs_on_off', 'online')->get(['id', 'franchises_name']);

                // Jika tidak ada franchise dengan pool id yang sama lakukan pencarian franchise dengan pool yang terdekat
                if ($franchisesid->count() == 0) {
                    $pools = Pool::all(['id', 'from_postcode']);
                    $smallest = [];
                    foreach ($pools as $value) {
                        $smallest[$value->id] = abs(preg_replace('/[^0-9]/i', '', $value->from_postcode) - preg_replace('/[^0-9]/i', '', $postcode));
                    }
                    $nearestPoolId = (array_keys($smallest, min($smallest)))[0] ?? null;

                    $franchisesid = Franchise::query()->whereHas('pools', function ($q) use ($nearestPoolId) {
                        $q->where('id', $nearestPoolId);
                    })->where('fs_on_off', 'online')->get(['id', 'franchises_name']);
                }

                if ($franchisesid->count() > 0) {
                    /** @var \Illuminate\Database\Eloquent\Collection<int,DeliveryPerson> $deliverypersonid */
                    $deliverypersonid = DeliveryPerson::query()
                        ->whereHas('schedules', function ($query) use ($pool_id, $franchisesid) {
                            /** @var \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Builder $query */
                            $stdate = date('Y-m-d');
                            $starttime = date('H:i:s');
                            $query->where('status', '2')
                                ->where('pool_id', $pool_id)
                                ->whereRaw('("'.$stdate.'" >= DATE(start_date) AND "'.$stdate.'" <= DATE(end_date)) && ( CAST(start_date AS TIME) <= "'.$starttime.'" AND   CAST(end_date AS TIME) >= "'.$starttime.'")')
                                ->where('franchise_id', $franchisesid[0]->id);
                        })
                        ->get(['id', 'dp_name']);

                    if (count($deliverypersonid) == 0) {
                        $deliverypersonid = DeliveryPerson::getDeliveryPersonFromPoolIdOrNearestDistance(
                            $pool_id,
                            $lat,
                            $long
                        );
                    }
                } else {
                    $deliverypersonid = DeliveryPerson::getDeliveryPersonFromPoolIdOrNearestDistance(
                        $pool_id,
                        $lat,
                        $long
                    );
                }
                $data['franchiseslist'] = Franchise::query()->where('fs_on_off', 'online')->get(['id', 'franchises_name']);
                $data['deliverypersonlist'] = DeliveryPerson::query()->where('dp_onoff', 'online')->get(['id', 'dp_name']);
                $data['deliverypersonid'] = count($deliverypersonid) > 0 ? $deliverypersonid[0]['id'] : '-';
                $data['franchisesid'] = count($franchisesid) > 0 ? $franchisesid[0]['id'] : '-';
                $data['dp_name'] = count($deliverypersonid) > 0 ? $deliverypersonid[0]['dp_name'] : '-';
                $data['franchises_name'] = count($franchisesid) > 0 ? $franchisesid[0]['franchises_name'] : '-';
                $data['disabled'] = (count($franchisesid) > 0 && count($deliverypersonid) > 0) ? '' : 'disabled';
                $data['checked'] = (count($franchisesid) > 0 && count($deliverypersonid) > 0) ? 'checked' : '';
            } else {

                $deliverypersonid = DeliveryPerson::getDeliveryPersonFromPoolIdOrNearestDistance(
                    $pool_id,
                    $lat,
                    $long
                );
                $data['franchiseslist'] = Franchise::query()->where('fs_on_off', 'online')->get(['id', 'franchises_name']);
                $data['deliverypersonlist'] = DeliveryPerson::query()->where('dp_onoff', 'online')->get(['id', 'dp_name']);
                $data['deliverypersonid'] = count($deliverypersonid) > 0 ? $deliverypersonid[0]['id'] : '-';
                $data['franchisesid'] = '-';
                $data['dp_name'] = count($deliverypersonid) > 0 ? $deliverypersonid[0]['dp_name'] : '-';
                $data['franchises_name'] = '-';
                $data['disabled'] = 'disabled';
                $data['checked'] = '';
            }
        } else {
            $data['franchiseslist'] = Franchise::query()->where('fs_on_off', 'online')->get(['id', 'franchises_name']);
            $data['deliverypersonlist'] = DeliveryPerson::query()->where('dp_onoff', 'online')->get(['id', 'dp_name']);
            $data['deliverypersonid'] = '-';
            $data['franchisesid'] = '-';
            $data['dp_name'] = '-';
            $data['franchises_name'] = '-';
            $data['disabled'] = 'disabled';
            $data['checked'] = '';
        }

        return view('modal.showApprovedPopup', $data);
    }

    public function orderApproved(Request $request)
    {
        $order_id = $request->input('oid');
        if ($request->input('customCheck1') == null) {
            $franchises = $request->input('franchises');
            $deliveryperson = $request->input('deliveryperson');
        } else {
            $franchises = $request->input('fid');
            $deliveryperson = $request->input('did');
        }

        if ($request->input('customCheck1') == null) {
            $rules = [
                'franchises'     => 'required',
                'deliveryperson' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()
                    ->json([
                        'status' => false,
                        'type'   => 'validation',
                        'errors' => $validator->errors(),
                    ]);
            }
        }

        /** @var Order $orderdetail */
        $orderdetail = Order::findOrFail($order_id);

        /** @var Franchise $franchise */
        $franchise = Franchise::findOrFail($franchises);

        /** @var DeliveryPerson $devpersondetail */
        $devpersondetail = DeliveryPerson::findOrFail($deliveryperson);

        $orderdetail->update(['delivery_person_id' => $deliveryperson, 'order_status' => OrderStatusEnum::APPROVED, 'od_assigned_time' => now()]);

        /** notification To Franchise*/
        $message = 'Order No '.$order_id.'Assigned to you';
        $notification = new Notification;
        $notification->user_type = 'franchise';
        $notification->to_id = $franchises;
        $notification->text = $message;
        $notification->save();
        /** notification To Delivery Person*/
        $notification = new Notification;
        $notification->user_type = 'delivery_person';
        $notification->to_id = $deliveryperson;
        $notification->text = $message;
        $notification->save();

        /** push notification */
        $mess = $franchise->franchises_name.' has assigned order order no. '.$order_id.' to you';
        $push = new PushNotification('fcm');
        $notif = $push->setMessage([
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
            ->setDevicesToken($devpersondetail->dp_device_token)
            ->send()
            ->getFeedback();

        // logger("log notif approve order ({$order_id})", ['$notif' => $notif]);

        if ($orderdetail->order_deliverect_id != '') {
            Deliverect::deliverectOrderStatus($orderdetail->order_deliverect_id, $orderdetail['order_receipt_id'], OrderStatusEnum::APPROVED->value);
        }

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Order Approved!!',
                'page'   => 'customer_service/order/view/'.$order_id,
            ]);
    }

    public function ReassignPopup(Request $request)
    {

        $data['oid'] = $request->id;
        $order = Order::findOrFail($data['oid']);

        $data['franchisesid'] = $order->franchise_id;
        $data['deliverypersonid'] = $order->delivery_person_id;

        $data['franchiseslist'] = Franchise::where('fs_on_off', 'online')->get();
        $data['deliverypersonlist'] = DeliveryPerson::where('dp_onoff', 'online')->get();

        return view('modal.showReassignPopup', $data);
    }

    public function Reassign(Request $request)
    {
        $order_id = $request->input('oid');
        $franchises = $request->input('franchises');
        $deliveryperson = $request->input('deliveryperson');
        $rules = [
            'franchises'     => 'required',
            'deliveryperson' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        }

        /** @var Order $orderdetail */
        $orderdetail = Order::findOrFail($order_id);

        $orderdetail->update(['id' => $franchises, 'delivery_person_id' => $deliveryperson, 'order_status' => 2, 'od_assigned_time' => now()]);
        /** notification To Franchise*/
        $message = 'Order No '.$order_id.' Assigned to you';
        $notification = new Notification;
        $notification->user_type = 'franchise';
        $notification->to_id = $franchises;
        $notification->text = $message;
        $notification->save();
        /** notification To Delivery Person*/
        $notification = new Notification;
        $notification->user_type = 'delivery_person';
        $notification->to_id = $deliveryperson;
        $notification->text = $message;
        $notification->save();

        /** push notification */
        $franchise = Franchise::find($franchises);
        $devpersondetail = DeliveryPerson::find($deliveryperson);
        $mess = $franchise['franchises_name'].' has assigned order orer no. '.$order_id.' to you';
        $push = new PushNotification('fcm');
        $notif = $push->setMessage([
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
            ->setDevicesToken($devpersondetail->dp_device_token)
            ->send()
            ->getFeedback();
        // logger("log notif reassign order ({$order_id})", ['$notif' => $notif]);

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Reassigned!!',
                'page'   => 'customer_service/order/list',
            ]);

    }
}
