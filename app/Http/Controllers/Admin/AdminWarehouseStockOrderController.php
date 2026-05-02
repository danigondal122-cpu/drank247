<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\FranchiseStockOrder;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminWarehouseStockOrderController extends Controller
{
    public function getOrderList(Request $request)
    {
        $query = FranchiseStockOrder::select(
            DB::raw('count(*) as total'),
            'franchise_stock_orders.order_id', // Include only necessary columns
            'franchise_stock_orders.warehouse_id',
            'franchise_stock_orders.franchise_id',
            'warehouses.wh_name as warehouse_name', // Example of specific column
            'franchises.franchises_name as franchise_name'  // Example of specific column
        )
            ->leftJoin('warehouses', 'warehouses.id', '=', 'franchise_stock_orders.warehouse_id')
            ->leftJoin('franchises', 'franchises.id', '=', 'franchise_stock_orders.franchise_id')
            ->groupBy('franchise_stock_orders.order_id', 'franchise_stock_orders.warehouse_id', 'franchise_stock_orders.franchise_id', 'warehouses.wh_name', 'franchises.franchises_name'); // Added necessary columns to group by
        // Corrected to use the correct column name

        // Filter by order source
        if ($request->filled('order_from')) {
            $query->where('franchise_stock_orders.warehouse_id', $request->order_from);
        }

        // Filter by franchise ID
        if ($request->filled('franchise_id')) {
            $query->where('franchise_stock_orders.franchise_id', $request->franchise_id);
        }

        // Set column order and search fields
        $columnOrder = ['order_id', 'franchises.franchises_name', 'warehouses.wh_name']; // Updated column names
        $columnSearch = ['order_id', 'franchises.franchises_name', 'warehouses.wh_name'];

        // Search functionality
        if ($request->filled('search.value')) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search, $columnSearch) {
                foreach ($columnSearch as $column) {
                    $q->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }

        // Sorting
        if ($request->filled('order.0.column')) {
            $query->orderBy($columnOrder[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query->orderBy('order_id', 'DESC');
        }

        // Pagination
        $total = $query->count();
        $data = $query->skip($request->start)->take($request->length)->get();

        return response()->json([
            'data'  => $data,
            'total' => $total,
        ]);
    }

    public function wareHouseStockOrderList()
    {
        $data['Franchise'] = Franchise::whereNull('deleted_at')->get();
        $data['warehouse'] = Warehouse::whereNull('deleted_at')->get();
        $data['products'] = Product::whereNull('deleted_at')->where('product_article_number', '!=', '')->get();
        $data['row'] = [];

        return view('admin.warehousestockorder.list', $data);
    }

    public function wareHouseStockOrderView($id)
    {

        /** @var \Illuminate\Database\Eloquent\Collection<int, FranchiseStockOrder> */
        $data['list'] = FranchiseStockOrder::leftjoin('products', 'franchise_stock_orders.product_id', 'products.id')
            ->leftjoin('warehouses', 'warehouses.id', 'franchise_stock_orders.id')
            ->where('order_id', $id)->get();
        $amount = 0;
        foreach ($data['list'] as $key => $value) {
            /** @var null|Stock $val */
            $val = Stock::where('product_id', $value['product_id'])->where('franchise_id', $value['franchise_id'])->first();
            $data['list'][$key]['price'] = number_format(($value->franchise_price * $val?->max_stock_order), '2', ',', '.');
            $amount += $value->franchise_price * $val?->max_stock_order;
        }
        $warehouse = $data['list'][0]->warehouse;
        $data['franchise'] = $data['list'][0]->franchise;
        $data['order_no'] = $id;
        $data['warehouse'] = $warehouse;
        $data['amount'] = number_format($amount, '2', ',', '.');
        $data['row'] = [];

        return view('admin.warehousestockorder.view', $data);
    }
}
