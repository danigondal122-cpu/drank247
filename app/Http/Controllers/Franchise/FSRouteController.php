<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Franchise\FSNotificationController;

use App\Models\Category;
use App\Models\Franchise;
use App\Models\Product;
use App\Models\Delivery;
use App\Models\DeliveryHistory;
use App\Models\Pool;
use App\Models\Stock;
use App\Models\Order;
use App\Models\Schedule;
use App\Models\OrderStatus;
use App\Models\SubDeliveryPerson;
use App\Models\DeliveryImage;
use App\Models\StockOrder;
use App\Models\StockOrderDetail;
use App\Models\WareHouse;
use App\Models\FranchiseStockOrder;
use App\Models\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FSRouteController extends FSNotificationController
{
  public function admin_login()
  {

    if (auth('franchise')->check()) {
      return redirect('/franchise/dashboard');
    }
    return view('franchise.auth.login');
  }
  public function adminChangePassword()
  {
    return view('franchise.auth.change_password');
  }
  public function forgotPassword()
  {

    return view('franchise.auth.forgot_password');
  }
  public function resetPassword(Request $request)
  {
    $data['token'] = $request->token;
    $data['id'] = $request->id;
    $data['row'] = Franchise::where('reset_token', $data['token'])->where('franchise_id', $data['id'])->first();
    return view('franchise.auth.reset_password', $data);
  }
  public function profile()
  {
    $data['row'] = Franchise::findOrFail(auth('franchise')->user()->franchise_id);
    return view('franchise.auth.profile', $data);
  }
  public function adminDashboard()
  {
    $franchise_id = auth('franchise')->user()->franchise_id;
    $data['total_ordercount'] = Order::whereNull('deleted_at')->where('franchise_id', $franchise_id)->get()->count();
    $data['todays_ordercount'] = Order::whereNull('deleted_at')->where('franchise_id', $franchise_id)->whereDay('created_at', now()->day)->get()->count();

    $data['total_customercount'] = Customer::whereNull('deleted_at')->get()->count();
    $data['todays_customercount'] = Customer::whereNull('deleted_at')->whereDay('created_at', now()->day)->get()->count();

    $data['total_revenue'] = Order::whereNull('deleted_at')->where('franchise_id', $franchise_id)->sum('order_finalamount');
    $data['todays_revenue'] = Order::whereNull('deleted_at')->where('franchise_id', $franchise_id)->whereDay('created_at', now()->day)->sum('order_finalamount');

    $order_ids = Order::whereNull('deleted_at')->where('franchise_id', $franchise_id)->pluck('order_id');

    $data['popular_products'] = DB::table('order_details')
      ->select(array('order_details.od_orderid', 'products.product_id', 'products.product_name', 'products.image', DB::raw('COUNT(products.product_id) as product_count')))
      ->whereIn('order_details.od_orderid', $order_ids)
      ->leftjoin("products", "products.product_id", "=", "order_details.od_productid")
      ->groupBy('products.product_id')
      ->orderBy('product_count', 'desc')
      ->get();

    $html = '';
    $year_start  = 2015;
    $year_end = date('Y'); // current Year
    $selected_year = date('Y');
    for ($i_year = $year_start; $i_year <= $year_end; $i_year++) {
      $selected = $selected_year == $i_year ? ' selected' : '';
      $html .= "<option value='$i_year' $selected>" . $i_year . "</option>";
    }
    $data['years'] = $html;
    return view('franchise.dashboard', $data);
  }

  public function getOrderdataOfyears(Request $request)
  {
    $franchise_id = auth('franchise')->user()->franchise_id;
    $selected_years = $request->selected_year;
    $last_year = $selected_years - 1;
    $order_data = Order::orderBy('created_at')->whereYear('created_at', $selected_years)->whereNull('deleted_at')->where('franchise_id', $franchise_id)->get()
      ->groupBy(function ($date) {
        return $date->created_at->month;
      })
      ->map(function ($group) {
        return $group->count();
      })
      ->union(array_fill(1, 12, 0))
      ->sortKeys()
      ->toArray();

    $order_data_last_year = Order::orderBy('created_at')->whereYear('created_at', $last_year)->whereNull('deleted_at')->where('franchise_id', $franchise_id)->get()
      ->groupBy(function ($date) {
        return $date->created_at->month;
      })
      ->map(function ($group) {
        return $group->count();
      })
      ->union(array_fill(1, 12, 0))
      ->sortKeys()
      ->toArray();

    return response()
      ->json([
        'status' => true,
        'msg' => 'Success',
        'data' => array_values($order_data),
        'last_year' => array_values($order_data_last_year)
      ]);
  }
  /** Stock Methods */
  public function stockAdd()
  {
    $data['row'] = [];
    $data['categories'] = Category::whereNull('deleted_at')->get();
    $data['franchisee'] = Franchise::whereNull('deleted_at')->get();
    $data['products'] = Product::whereNull('deleted_at')->get();
    return view('franchise.stock.create', $data);
  }
  public function stockEdit($id)
  {
    $data['row'] = [];
    $data['categories'] = Category::whereNull('deleted_at')->get();
    $data['franchisee'] = Franchise::whereNull('deleted_at')->get();
    $data['products'] = Product::whereNull('deleted_at')->get();
    if ($id) {
      $data['row'] = Stock::findOrFail($id);
    }
    return view('franchise.stock.create', $data);
  }
  public function stockList()
  {
    $data['warehouse'] = WareHouse::whereNull('deleted_at')->get();
    $data['categories'] = Category::whereNull('deleted_at')->get();
    return view('franchise.stock.list', $data);
  }
  /** Delivery Methods */

  public function DeliveryAdd()
  {
    $data['row'] = [];
    $data['pool'] = Pool::whereNull('deleted_at')->get();
    $data['delivery'] = DeliveryPerson::whereNull('deleted_at')->get();
    return view('franchise.deliveryperson.create', $data);
  }

  public function DeliveryEdit($id)
  {
    $data['row'] = [];
    if ($id) {
      $data['row'] = DeliveryPerson::findOrFail($id);
    }

    $poolarray = SubDeliveryPerson::where('s_dpid', $id)->where('s_fid', auth('franchise')->user()->franchise_id)->first('s_pool');
    $data['poolarray'] = explode(',', $poolarray['s_pool']);
    $data['pool'] = Pool::whereNull('deleted_at')->get();
    $data['delivery'] = DeliveryPerson::whereNull('deleted_at')->groupBy('dp_email')->get();
    return view('franchise.deliveryperson.create', $data);
  }
  public function deliveryList()
  {
    return view('franchise.deliveryperson.list');
  }

  public function deliveryPersonView($id)
  {
    $data['row'] = [];
    if ($id) {
      $data['row'] = DeliveryPerson::findOrFail($id);
    }
    return view('franchise.deliveryperson.view', $data);
  }

  public function historyDetail($id)
  {
    $data['row'] = [];
    if ($id) {
      $data['date'] =  DeliveryHistory::where('history_id', $id)->first();
      $data['dp'] =  DeliveryPerson::where('dp_id', $data['date']['history_dpid'])->first();
      $data['start'] =  DeliveryImage::where('dp_im_historyid', $id)->where('dp_im_type', 'start')->get();
      $data['end'] =  DeliveryImage::where('dp_im_historyid', $id)->where('dp_im_type', 'end')->get();
    }

    return view('franchise.deliveryperson.historyDetail', $data);
  }

  /** Order Methods */

  public function orderList()
  {
    $data['Franchise'] = Franchise::whereNull('deleted_at')->get();
    $data['status_list'] = OrderStatus::whereIN('os_id', [5, 6, 11, 8])->get();
    $data['monthlist'] = [
      1 => 'Jan',
      2 => 'Feb',
      3 => 'Mar',
      4 => 'Apr',
      5 => 'May',
      6 => 'Jun',
      7 => 'Jul',
      8 => 'Aug',
      9 => 'Sep',
      10 => 'Oct',
      11 => 'Nov',
      12 => 'Dec'
    ];

    return view('franchise.order.list', $data);
  }

  function number_of_days($days, $start, $end)
  {
    $start = strtotime($start);
    $end = strtotime($end);
    $w = array(date('w', $start), date('w', $end));
    $x = floor(($end - $start) / 604800);
    $sum = 0;
    for ($day = 0; $day < 7; ++$day) {
      if ($days & pow(2, $day)) {
        $sum += $x + ($w[0] > $w[1] ? $w[0] <= $day || $day <= $w[1] : $w[0] <= $day && $day <= $w[1]);
      }
    }
    return $sum;
  }
}
