<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class FSController extends Controller
{
    public function adminDashboard()
    {
        $franchise_id = auth('franchise')->id();
        $data['total_ordercount'] = Order::where('franchise_id', $franchise_id)->get()->count();
        $data['todays_ordercount'] = Order::where('franchise_id', $franchise_id)->whereDay('created_at', now()->day)->get()->count();

        $data['total_customercount'] = Customer::get()->count();
        $data['todays_customercount'] = Customer::whereDay('created_at', now()->day)->get()->count();

        $data['total_revenue'] = Order::where('franchise_id', $franchise_id)->sum('order_final_amount');
        $data['todays_revenue'] = Order::where('franchise_id', $franchise_id)->whereDay('created_at', now()->day)->sum('order_final_amount');

        $data['popular_products'] = Product::query()
            ->select(['products.id', 'products.product_name', 'products.image'])
            ->withCount(['orderDetails as order_count' => function (Builder|QueryBuilder $query) use ($franchise_id) {
                $query->whereIn('order_details.order_id', Order::where('franchise_id', $franchise_id)->select('id'));
            }])
            ->having('order_count', '>', 0)
            ->orderByDesc('order_count')
            ->get();

        // $data['popular_products'] = DB::table('order_details')
        //     ->select(array('order_details.od_orderid', 'products.product_id','products.product_name','products.image', DB::raw('COUNT(products.product_id) as product_count')))
        //     ->whereIn('order_details.od_orderid', $order_ids)
        //     ->leftjoin("products","products.product_id","=","order_details.od_productid")
        //     ->groupBy('products.product_id')
        //     ->orderBy('product_count', 'desc')
        //     ->get();
        // return ($data['popular_products']);

        $html = '';
        $year_start = 2015;
        $year_end = date('Y'); // current Year
        $selected_year = date('Y');
        for ($i_year = $year_start; $i_year <= $year_end; $i_year++) {
            $selected = $selected_year == $i_year ? ' selected' : '';
            $html .= "<option value='$i_year' $selected>".$i_year.'</option>';
        }
        $data['years'] = $html;

        return view('franchise.dashboard', $data);
    }
}
