<?php

namespace App\Http\Controllers\Franchise;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\DeliveryPerson;
use App\Models\Franchise;
use App\Models\InvoicePdf;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Stock;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FSOrderController extends Controller
{
    public function orderList()
    {
        $data['Franchise'] = Franchise::whereNull('deleted_at')->get();
        $data['status_list'] = OrderStatus::whereIN('id', OrderStatusEnum::getOrderStatusForFranchise())->get();
        $data['monthlist'] = [
            1  => 'Jan',
            2  => 'Feb',
            3  => 'Mar',
            4  => 'Apr',
            5  => 'May',
            6  => 'Jun',
            7  => 'Jul',
            8  => 'Aug',
            9  => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];

        return view('franchise.order.list', $data);
    }

    public function getList(Request $request)
    {
        // print_r(Session::get('fs_week'));die;
        session()->forget('fs_status');
        session()->forget('fs_month');
        session()->forget('fs_week');
        session()->forget('fs_year');
        session()->forget('fs_startpage');
        session()->forget('fs_perpage');
        session()->forget('fs_searchvalue');

        /** @var Franchise $franchiseData */
        $franchiseData = auth('franchise')->user();
        $fid = $franchiseData->id;
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
            'customer_addresses.address',
            'house_no',
            'name',
            'order_store_id',
            'order_uber_id',
            'order_uber_display_id',
            'order_takeaway_id',
            'order_takeaway_key',
            'order_takeaway_public_ref'
        )
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
            })->join('order_statuses', 'order_statuses.id', 'orders.order_status')->whereNotIn('order_status', [
                OrderStatusEnum::ORDER_CREATED,
                OrderStatusEnum::REJECTED,
                OrderStatusEnum::CANCELED,
            ])->whereNull('orders.deleted_at');
        // ->where('franchise_id', $fid);

        if ($request->get('page') == 'order-list') {
            $query = $query->where('franchises.bank_account', $franchiseData->bank_account);
        } else {
            $query = $query->where('franchise_id', $fid);
        }

        if ($request->get('year') && $request->get('year') != '' && $request->get('week') == '') {
            Session::put('fs_year', $request->get('year'));
            $query = $query->whereRaw("(YEAR(orders.created_at) = '".$request->get('year')."')");
        }

        if ($request->get('status') && $request->get('status') != '') {
            Session::put('fs_status', $request->get('status'));
            $query = $query->where('orders.order_status', $request->get('status'));
        }

        if ($request->get('month') && $request->get('month') != '' && $request->get('week') == '') {
            Session::put('fs_month', $request->get('month'));
            $query = $query->whereRaw("(MONTH(orders.created_at) = '".$request->get('month')."')");
        }

        if ($request->get('week') && $request->get('week') != '') {
            Session::put('fs_week', $request->get('week'));
            $week = explode('/', $request->get('week'));

            $start_from = $week[0];
            $start_from = Carbon::parse($start_from, Session::get('time_zone'))
                ->startOfDay()
                ->setTimezone('UTC');
            $end_to = $week[1];
            $end_to = Carbon::parse($end_to, Session::get('time_zone'))
                ->endOfDay()
                ->setTimezone('UTC');

            $query = $query->where('orders.created_at', '>=', $start_from)->where('orders.created_at', '<=', $end_to);
            // $query = $query->whereBetween("orders.created_at", [$start_from, $end_to]);
        }

        $column_order = ['channel_image', 'orders.id', 'orders.created_at', 'customer_name', 'franchises_name', 'dp_name', 'order_channel_order_id', 'order_final_with_discount']; //set column field database for datatable orderable
        $column_search = ['orders.id', 'orders.created_at', 'customer_name', 'franchises_name', 'dp_name', 'order_channel_order_id', 'order_final_with_discount', 'customers.customer_contact_no', 'customers.customer_email', 'customer_addresses.address', 'house_no']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;

        Session::put('fs_startpage', $request->start);
        Session::put('fs_perpage', $request->length);
        //Search
        if ($request->search['value'] && $request->search['value'] != '') {
            Session::put('fs_searchvalue', $request->search['value']);
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }
        //Sorting
        if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('orders.id', 'DESC');
        }

        $total = $query->count();
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $data */
        $data = $query->skip($start_from)->limit($per_page)->get();
        // print_r($data);die;
        $order_total_amount = 0;
        foreach ($data as $row) {
            $order_total_amount += $row->order_final_with_discount;
        }

        return response()
            ->json([
                'data'               => $data,
                'total'              => $total,
                'total_order_amount' => $order_total_amount,
            ]);
    }

    public function orderView(Order $id)
    {
        $data['row'] = $id;

        $explode = explode(',', $id->od_rejected_id);
        $data['last_dp_id'] = $explode[array_key_last($explode)];
        $data['order_details'] = $id->orderDetails->load('product');
        $data['rejected_by'] = DeliveryPerson::whereIn('id', $explode)->get();

        return view('franchise.order.view', $data);
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
}
