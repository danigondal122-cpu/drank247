<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPerson;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class FSHoursController extends Controller
{
    public function hourslist()
    {
        $data['delivery'] = DeliveryPerson::get();
        $data['status_list'] = OrderStatus::whereIn('id', [5, 6, 11, 8])->get();

        return view('franchise.hours.list', $data);
    }

    public function getList(Request $request)
    {
        $fid = auth('franchise')->id();

        $query = Order::select([
            'orders.id',
            'orders.od_start_time',
            'orders.od_end_time',
            'delivery_people.dp_name',
        ])
            ->leftJoin('delivery_people', function ($join) {
                $join->on('delivery_people.id', '=', 'orders.delivery_person_id');
            })->join('order_statuses', 'order_statuses.id', 'orders.order_status')->where('order_status', '!=', '0')->whereNull('orders.deleted_at')
            ->where('franchise_id', $fid)
            ->where('order_status', '10');

        if ($request->get('delivery_id') && $request->get('delivery_id') != '') {
            $query = $query->where('orders.delivery_person_id', $request->get('delivery_id'));
        }
        if ($request->get('date') && $request->get('date') != '') {

            $explode = explode('-', $request->get('date'));
            $startdate = str_replace('/', '-', $explode[0]);
            $enddate = str_replace('/', '-', $explode[1]);
            $startdate = date('Y-m-d', strtotime($startdate));
            $enddate = date('Y-m-d', strtotime($enddate));
            $query = $query->whereBetween('od_start_time', [$startdate, $enddate]);
        }
        $column_order = ['orders.id', 'dp_name']; //set column field database for datatable orderable
        $column_search = ['orders.id', 'dp_name']; //set column field database for datatable searchable
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
            $query = $query->orderBy('orders.id', 'DESC');
        }
        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        $totalHours = 0;
        foreach ($data as $key => $value) {
            $totalHours += $value['TotalOrderTimeINM'];
        }

        return response()
            ->json([
                'data'       => $data,
                'total'      => $total,
                'TotalHours' => gmdate('H:i:s', $totalHours),
            ]);
    }
}
