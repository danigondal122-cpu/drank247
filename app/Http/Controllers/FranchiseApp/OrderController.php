<?php

namespace App\Http\Controllers\FranchiseApp;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TakeawayController;

use App\Models\Franchise;
use App\Models\Product;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\OrderStatus;
use App\Models\UberStore;
use App\Services\Deliverect;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getOrders(Request $request)
    {
        $id = $request->input('id');
        $language = $request->input('language');

        $query = Order::select(
            'order_id',
            'orders.created_at',
            'customer_name',
            'franchises_name',
            'dp_name',
            'order_channel_order_id',
            'order_payment_status',
            'order_status',
            'order_id',
            'order_cancelled_reason',
            'os_name',
            'order_final_with_discount',
            'channel_id',
            'channel_image',
            'channel_name',
            'payment_method',
            'customers.customer_contact_no',
            'customers.customer_email',
            'order_address_id',
            'address',
            'post_code',
            'house_no',
            'name',
            'order_store_id',
            'order_uber_id',
            'order_uber_display_id',
            'order_takeaway_id',
            'order_takeaway_key',
            'order_takeaway_public_ref'

        )->leftJoin(
            'franchises',
            function ($join) {
                $join->on('franchises.id', '=', 'orders.franchise_id');
            }
        )->leftJoin(
            'address',
            function ($join) {
                $join->on('address.address_id', '=', 'orders.order_address_id');
            }
        )->leftJoin(
            'customers',
            function ($join) {
                $join->on('customers.customer_id', '=', 'orders.order_customerid');
            }
        )->leftJoin(
            'deliveryperson',
            function ($join) {
                $join->on('deliveryperson.dp_id', '=', 'orders.delivery_person_id');
            }
        )->leftJoin(
            'channel',
            function ($join) {
                $join->on('channel.channel_id', '=', 'orders.order_channel_id');
            }
        )->leftJoin(
            'uber_stores',
            function ($join) {
                $join->on('uber_stores.store_id', '=', 'orders.order_store_id');
            }
        )->join('order_status', 'order_status.os_id', 'orders.order_status')

            ->whereNotIn('order_status', ['0', '11', '7'])
            ->whereNull('orders.deleted_at')
            ->orderBy('order_id', 'DESC');
        if ($request->input('page') == 'order-list') {
            $franchiseData = Franchise::find($id);
            $query = $query->where('franchises.bank_account', $franchiseData->bank_account);
        } else {
            $query = $query->where('franchise_id', $id);
        }
        if ($request->input('status') && $request->input('status') != '') {
            $query = $query->where('orders.order_status', $request->input('status'));
        }
        $detail = $query->get()->toArray();
        if ($detail) {
            $message = ($language == 'nl') ?  'Succesvol inloggen'  : 'Data listed Successfully!!';
            return response()->json(['status' => true, 'message' => $message, 'data' => $detail]);
        } else {
            $message = ($language == 'nl') ?  'Email bestaat al'  : 'No records available';
            return response()->json(['status' => false, 'message' => $message]);
        }
    }
    public function updateOrderstatus(Request $request)
    {
        $id = $request->input('id');
        $language = $request->input('language');
        $order_id = $request->input('order_id');
        $value = $request->input('value');
        //Order::where('order_id', $order_id)->update(['order_status' => $value]);
        $ordercolor = OrderStatus::find($value);

        $orderdetail = Order::find($order_id);
        if ($orderdetail['order_deliverect_id'] != "") {
            Deliverect::updateOrderStatus($orderdetail['order_deliverect_id'], $orderdetail['order_receiptid'], $value);
        }
        if ($orderdetail['order_takeaway_id'] != "") {
            if ($value == 1 || $value == 4 || $value == 6 || $value == 10) {
                TakeawayController::takeawayOrderStatus($orderdetail['order_takeaway_id'], $value, $orderdetail['order_takeaway_key']);
            }
        }
        if ($orderdetail['order_uber_id'] != "" && $orderdetail['order_store_id'] != "") {
            if ($value == 6 || $value == 12  ||  $value == 10) {
                if ($orderdetail['uber_order_delivery_type'] == 'DELIVERY_BY_RESTAURANT') {
                    UberStore::updateUberOrderStatus($orderdetail['order_uber_id'], $value);
                }
            }
            if ($value == 11 || $value == 7 || $value == 8) {
                UberStore::cancelUberOrder($orderdetail['order_uber_id']);
            }
        }

        return response()
            ->json([
                'status' => true,
                'message' => 'Status Changed Successfully!',
                'id' => $order_id,
                'color' => $ordercolor['os_color'],
            ]);
    }
    public function orderView(Request $request)
    {
        $order_id = $request->input('order_id');
        $language = $request->input('language');
        $data['row'] = [];
        if ($order_id) {
            $data['row'] = Order::select(
                'order_id',
                'orders.created_at',
                'customer_name',
                'franchises_name',
                'franchise_id',
                'delivery_person_id',
                'dp_name',
                'order_channel_order_id',
                'order_payment_status',
                'order_status',
                'order_id',
                'order_cancelled_reason',
                'order_price',
                'order_delivery_charge',
                'order_finalamount',
                'promo_code_id',
                'order_discount',
                'os_name',
                'order_final_with_discount',
                'order_receiptid',
                'customer_email',
                'phone_code',
                'customer_contact_no',
                'post_code',
                'address',
                'franchises_email',
                'dp_email',
                'dp_contact_no',
                'order_discount',
                'order_note',
                'order_channel_id',
                'channel_image',
                'od_endtime',
                'od_starttime',
                'order_deliverytime',
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
            )->leftJoin(
                'franchises',
                function ($join) {
                    $join->on('franchises.id', '=', 'orders.franchise_id');
                }
            )->leftJoin(
                'customers',
                function ($join) {
                    $join->on('customers.customer_id', '=', 'orders.order_customerid');
                }
            )->leftJoin(
                'address',
                function ($join) {
                    $join->on('address.address_id', '=', 'orders.order_address_id');
                }
            )->leftJoin(
                'deliveryperson',
                function ($join) {
                    $join->on('deliveryperson.dp_id', '=', 'orders.delivery_person_id');
                }
            )->leftJoin(
                'channel',
                function ($join) {
                    $join->on('channel.channel_id', '=', 'orders.order_channel_id');
                }
            )->leftJoin(
                'uber_stores',
                function ($join) {
                    $join->on('uber_stores.store_id', '=', 'orders.order_store_id');
                }
            )->join('order_status', 'order_status.os_id', 'orders.order_status')
                ->whereNull('orders.deleted_at')
                ->where('orders.order_id', $order_id)->first();

            $explode = explode(',', $data['row']['od_rejected_id']);
            $data['last_dp_id'] = $explode[array_key_last($explode)];
            $data['orderdetail'] = Product::join('order_details', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $order_id)->get();
            $data['rejected_by'] = DeliveryPerson::whereIn('dp_id', $explode)->get();

            return response()
                ->json([
                    'status' => true,
                    'data' => $data,
                ]);
        } else {
            return response()
                ->json([
                    'status' => false,
                    'message' => 'Something is wrong!',
                ]);
        }
    }
}
