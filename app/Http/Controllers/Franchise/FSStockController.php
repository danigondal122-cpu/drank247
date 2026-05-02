<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Mail\StockOrderFromFranchise;
use App\Models\Category;
use App\Models\Franchise;
use App\Models\FranchiseStockOrder;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FSStockController extends Controller
{
    public function stockList()
    {
        $data['warehouse'] = Warehouse::whereNull('deleted_at')->get();
        $data['categories'] = Category::whereNull('deleted_at')->get();

        return view('franchise.stock.list', $data);
    }

    public function getList(Request $request)
    {
        $fid = auth('franchise')->id();
        $query = Stock::leftjoin('products', 'products.id', 'stocks.product_id')
            ->where('franchise_id', $fid)
            ->whereNull('stocks.deleted_at'); //$request->get('cat_id')

        $column_order = ['product_name', 'max_stock_order', 'stock_current', 'stock_minimum']; //set column field database for datatable orderable
        $column_search = ['stocks.id', 'products.product_name', 'max_stock_order', 'stock_current', 'stock_minimum']; //set column field database for datatable searchable
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
            $query = $query->orderBy('stocks.id', 'desc');
        }

        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function stockAdd()
    {
        $data['row'] = [];
        $data['categories'] = Category::whereNull('deleted_at')->get();
        $data['franchisee'] = Franchise::whereNull('deleted_at')->get();
        $data['products'] = Product::whereNull('deleted_at')->get();

        return view('franchise.stock.create', $data);
    }

    public function stockEdit(Stock $id)
    {
        $data['row'] = $id;
        $data['categories'] = Category::whereNull('deleted_at')->get();
        $data['franchisee'] = Franchise::whereNull('deleted_at')->get();
        $data['products'] = Product::whereNull('deleted_at')->get();

        return view('franchise.stock.create', $data);
    }

    public function save(Request $request)
    {
        $fid = auth('franchise')->id();

        $rules = [
            'product_name' => 'required',
            // 'frenchisee_name' => 'required',
            // 'category_name' => 'required',
            // 'price' => 'required|numeric',
            'min_stock'     => 'required|numeric',
            'current_stock' => 'required|numeric',
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

        $qry = Stock::query()->where('product_id', $request->input('product_name'))->where('franchise_id', $fid);
        if ($request->stock_id) {
            $count = $qry->whereNotIn('id', [$request->stock_id])->get()->count();
        } else {
            $count = $qry->count();
        }
        if ($count > 0) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => [
                        'product_name' => ['You have already added this product'],
                    ],
                ]);
        }

        if ($request->stock_id) {
            $message = 'Stock succesvol bijgewerkt';
            /** @var Stock $stock */
            $stock = Stock::find($request->stock_id);
        } else {
            $message = 'Stock succesvol toegevoegd';
            $stock = new Stock;
        }

        $stock->product_id = $request->input('product_name');
        $stock->franchise_id = $fid;
        // $stock->stock_category = $request->input('category_name');
        // $stock->stock_price = $request->input('price');
        $stock->stock_current = $request->input('current_stock');
        $stock->stock_minimum = $request->input('min_stock');
        $stock->is_reminder_set = ($request->input('is_reminder_set') == 'on') ? '1' : '0';
        $stock->save();

        return response()
            ->json([
                'status' => true,
                'msg'    => $message,
                'page'   => 'franchise/stock/list',
            ]);
    }

    public function deleteStock(Request $request)
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
        }

        Stock::find($request->id)->delete();

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Stock deleted !',
            ]);
    }

    public function getCategory(Request $request)
    {

        $category = Product::where('id', $request->id)->first();
        $category_id = $category['category_id'];

        return response()
            ->json([
                'status'      => true,
                'category_id' => $category_id,
            ]);
    }

    public function franchiseStockOrderFromList()
    {
        $data['warehouse'] = Warehouse::get();
        $data['products'] = Product::where('product_article_number', '!=', '')->get();
        $data['row'] = [];

        return view('franchise.franchisestockorderfrom.list', $data);
    }

    public function processFranchiseStockOrderFromList(Request $request)
    {

        $fid = auth('franchise')->id();
        $query = Stock::select('stocks.*', 'products.franchise_price as price', 'products.product_name', 'products.product_article_number')
            ->leftjoin('franchises', 'franchises.id', 'stocks.franchise_id')
            // ->join('categories','categories.category_id','stocks.stock_category')
            ->leftjoin('products', 'products.id', 'stocks.product_id')
            ->where('stocks.franchise_id', $fid)
            ->whereRaw('stock_current < stock_minimum');
        if ($request->get('order_from') != null) {
            $query = $query->where('products.order_from', $request->get('order_from'));
        }
        $column_order = ['product_name', 'product_article_number', 'max_stock_order', 'franchise_price', 'stock_current', 'stock_minimum']; //set column field database for datatable orderable
        $column_search = ['product_name', 'product_article_number', 'max_stock_order', 'franchise_price', 'stock_current', 'stock_minimum']; //set column field database for datatable searchable
        $start_from = $request->start ?? 0;
        $per_page = $request->length ?? 10;
        $rawQuery = '';
        //Search
        if ($request->search['value'] ?? false && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }
        //Sorting
        if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('stocks.id', 'DESC');
        }

        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();
        $amount = 0;
        foreach ($data as $key => $value) {
            $data[$key]['total'] = number_format(($value['price'] * $value['max_stock_order']), '2', ',', '.');
            $amount += $value->price * $value->max_stock_order;
        }

        return response()
            ->json([
                'data'   => $data,
                'total'  => $total,
                'amount' => number_format($amount, '2', ',', '.'),
            ]);
    }

    public function placeOrderforStock(Request $request)
    {
        $order_from = $request->order_from;
        $fid = auth('franchise')->id();
        $query = Stock::select('stocks.*', 'products.franchise_price as price', 'products.product_name', 'products.product_article_number', 'products.id', 'franchises.franchises_name')
            ->leftjoin('franchises', 'franchises.id', 'stocks.franchise_id')
            ->leftjoin('products', 'products.id', 'stocks.product_id')
            ->where('franchise_id', $fid)
            ->whereRaw('stock_current < stock_minimum')
            ->whereNull('stocks.deleted_at'); //$request->get('cat_id')
        $query = $query->orderBy('stocks.id', 'DESC');
        if ($order_from != null) {
            $query = $query->where('products.order_from', $order_from);
        }
        $data = $query->get();
        $amount = 0;
        foreach ($data as $key => $value) {
            $data[$key]['price'] = $value->price * $value->max_stock_order;
            $amount += $value->price * $value->max_stock_order;
        }
        $max_amount = Warehouse::query()->findOrFail($order_from);

        if ($amount >= $max_amount->wh_minprice) {
            Mail::to('247warehouse@247drank.nl')
                ->cc(['mital.vekariya@nexuslinkservices.in', 'hemangi.vekariya@nexuslinkservices.in'])
                ->send(new StockOrderFromFranchise(
                    data: $data,
                    amount: number_format($amount, '2', ',', '.'),
                    wh_name: $max_amount['wh_name'],
                    fr_name: $data['0']['franchises_name'],
                ));
            $order_id = FranchiseStockOrder::max('order_id');
            // dd($data);
            foreach ($data as $key => $value) {
                $order_data = new FranchiseStockOrder;
                $order_data->franchise_id = $fid;
                $order_data->product_id = $value->product_id;
                $order_data->fs_qty = $value->max_stock_order;
                $order_data->order_id = $order_id + 1;
                $order_data->warehouse_id = $order_from;
                $order_data->save();
            }

            return response()
                ->json([
                    'status'  => true,
                    'message' => 'Order has been sent',
                    'page'    => 'franchise/franchisestockorderfrom/FrStockOrderList',
                ]);
        } else {
            return response()
                ->json([
                    'status'  => false,
                    'message' => 'Your Order Amount less than Ware house Amount. So you can not order ',
                ]);
        }
    }

    public function FrStockOrderList()
    {
        $data['warehouse'] = Warehouse::get();
        $data['products'] = Product::where('product_article_number', '!=', '')->get();
        $data['row'] = [];

        return view('franchise.franchisestockorderfrom.orderlist', $data);
    }

    public function processFrStockOrderList(Request $request)
    {

        $fid = auth('franchise')->id();
        $query = Warehouse::select([
            'warehouses.*',
        ])
        ->withCount('franchiseStockOrders as total')
            ->withWhereHas('franchiseStockOrders', function ($query) use ($fid, $request) {
                $query->where('franchise_stock_orders.franchise_id', $fid)
                    ->select('id','order_id', 'warehouse_id', 'order_status');
                if ($request->get('order_from') != null) {
                    $query->where('franchise_stock_orders.warehouse_id', $request->get('order_from'));
                }
            });
        $column_order = ['order_id', 'wh_name']; //set column field database for datatable orderable
        $column_search = ['order_id', 'wh_name']; //set column field database for datatable searchable
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
            // $query = $query->orderBy('warehouses.id', 'DESC');
        }

        $total = $query->count();

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Warehouse> $data */
        $data = $query->skip($start_from)->limit($per_page)->get();
        foreach ($data as $key => $value) {
            $value->order_id = $value->franchiseStockOrders[0]->order_id;
            $all_count = $value->total;
            $completed_count = $value->franchiseStockOrders->where('order_status', 'COMPLETED')->count();
            if ($all_count == $completed_count) {
                $final_order_status = 'COMPLETED';
            } else {
                $final_order_status = 'PENDING';
            }
            $data[$key]['final_order_status'] = $final_order_status;
        }

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function changeOrderStatus(Request $request)
    {
        $f_id = auth('franchise')->id();
        $order_id = $request->id;

        $orderDetail = FranchiseStockOrder::query()->where('order_id', $order_id)->where('order_status', 'PENDING')->get();
        foreach ($orderDetail as $key => $value) {
            $stockdetail = Stock::where('product_id', $value->product_id)->where('franchise_id', $f_id)->get();
            $total = $stockdetail[0]['stock_current'] + $value->fs_qty;
            Stock::where('id', $stockdetail['0']['id'])->update(['stock_current' => $total]);
            $value->update(['order_status' => 'COMPLETED']);
        }

        return response()
            ->json([
                'status'  => true,
                'message' => 'Status Updated!!',
                'page'    => 'franchise/franchisestockorderfrom/FrStockOrderList',
            ]);
    }

    public function changeProductStatus(Request $request)
    {
        $f_id = auth('franchise')->id();
        $id = $request->id;
        $detail = FranchiseStockOrder::query()->findOrFail($id);

        $stockdetail = Stock::where('product_id', $detail['product_id'])->where('franchise_id', $f_id)->first();

        $detail->update(['order_status' => 'COMPLETED']);
        $total = $stockdetail['stock_current'] + $detail['fs_qty'];
        Stock::where('id', $stockdetail['id'])->update(['stock_current' => $total]);

        return response()
            ->json([
                'status'  => true,
                'message' => 'Status Updated!!',
                'page'    => 'franchise/franchisestockorderfrom/view/'.$detail['order_id'],
            ]);
    }

    public function FranchiseStockOrderView($id)
    {

        $data['list'] = FranchiseStockOrder::select([
            'franchise_stock_orders.*',
            'products.product_name',
            'products.product_article_number',
        ])
            ->leftjoin('products', 'franchise_stock_orders.product_id', 'products.id')
            ->leftjoin('warehouses', 'warehouses.id', 'franchise_stock_orders.warehouse_id')
            ->where('order_id', $id)->get();
        $amount = 0;
        foreach ($data['list'] as $key => $value) {
            $val = Stock::where('product_id', $value['product_id'])->where('franchise_id', $value['franchise_id'])->first();
            $data['list'][$key]['price'] = number_format(($value['franchise_price'] * $val['max_stock_order']), '2', ',', '.');
            $amount += $value['franchise_price'] * $val['max_stock_order'];
        }
        $warehouse = Warehouse::where('id', $data['list'][0]['warehouse_id'])->first();
        $data['order_no'] = $id;
        $data['warehouse'] = $warehouse;
        $data['amount'] = number_format($amount, '2', ',', '.');
        $data['row'] = [];

        return view('franchise.franchisestockorderfrom.viewstockorder', $data);
    }
}
