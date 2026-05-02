<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StockReminder;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Franchise;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AdminStockController extends Controller
{
    public function create()
    {
        $data['row'] = [];
        $data['categories'] = Category::orderBy('category_order', 'ASC')->whereNull('deleted_at')->get();
        $data['franchisee'] = Franchise::whereNull('deleted_at')->get();
        $data['products'] = Product::whereNull('deleted_at')->get();

        return view('admin.stock.create', $data);
    }

    public function edit($id)
    {
        $data['row'] = [];
        $data['categories'] = Category::orderBy('category_order', 'ASC')->whereNull('deleted_at')->get();
        $data['franchisee'] = Franchise::whereNull('deleted_at')->get();
        $data['products'] = Product::whereNull('deleted_at')->get();
        if ($id) {
            $data['row'] = Stock::findOrFail($id);
        }

        return view('admin.stock.create', $data);
    }

    public function index()
    {
        $data['Franchise'] = Franchise::whereNull('deleted_at')->get();

        return view('admin.stock.list', $data);
    }

    public function getList(Request $request)
    {
        $query = Stock::select('stocks.*', 'franchises.franchises_name', 'products.product_name')->join('products', 'products.id', '=', 'stocks.product_id')
            ->join('franchises', 'franchises.id', '=', 'stocks.franchise_id')
            ->whereNull('stocks.deleted_at');

        // Filter by franchise ID if provided
        if ($request->filled('frs_id')) {
            $query->where('stocks.franchise_id', $request->frs_id);
        }

        $column_order = ['product_name', 'franchises_name', 'stock_current', 'stock_minimum']; // Set column fields for ordering
        $column_search = ['product_name', 'franchises_name', 'stock_current', 'stock_minimum']; // Set column fields for searching
        $start_from = $request->start ?? 0; // Default to 0 if not set
        $per_page = $request->length ?? 10; // Default to 10 if not set
        $rawQuery = '';

        // Search
        if ($request->has('search') && ! empty($request->search['value'])) {
            $search = $request->search['value'];
            $rawQuery = '('.implode(' OR ', array_map(function ($column) use ($search) {
                return "$column LIKE '%$search%'";
            }, $column_search)).')';
            $query->whereRaw($rawQuery);
        }

        // Sorting
        if ($request->has('order') && isset($request->order[0]['column'])) {
            $columnIndex = $request->order[0]['column'];
            if (isset($column_order[$columnIndex])) {
                $query->orderBy($column_order[$columnIndex], $request->order[0]['dir'] ?? 'asc');
            }
        } else {
            $query->orderBy('stocks.id', 'DESC'); // Specify the table for the id column
        }

        $total = $query->count(); // Get total count
        $data = $query->skip($start_from)->take($per_page)->get(); // Get paginated data

        return response()->json([
            'data'  => $data,
            'total' => $total,
        ]);
    }

    public function save(Request $request)
    {
        $rules = [
            'product_name'   => 'required',
            'franchise_name' => 'required',
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
        } else {
            $qry = Stock::where('product_id', $request->input('product_name'))->where('franchise_id', $request->input('franchise_name'));
            if ($request->id) {
                $count = $qry->whereNotIn('id', [$request->id])->get()->count();
            } else {
                $count = $qry->count();
            }
            if ($count == 0) {
                if ($request->id) {
                    $message = 'Stock succesvol bijgewerkt';
                    $stock = Stock::find($request->id);
                } else {
                    $message = 'Stock succesvol toegevoegd';
                    $stock = new Stock;
                }
                $stock->product_id = $request->input('product_name');
                $stock->franchise_id = $request->input('franchise_name');
                // $stock->stock_category = $request->input('category_name');
                // $stock->stock_price = $request->input('price');
                $stock->stock_current = $request->input('current_stock');
                $stock->stock_minimum = $request->input('min_stock');
                $stock->is_reminder_set = ($request->input('is_reminder_set') == 'on') ? '1' : '0';

                $stock->save();
                if ($stock && ($request->input('current_stock') <= $request->input('min_stock')) && $request->input('is_reminder_set') == 'on') {
                    $admin = Admin::find(1);
                    $product = Product::find($request->input('product_name'));
                    $franchise = Franchise::find($request->input('franchise_name'));
                    $maildata['name'] = $admin['name'];
                    $maildata['email'] = $admin['email'];
                    $maildata['franchise'] = $franchise['franchises_name'];
                    $maildata['product'] = $product['product_name'];
                    $maildata['stock_minimum'] = $request->input('min_stock');
                    $maildata['newstock'] = $request->input('current_stock');
                    Mail::to($admin['email'])
                        ->send(new StockReminder($maildata));
                }
            } else {
                return response()
                    ->json([
                        'status' => false,
                        'type'   => 'validation',
                        'errors' => [
                            'product_name' => ['You have already added this product for Franchise'],
                        ],
                    ]);
            }
        }

        return response()
            ->json([
                'status' => true,
                'msg'    => $message,
                'page'   => 'admin/stock/list',
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
        } else {
            Stock::where('id', $request->id)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Stock deleted !',
                ]);
        }
    }

    public function getCategory(Request $request)
    {

        $category = Product::where('product_id', $request->id)->first();
        $category_id = $category['category_id'];

        return response()
            ->json([
                'status'      => true,
                'category_id' => $category_id,
            ]);
    }
}
