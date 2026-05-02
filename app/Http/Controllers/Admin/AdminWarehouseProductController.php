<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class AdminWarehouseProductController extends Controller
{
    public function index()
    {
        $data['products'] = Product::whereNull('deleted_at')->get();
        $data['warehouse'] = WareHouse::whereNull('deleted_at')->get();

        return view('admin.warehouseproduct.list', $data);
    }

    public function getList(Request $request)
    {
        $query = Product::select('products.*')
            ->leftJoin('categories', 'categories.id', 'products.category_id')
            ->join('warehouses', 'warehouses.id', 'products.order_from')
            ->whereNull('categories.deleted_at')->whereNull('products.deleted_at');

        if ($request->get('order_from') != null) {
            $query = $query->where('products.order_from', $request->get('order_from'));
        }

        $column_order = ['product_name', 'product_article_number']; //set column field database for datatable orderable
        $column_search = ['product_name', 'product_article_number'];
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
            $query = $query->orderBy('products.id', 'desc');
        }

        $total = $query->get()->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        // dd($data);
        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function changeProductPrice(Request $request)
    {
        $product_id = $request->input('product_id');
        $type = $request->input('type');
        $value = $request->input('value');
        if ($type == 'main_price') {
            $col = 'main_price';
        } elseif ($type == 'drank247_price') {
            $col = 'drank247_price';
        } elseif ($type == 'customer_price') {
            $col = 'customer_price';
        } else {
            $col = 'franchise_price';
        }

        return response()->json([
            'rows' => Product::query()->where('id', $product_id)->update([$col => $value]),
        ]);
    }
}
