<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Base\BaseController as BaseBaseController;

use App\Mail\PlaceOrder;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Pool;
use App\Models\Customer;
use App\Models\PromoCode;
use App\Models\UsedPromoCode;
use App\Models\RateandReview;
use App\Models\AssignProduct;
use App\Models\CmsPage;
use App\Models\Banner;
use App\Models\AssignAllergen;
use App\Models\Cart as CartModel;
use App\Models\Country;
use App\Models\Franchise;

use stdClass;
use Carbon\Carbon;
use Bluem\BluemPHP\Bluem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class RouteController extends BaseBaseController
{
  public function index()
  {
    $data['categories'] = Category::where('category_parentid', '0')->where('is_show', '1')->orderBy('category_order', 'ASC')->whereNull('deleted_at')->get();
    foreach ($data['categories'] as $key => $value) {
      $data['categories'][$key]['sub_category'] = Category::where('category_parentid', $value['category_id'])->whereNull('deleted_at')->get()->count();
    }
    // $data['popular_product'] = Product::with('category')->where('is_show', '1')->where('product_type','0')->where('is_popular', '1')->whereNull('deleted_at')->get();
    //  dd($data['popular_product']);
    $data['popular_product'] = Product::leftJoin('categories', function ($join) {
      $join->on('categories.category_id', '=', 'products.category_id');
    })->where('products.is_show', '1')->where('product_type', '0')->where('products.is_popular', '1')->whereNull('categories.deleted_at')->whereNull('products.deleted_at')->get(['product_id', 'product_name', 'product_price', 'products.image', 'vat', 'vat_price', 'products.is_popular', 'products.category_id', 'category_name']);

    foreach ($data['popular_product'] as $key => $value) {
      $count = AssignProduct::where('assign_catid', $value['category_id'])->leftJoin('products', 'products.product_id', 'assign_product.assign_proid')->whereNull('deleted_at')->where('assign_proid', '!=', '0')->get()->count();
      $data['popular_product'][$key]['assign_product'] = $count;
    }
    $data['banner'] = Banner::get();

    return view('frontend.index', $data);
  }
  public function test()
  {
  //  $list=Order::get();
  //  foreach($list as $key=>$value){
  //   $assign= date('Y-m-d H:i:s', strtotime($value['od_assignedtime'] .'-1 hour'));
  //   $start= date('Y-m-d H:i:s', strtotime($value['od_starttime'] .'-1 hour'));
  //   $end= date('Y-m-d H:i:s', strtotime($value['od_endtime'] .'-1 hour'));
  //   Order::where('order_id',$value['order_id'])->update(['od_assignedtime' => $assign,'od_starttime' => $start ,'od_endtime' => $end]);
  //  }
  }
  public function resetPassword(Request $request)
  {

    $data['token'] = $request->token;
    $data['id'] = $request->id;
    $data['row'] = Customer::where('customer_resettoken', $data['token'])->where('customer_id', $data['id'])->first();
    return view('frontend.reset_password', $data);
  }

  public function getProfile()
  {

    $data['user_profile'] = auth('customer')->user();

    $data['order'] = Order::join('order_status', 'order_status.os_id', 'orders.order_status')
      ->join('address', 'address.address_id', 'orders.order_address_id')->where('order_customerid', $data['user_profile']['customer_id'])->orderBy('order_id', 'Desc')->get();

    if(isset($data['user_profile']->customer_contact_no) && !empty($data['user_profile']->customer_contact_no))
    {
      $contact_no_array = explode('-',$data['user_profile']->customer_contact_no);
    }
    
    if(isset($contact_no_array[1]))
    {
      $data['user_profile']->country_code = $contact_no_array[0];
      $data['user_profile']->customer_contact_no = $contact_no_array[1];
    }
       
    $data['country_code'] = Country::all();

    $data['ordercount'] = $data['order']->count();
    return view('frontend.profile', $data);
  }
  public function categoryList()
  {
    // $data['categories'] = Category::whereNull('deleted_at')->get();
    $data['categories'] = Category::where('category_parentid', '0')->where('is_show', '1')->whereNull('deleted_at')->orderBy('category_order', 'ASC')->get();
    return view('frontend.categories', $data);
  }
  public function productList(Request $request)
  {
    $name=$request->category_name;
    
    if ($name != "extra_product") {
      $name=str_replace('_',' ',$name);
      $detail=Category::where('category_name',$name)->first();
      $category_id=$detail['category_id'];
      $data['category'] = Category::find($category_id);
      $data['products'] = Product::with('category')->where('category_id', $category_id)->where('product_type', '0')->where('is_show', '1')->whereNull('deleted_at')->orderBy('product_order', 'ASC')->get();

      foreach ($data['products'] as $key => $value) {
        //  $count=AssignProduct::where('assign_catid',$value['category']['category_id'])->get()->count();
        $count = AssignProduct::where('assign_catid', $value['category']['category_id'])->leftJoin('products', 'products.product_id', 'assign_product.assign_proid')->whereNull('deleted_at')->where('assign_proid', '!=', '0')->get()->count();
        $data['products'][$key]['assign_product'] = $count;
      }
    } else {
      $data['products'] = Product::where('product_type', '1')->where('is_show', '1')->whereNull('deleted_at')->orderBy('product_order', 'ASC')->get();
    }
    return view('frontend.products', $data);
  }
  public function subCategoryList($category_id)
  {
    $data['category'] = Category::find($category_id);
    $data['subcategory'] = Category::where('category_parentid', $category_id)->whereNull('deleted_at')->get();

    return view('frontend.subcategory', $data);
  }
  public function cartItems()
  {

    $data = [
      'address' => [],
      'cart_contents' => [],
      'cart_total_price' => 0
    ];
    $data = $this->setFinalAmountInFooter();

    if(isset($data['contact_no']) && !empty($data['contact_no']))
    {
      $contact_no_array = explode('-',$data['contact_no']);
    }

    if(isset($contact_no_array[1]))
    {
      $data['country_code_no'] = $contact_no_array[0];
      $data['contact_no'] = $contact_no_array[1];  
    }
  
    $data['country_code'] = Country::all();

    return view('frontend.cart', $data);
  }
  public function orderStatus(Request $request)
  {

    $entranceCode = $request->get('entranceCode');
    $referenceId = $request->get('transactionID');
    $order_payment = OrderPayment::where('iban_entrance_code', $request->get('entranceCode'))->first();
    $transactionID = $order_payment->iban_transaction_id;

    $bluem_config = new stdClass();
    // Fill in prod, test or acc for production, test or acceptance environment.
    $bluem_config->environment = env('BLUEM_ENVIRONMENT');

    // The sender ID, issued by BlueM. Starts with an S, followed by a number.
    $bluem_config->senderID = env('BLUEM_SENDERID');

    // The access token to communicate with BlueM, for the test environment.
    $bluem_config->test_accessToken = env('BLUEM_TEST_ACCESSTOKEN');

    // The access token to communicate with BlueM, for the production environment.
    $bluem_config->production_accessToken = env('BLUEM_PRODUCTION_ACCESSTOKEN');

    // the merchant ID, to be found on the contract you have with the bank for receiving direct debit mandates.
    $bluem_config->merchantID = env('BLUEM_MERCHANTID');

    // What's your BrandID? Set at BlueM
    $bluem_config->brandID = '247DrankPayment';

    $bluem_config->merchantReturnURLBase = env('MERCHANT_RETURN_URL_BASE');

    $bluem_object = new Bluem($bluem_config);

    $response = $bluem_object->PaymentStatus($transactionID, $entranceCode);

    if ($response->PaymentStatusUpdate) {
      $statuscode = $response->PaymentStatusUpdate->Status;

      //update payment status code
      OrderPayment::where('iban_entrance_code', $request->get('entranceCode'))->update(['status_code' => $statuscode]);

      // add your own logic in each case:
      switch ($statuscode) {
        case 'Success':
        case 'New':
        case 'Processing':
          // echo 'successful status response';

          $order_id = $order_payment->order_id;


          $detail = Order::leftJoin('customers', function ($join) {
            $join->on('customers.customer_id', '=', 'orders.order_customerid');
          })->where('order_id', $order_id)->first();

          $customer_id = $detail['order_customerid'];
          ## update Custome is_verified status

          Customer::where('customer_id', $detail['order_customerid'])->update(['is_verified' => TRUE]);

          ## Update Order Status

          $customer_email = $detail['customer_email'];
          $customer_name = $detail['customer_name'];
          Order::where('order_id', $order_id)->update(['order_status' => '1']);
          CartModel::where('cart_custid', $detail['order_customerid'])->whereNull('deleted_at')->delete();

          ## promo code count 
          if ($detail['order_promocode'] != "0" && $detail['order_discount'] != 0.00) {
            $custom_used = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id', $detail['order_promocode'])->get()->count();
            if ($custom_used) {
              $custuser = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->first();
              UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->update(['used_count' => $custuser['used_count'] + 1]);
            } else {
              $newusedcode = new UsedPromoCode();
              $newusedcode->pcode_id =  $detail['order_promocode'];
              $newusedcode->c_id = auth('customer')->user()->customer_id;
              $newusedcode->used_count = 1;
              $newusedcode->save();
            }
          }


          ## Send Email##   

          $maildata = [];
          $maildata['order_id'] = $order_id;
          $maildata['name'] = $customer_name;
          $maildata['scan'] = 'qrcode' . $order_id . '.png';
          $maildata['order'] = Order::leftJoin('customers', function ($join) {
            $join->on('customers.customer_id', '=', 'orders.order_customerid');
          })->leftJoin('address', function ($join) {
            $join->on('address.address_id', '=', 'orders.order_address_id');
          })->find($order_id);

          $maildata['orderdetail'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $order_id)
            ->get(['od_qty', 'od_vattotal', 'product_name', 'image']);
          foreach ($maildata['orderdetail'] as $key => $value) {
            if ($value['image'] != "") {
              $maildata['orderdetail'][$key]['image'] = asset('uploads/product/thumb') . '/' . $value['image'];
            } else {
              $maildata['orderdetail'][$key]['image'] = asset('img/logo.png');
            }
          }
          Mail::to($customer_email)
            ->send(new PlaceOrder($maildata));

          //Automatic order Assignment
          $this->OrderAssignment($order_id);

          $data['status'] = 'Success';
          $data['message'] = 'Your amount has been successfully paid !!';
          return view('frontend.order_status', $data);

        case 'Pending':
          $data['status'] = 'Pending';
          $data['message'] = 'Your payment is in Pending';
          // do something when the request is still processing (for example tell the user to come back later to this page)
          return view('frontend.order_status', $data);
          break;
        case 'Cancelled':
          // do something when the request has been canceled by the user
          $data['status'] = 'Cancelled';
          $data['message'] = 'Your order Cancelled !!';
          return view('frontend.order_cancelled', $data);
          break;
        case 'Open':
          $data['status'] = 'Open';
          $data['message'] = 'Your order is Open !!';
          // do something when the request has not yet been completed by the user, redirecting to the transactionURL again 
          return view('frontend.order_status', $data);
          break;
        case 'Expired':
          $data['status'] = 'Expired';
          $data['message'] = 'Your payment link is expired !!';
          // do something when the request has expired
          return view('frontend.order_cancelled', $data);
          break;
        default:
          $data['status'] = 'Error';
          $data['message'] = 'Something went wrong !!';
          // unexpected status returned, show an error
          return view('frontend.order_cancelled', $data);
          break;
      }
    }
    if ($response->PaymentErrorResponse) {
      $data['status'] = 'Error';
      $data['message'] = 'Something went wrong !!';
      return view('frontend.order_cancelled', $data);
    }
  }

  

  public function getProductDetail(Request $request)
  {
    $id = $request->id;

    $data['products'] = Product::with('category')->where('product_id', $id)->whereNull('deleted_at')->first();
    $count = AssignProduct::where('assign_catid', $data['products']['category']['category_id'])->leftJoin('products', 'products.product_id', 'assign_product.assign_proid')->whereNull('deleted_at')->where('assign_proid', '!=', '0')->get()->count();
    $data['products']['assign_product'] = $count;
    $allergen =  AssignAllergen::select(DB::raw('group_concat(name) as allergen_names'))
      ->leftjoin('allergen', 'allergen.allergen_id', 'assign_allergen.allergen_id')->where('product_id', $id)->groupBy('product_id')->first();
    $data['products']['allergen'] = $allergen['allergen_names'];
    //  $data['products'] = Product::leftJoin('categories', function($join) {
    //   $join->on('products.category_id', '=', 'categories.category_id');
    //  })->where('product_id',$id)->whereNull('products.deleted_at')->first();
    //  dd($data['products']);
    return view('modal.productdetail', $data);
  }
  public function customizedProduct(Request $request)
  {

    $id = $request->id;
    $data['mainProduct'] = Product::with('category')->where('product_id', $id)->whereNull('deleted_at')->first();

    $category_id = $data['mainProduct']['category']['category_id'];
    $get_assignproduct = AssignProduct::where('assign_catid', $category_id)->where('assign_proid', '!=', '0')->pluck('assign_proid')->toarray();
    $data['ExtraProducts'] = Product::where('product_type', '1')->whereIn('product_id', $get_assignproduct)->whereNull('deleted_at')->get();

    if (Auth::guard('customer')->check()) {
      $customer_id = auth('customer')->user()->customer_id;
      $data['exit_productarray'] = $data['cart_contents'] = CartModel::with('product')->with('product.category')->where('cart_custid', $customer_id)->pluck('cart_itemid')->toarray();
      $array = CartModel::with('product')->with('product.category')->where('cart_custid', $customer_id)->get()->toarray();
      $array = array_values($array);

      if (in_array(strval($id), $data['exit_productarray'])) {
        $key = array_search($id, array_column($array, 'cart_itemid'));
        $data['mainProduct']['qty'] = $array[$key]['cart_qty'];
        $data['mainProduct']['vatvalue'] = $array[$key]['cart_vattotal'];
        $data['mainProduct']['rowId'] = $array[$key]['cart_id'];
      } else {
        $key = array_search($id, array_column($array, 'id'));
        $data['mainProduct']['qty'] = '1';
        $data['mainProduct']['vatvalue'] = $data['mainProduct']['vat_price'];
        $data['mainProduct']['rowId'] = '0';
      }


      foreach ($data['ExtraProducts'] as $key1 => $value) {

        if (in_array(strval($value['product_id']), $data['exit_productarray'])) {

          $key = array_search(strval($value['product_id']), array_column($array, 'cart_itemid'));

          $data['ExtraProducts'][$key1]['qty'] = $array[$key]['cart_qty'];
          $data['ExtraProducts'][$key1]['vatvalue'] = $array[$key]['cart_vattotal'];
          $data['ExtraProducts'][$key1]['rowId'] = $array[$key]['cart_id'];
        } else {
          $data['ExtraProducts'][$key1]['qty'] = '1';
          $data['ExtraProducts'][$key1]['vatvalue'] = $value['vat_price'];
          $data['ExtraProducts'][$key1]['rowId'] = '0';
        }
      }
    } else {
      $data['exit_productarray'] = Cart::content()->pluck('id')->toarray();
      $array = Cart::content()->toarray();
      $array = array_values($array);

      if (in_array(strval($id), $data['exit_productarray'])) {
        $key = array_search($id, array_column($array, 'id'));
        $data['mainProduct']['qty'] = $array[$key]['qty'];
        $data['mainProduct']['vatvalue'] = $array[$key]['subtotal'];
        $data['mainProduct']['rowId'] = $array[$key]['rowId'];
      } else {
        $key = array_search($id, array_column($array, 'id'));
        $data['mainProduct']['qty'] = '1';
        $data['mainProduct']['vatvalue'] = $data['mainProduct']['vat_price'];
        $data['mainProduct']['rowId'] = '0';
      }
      foreach ($data['ExtraProducts'] as $key1 => $value) {

        if (in_array(strval($value['product_id']), $data['exit_productarray'])) {
          $key = array_search(strval($value['product_id']), array_column($array, 'id'));
          $data['ExtraProducts'][$key1]['qty'] = $array[$key]['qty'];
          $data['ExtraProducts'][$key1]['vatvalue'] = $array[$key]['subtotal'];
          $data['ExtraProducts'][$key1]['rowId'] = $array[$key]['rowId'];
        } else {
          $data['ExtraProducts'][$key1]['qty'] = '1';
          $data['ExtraProducts'][$key1]['vatvalue'] = $value['vat_price'];
          $data['ExtraProducts'][$key1]['rowId'] = '0';
        }
      }
    }
    return view('modal.customizedproduct', $data);
  }
  public function checkPostCode(Request $request)
  {

    $rules = [
      'postcode' => 'required',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return response()
        ->json([
          'status' => false,
          'type' => 'validation',
          'errors' => $validator->errors()
        ]);
    } else {
      $postcode = $request->input('postcode');
      $assign_pool = Franchise::select('franchise_pool')->where('fs_on_off','online')->whereNull('deleted_at')->get();

      $ass_pools = [];
      foreach ($assign_pool as $key => $value) {
        $value = explode(',', $value['franchise_pool']);
        $ass_pools[] = $value;
      }
      $total_ass = call_user_func_array("array_merge", $ass_pools);
      // $total_ass=implode(',',$total_ass);

      $pool = Pool::whereIN('pool_id', $total_ass)->whereNull('deleted_at')->get();
      $array = [];

      foreach ($pool as $value) {
        $code = preg_replace('/[^0-9.]+/', '', $postcode);
        if ($code >= $value->from_postcode && $code <= $value->to_postcode) {
          $array[] = $value['pool_id'];
        }
      }

      $count = count($array);

      $query = Pool::whereIN('pool_id', [$total_ass])->whereNull('deleted_at');

      $column_search = ['area'];
      $rawQuery = '';
      $search = $request->input('postcode');
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

      $total = $query->get()->count();
      if ($count == 0 && $total == 0) {
        return response()
          ->json([
            'status' => false,
            'type' => 'invalidPostcode',
            'message' => 'we do not provie our service on your postcode area. Please call customer service for more details.',
          ]);
      } else {
        return response()
          ->json([
            'status' => true,
            'type' => 'success',
            'message' => 'We deliver at your postcode !',
          ]);
      }
    }
  }
  public function checkPromoCode(Request $request)
  {

    $rules = [
      'promocode' => 'required',

    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return response()
        ->json([
          'status' => false,
          'type' => 'validation',
          'errors' => $validator->errors()
        ]);
    } else {
      $date = (Carbon::now())->toDateString();
      $promocode = $request->input('promocode');
      $finalamount = $request->input('finalamount');

      $code_detail = PromoCode::where('code_text', "$promocode")->where('code_status', '1')->first();

      if ($code_detail) {
        if ($code_detail['expiration_type'] == "0") {

          $isExit = PromoCode::where('code_text', "$promocode")->where('code_status', '1')->where('start_date', '<=', $date)->first();
        }
        if ($code_detail['expiration_type'] == "1") {
          $isExit = PromoCode::where('code_text', "$promocode")->where('code_status', '1')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)->first();
        }
        if ($isExit) {

          $customer_id = auth('customer')->user()?auth('customer')->user()->customer_id:'';
          $code_id = $code_detail['code_id'];
          $discount_type = $code_detail['discount_type'];
          $discount = $code_detail['discount'];
          $limitation_type = $code_detail['limitation_type'];
          $max_users = $code_detail['max_users'];
          $max_peruser = $code_detail['max_peruser'];
          $expiration_type = $code_detail['expiration_type'];
          $start_date = $code_detail['start_date'];
          $end_date = $code_detail['end_date'];
          $code_status = $code_detail['code_status'];

          if(!empty($customer_id)){
            $custom_used = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id', $code_id)->get()->count();

            if ($custom_used) {
              $custuser = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id', $code_id)->first();
              $customcount = $custuser['used_count'];
            } else {
              $customcount = 0;
            }
          }else{
            $customcount = 0;
          }
          $usedlimatation = UsedPromoCode::where('pcode_id', $code_id)->get()->sum('used_count');

          if ($code_detail['expiration_type'] == "0") {
            if ($customcount < $max_peruser) {
              if ($discount_type == 0) {
                if ($finalamount > $discount) {
                  return response()
                    ->json([
                      'status' => true,
                      'type' => 'valid',
                      'message' => 'Promo code applied successfully!',
                      'discount_type' => $discount_type,
                      'discount' => $discount,
                      'promo_code' => $code_id,

                    ]);
                } else {
                  return response()
                    ->json([
                      'status' => false,
                      'type' => 'invalidPromoCode',
                      'message' => 'Final Price should be greater than ' . $discount,
                      'discount_type' => $discount_type,
                      'discount' => $discount,
                      'promo_code' => $code_id,

                    ]);
                }
              } else {
                return response()
                  ->json([
                    'status' => true,
                    'type' => 'valid',
                    'message' => 'Promo code applied successfully!',
                    'discount_type' => $discount_type,
                    'discount' => $discount,
                    'promo_code' => $code_id,

                  ]);
              }
            } else {
              return response()
                ->json([
                  'status' => false,
                  'type' => 'invalidPromoCode',
                  'message' => 'Please Enter Valid Promo Code !',
                ]);
            }
          }
          if ($code_detail['expiration_type'] == "1") {

            if ($customcount < $max_peruser && $usedlimatation < $max_users) {
              if ($discount_type == 0) {
                if ($finalamount > $discount) {
                  return response()
                    ->json([
                      'status' => true,
                      'type' => 'valid',
                      'message' => 'Promo code applied successfully!',
                      'discount_type' => $discount_type,
                      'discount' => $discount,
                      'promo_code' => $code_id,
                    ]);
                } else {
                  return response()
                    ->json([
                      'status' => false,
                      'type' => 'invalidPromoCode',
                      'message' => 'Final Price should be greater than ' . $discount,
                      'discount_type' => $discount_type,
                      'discount' => $discount,
                      'promo_code' => $code_id,

                    ]);
                }
              } else {
                return response()
                  ->json([
                    'status' => true,
                    'type' => 'valid',
                    'message' => 'Promo code applied successfully!',
                    'discount_type' => $discount_type,
                    'discount' => $discount,
                    'promo_code' => $code_id,
                  ]);
              }
            } else {
              return response()
                ->json([
                  'status' => false,
                  'type' => 'invalidPromoCode',
                  'message' => 'Please Enter Valid Promo Code !',
                ]);
            }
          }
        } else {

          return response()
            ->json([
              'status' => false,
              'type' => 'invalidPromoCode',
              'message' => 'Please Enter Valid Promo Code !',
            ]);
        }
      } else {
        return response()
          ->json([
            'status' => false,
            'type' => 'invalidPromoCode',
            'message' => 'Please Enter Valid Promo Code !',
          ]);
      }
    }
  }
  public function contactUs(Request $request)
  {
   
    return view('frontend.contactus');
  }

  // public function refreshCaptcha(){
  //   return response()->json(['captcha'=> captcha_img()]);
  // }

  public function RateandReview(Request $request)
  {

    $order_id = $request->order_id;
    $currentRating = $request->currentRating;
    $dp_id = $request->dp_id;
    $customer_id = auth('customer')->user()->customer_id;

    $count = RateandReview::where('order_id', $order_id)->first();
    if ($count) {
      $rates = RateandReview::find($count['id']);
    } else {
      $rates = new RateandReview();
    }

    $rates->order_id = $order_id;
    $rates->customer_id = $customer_id;
    $rates->dp_id = $dp_id;
    $rates->rate = $currentRating;
    $rates->save();

    return response()
      ->json([
        'status' => true,
        'type' => 'valid',
        'message' => 'Success!',


      ]);
  }
  public function addReview(Request $request)
  {

    $order_id = $request->order_id;
    $review = $request->review;
    $dp_id = $request->dp_id;
    $customer_id = auth('customer')->user()->customer_id;

    $rules = [
      'review' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()
        ->json([
          'status' => false,
          'type' => 'VALIDATION',
          'errors' => $validator->errors()
        ]);
    } else {

      $count = RateandReview::where('order_id', $order_id)->first();
      if ($count) {
        $rates = RateandReview::find($count['id']);
      } else {
        $rates = new RateandReview();
      }

      $rates->order_id = $order_id;
      $rates->customer_id = $customer_id;
      $rates->dp_id = $dp_id;
      $rates->review = $review;
      $rates->save();


      return response()->json([
        'status' => true,
        'message' => 'Success!'
      ]);
    }
  }
  // public function privacyPolicy()
  // {

  //   $row = CmsPage::where('_page_name', 'Privacy Policy')->first();
  //   $data['content'] = Session::get('locale') == 'nl' ? $row['_page_content_dutch'] : $row['_page_content_eng'];
  //   return view('frontend.privacy_policy', $data);
  // }
  // public function termsAndCondition()
  // {

  //   $row = CmsPage::where('_page_name', 'Terms & Condition')->first();
  //   $data['content'] = Session::get('locale') == 'nl' ? $row['_page_content_dutch'] : $row['_page_content_eng'];
  //   return view('frontend.terms_condition', $data);
  // }
  // public function coloPhone()
  // {

  //   $row = CmsPage::where('_page_name', 'Colophone')->first();
  //   $data['content'] = Session::get('locale') == 'nl' ? $row['_page_content_dutch'] : $row['_page_content_eng'];
  //   return view('frontend.colophone', $data);
  // }
  // public function cookieStatement()
  // {

  //   $row = CmsPage::where('_page_name', 'Cookie Statement')->first();
  //   $data['content'] = Session::get('locale') == 'nl' ? $row['_page_content_dutch'] : $row['_page_content_eng'];
  //   return view('frontend.cookiestatement', $data);
  // }
  // public function alcoholLaw()
  // {

  //   $row = CmsPage::where('_page_name', 'Guaranteed Working Method Alcohol Law')->first();
  //   $data['content'] = Session::get('locale') == 'nl' ? $row['_page_content_dutch'] : $row['_page_content_eng'];
  //   return view('frontend.alcohollaw', $data);
  // }
  // public function favouriteItems()
  // {
  //   $customer_id = auth('customer')->user()->customer_id;
  //   $data['products'] = Product::rightjoin('favourite', 'products.product_id', 'favourite.fav_itemid')->where('fav_custid', $customer_id)->orderBy('fav_id', 'Desc')->get();

  //   return view('frontend.favourite', $data);
  // }
  // public function technology()
  // {
  //   return view('frontend.technology');
  // }

  // public function autocomplete(Request $request)
  // {

  //   $data = Product::where("product_name", "LIKE", "%%{$request->search}%%")
  //     ->get();

  //   $response = array();
  //   foreach ($data as $product) {

  //     $response[] = array("value" => $product->category_id == '' ? 'extra_product' : $product->category_id, "label" => $product->product_name);
  //   }
  //   //  dd($response);
  //   return response()->json($response);
  // }
  public function ageValidation()
  {
    return view('frontend.age-validation');
  }

  public function validateAge(Request $request)
  {
    $rules = [
      'date' => 'required',
      'month' => 'required',
      'year' => 'required',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return response()
        ->json([
          'status' => false,
          'type' => 'VALIDATION',
          'errors' => $validator->errors()
        ]);
    } else {
      $date = $request['date'];
      $month = $request['month'];
      $year = $request['year'];

      $birthDate = $year.'-'.$month.'-'.$date;
    
      $years = Carbon::parse($birthDate)->age;

      if($years > 18){

        return response()->json([
          'status' => true,
          'msg' => 'Success !'
        ]);
      }else{
        return response()->json([
          'status' => false,
          'type' => 'LESS_AGE',
          'msg' => 'You must be at least 18 years of age to enter this website.'
        ]);
      }
     
    }
  }
}
