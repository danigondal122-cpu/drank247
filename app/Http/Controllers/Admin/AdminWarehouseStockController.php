<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class AdminWarehouseStockController extends Controller
{
    public function index()
    {
        $data['franchise'] = Franchise::orderBy('fs_on_off', 'ASC')->orderBy('franchises_name', 'ASC')->get();
        $data['warehouse'] = Warehouse::all();
        // $data['product']=Products::select('product_id','product_name','product_article_number')->whereNull('deleted_at')->get();

        $data['product'] = Product::select('products.id as id', 'product_name', 'product_article_number')
            ->join('categories', 'categories.id', 'products.category_id')
            ->leftjoin('warehouses', 'warehouses.id', 'products.order_from')
            ->whereNull('products.deleted_at')
            ->whereNull('categories.deleted_at')
            ->orderBy('id', 'asc')
            ->get();

        $fran_array[] = [];
        foreach ($data['product'] as $key => $value) {
            /** @var Product $value */
            // $data['product_name']=$value['product_name'];
            // $data['product_article_number']=$value['product_article_number'];
            $data['product'][$key]['product_name'] = $value['product_name'];
            $data['product'][$key]['product_article_number'] = $value['product_article_number'];
            // $data['product'][$key]['franchise_id']=1;

            foreach ($data['franchise'] as $key1 => $value1) {
                /** @var Franchise $value1 */
                /** @var Stock $stock_detail */
                $stock_detail = Stock::query()->where('product_id', $value->id)->where('franchise_id', $value1->id)->first();

                if ($stock_detail) {
                    $fran_array[$key1]['max_stock_order'] = $stock_detail->max_stock_order;
                    $fran_array[$key1]['stock_minimum'] = $stock_detail->stock_minimum;
                    $fran_array[$key1]['stock_current'] = $stock_detail->stock_current;
                    $fran_array[$key1]['franchise_id'] = $value1->id;
                } else {
                    $fran_array[$key1]['max_stock_order'] = 0;
                    $fran_array[$key1]['stock_minimum'] = 0;
                    $fran_array[$key1]['stock_current'] = 0;
                    $fran_array[$key1]['franchise_id'] = $value1->id;
                }
            }

            $data['product'][$key]['franchise'] = $fran_array;
        }

        return view('admin.warehousestock.list', $data);
    }

    public function changeStock(Request $request)
    {
        $product_fran_id = $request->input('product_fran_id');
        $explode = explode('&', $product_fran_id);
        $product_id = $explode[0];
        $franchise_id = $explode[1];

        Product::query()->findOrFail($product_id, ['id']);
        Franchise::query()->findOrFail($franchise_id, ['id']);

        $type = $request->input('type');
        $value = $request->input('value');
        if ($type == 'min_stock') {
            $col = 'stock_minimum';
        } elseif ($type == 'max_stock_order') {
            $col = 'max_stock_order';
        } else {
            $col = 'stock_current';
        }
        $isExist = Stock::where('product_id', $product_id)->where('franchise_id', $franchise_id)->first();
        if ($isExist) {
            Stock::where('product_id', $product_id)->where('franchise_id', $franchise_id)->update([$col => $value]);
        } else {
            $stock = new Stock;
            $stock->product_id = $product_id;
            $stock->franchise_id = $franchise_id;
            if ($type == 'min_stock') {
                $stock->stock_minimum = $value;
            } elseif ($type == 'max_stock_order') {
                $stock->max_stock_order = $value;
            } else {
                $stock->stock_current = $value;
            }

            $stock->save();
        }
    }
}
