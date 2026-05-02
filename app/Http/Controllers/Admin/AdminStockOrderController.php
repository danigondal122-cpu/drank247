<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\StockOrder;
use App\Models\StockOrderDetail;
use Illuminate\Http\Request;

class AdminStockOrderController extends Controller
{
    public function stockOrderList()
    {
        $data['Franchise'] = Franchise::whereNull('deleted_at')->get();

        return view('admin.stockorder.orderlist', $data);
    }

    public function stockOrderView($id)
    {
        $data['row'] = [];
        if ($id) {
            $data['row'] = StockOrder::leftJoin('franchises', function ($join) {
                $join->on('franchises.id', '=', 'stock_orders.franchise_id');
            })->where('stock_orders.id', $id)->firstOrFail(['stock_orders.*', 'franchises.franchises_name']);

            $data['orderdetail'] = StockOrderDetail::leftJoin('products', 'products.id', 'stock_order_details.product_id')->where('stock_order_id', $data['row']->id)->get(['stock_order_details.product_id', 'product_name', 'product_article_number', 'qty']);
        }

        return view('admin.stockorder.viewstockorder', $data);
    }

    public function getList(Request $request)
    {
        $query = StockOrder::select('franchises.id', 'franchises.franchises_name', 'stock_orders.id', 'order_reference', 'order_status', 'order_to', 'stock_orders.created_at', 'stock_orders.updated_at')->join('franchises', 'franchises.id', 'stock_orders.franchise_id')->Where(
            function ($query) {
                return $query
                    ->Where('order_to', '=', '1')
                    ->orWhere('order_to', '=', '2')
                    ->orWhere(
                        function ($query2) {
                            return $query2
                                ->where('order_to', '=', '0')
                                ->where('order_reference', '!=', '""');
                        }
                    );
            }
        );
        if ($request->get('frs_id') && $request->get('frs_id') != '') {

            $query = $query->where('stock_orders.franchise_id', $request->get('frs_id'));
        }

        if ($request->get('order_to') != '') {
            $query = $query->where('stock_orders.order_to', $request->get('order_to'));
            if ($request->get('order_to') == '0') {
                $query = $query->where('order_reference', '!=', '""');
            }
        }
        $column_order = ['stock_orders.id', 'order_reference', 'stock_orders.created_at', 'pickup_delivery_date']; //set column field database for datatable orderable
        $column_search = ['order_reference', 'order_reference', 'stock_orders.created_at', 'pickup_delivery_date', 'franchises_name']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;
        //Search
        if ($request->search['value'] && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }
        //Sorting
        // dd($request->order[0]['column']);
        if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('stock_orders.id', 'DESC');
        }
        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }
}
