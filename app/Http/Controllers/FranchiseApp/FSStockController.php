<?php

namespace App\Http\Controllers\FranchiseApp;

use App\Http\Controllers\Controller;

use App\Models\Stock;
use App\Models\Warehouse;

use Illuminate\Http\Request;

class FSStockController extends Controller
{
    public function getList(Request $request)
    {
        $id = $request->input('id');
        $query = Stock::leftjoin('franchises', 'franchises.id', 'stocks.franchise_id')
            ->leftjoin('products', 'products.product_id', 'stocks.stock_product')
            ->where('franchise_id', $id)
            ->whereNull('stocks.deleted_at');

        if ($request->input('order_from') != null) {
            $query = $query->where('products.order_from', $request->input('order_from'));
        }
        $column_search = ['product_name', 'max_stock_order', 'stock_current', 'stock_minimum']; //set column field database for datatable searchable
        $rawQuery = '';
        //Search
        if ($request->input('search') && $request->input('search') != '') {
            $search = $request->input('search');
            $i = 0;
            foreach ($column_search as $key => $value) {
                // dd($value);
                if ($i === 0) // first loop
                {
                    $rawQuery .= '('; // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $rawQuery .= $value . ' LIKE "%' . $search . '%"';
                } else {
                    $rawQuery .= ' OR ' . $value . ' LIKE "%' . $search . '%"';
                }
                if (count($column_search) - 1 == $i) {
                    //last loop
                    $rawQuery .= ')'; //close bracket
                }
                $i++;
            }
            $query = $query->whereRaw($rawQuery);
        }

        $detail = $query->get()->toArray();
        if ($detail) {
            return response()->json(['status' => true, 'data' => $detail]);
        } else {
            return response()->json(['status' => false, 'data' => '']);
        }
    }
    public function getWarehouse()
    {
        $warehouse = Warehouse::whereNull('deleted_at')->get();
        if ($warehouse) {
            return response()->json(['status' => true, 'data' => $warehouse]);
        } else {
            return response()->json(['status' => false, 'data' => '']);
        }
    }
}
