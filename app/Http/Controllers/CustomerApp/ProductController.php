<?php

namespace App\Http\Controllers\CustomerApp;

use App\Http\Controllers\Controller;

use App\Models\AssignProduct;
use App\Models\Product;
use App\Models\Favourite;
use App\Models\Cart;
use App\Models\AssignAllergen;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
  public function productList(Request $request)
  {
    $id = $request->input('id');
    $token = $request->input('token');
    $cat_id = $request->input('cat_id');
    $search = $request->input('search');
    $page_no = $request->input('page_no');
    $start_from = ($page_no - 1) * 20;
    $per_page = 20;
    $query = Product::select('product_id', 'product_name', 'product_type', 'product_price', 'vat_price', 'products.image', 'products.is_popular', 'products.category_id', 'category_name')
      ->leftjoin('categories', 'categories.category_id', 'products.category_id');

    if (isset($cat_id) && $cat_id != "" &&  $cat_id != 0) {
      $query = $query->where('products.category_id', $cat_id)->where('product_type', 0);
    } else {
      $query = $query->where('product_type', 1);
    }

    $rawQuery = '';

    $column_search = ['product_name']; //set column field database for datatable searchable 
    //Search 
    if ($request->input('search') && $request->input('search') != '') {

      $search = $request->input('search');
      $i = 0;
      foreach ($column_search as $key => $value) {
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
    $data = $query->skip($start_from)->limit($per_page)->get();

    if (isset($id) && $id != "" &&  $id != 0) {
      $fav_array = Favourite::where('fav_custid', $id)->pluck('fav_itemid')->toArray();
    }
    foreach ($data as $key => $value) {
      if (isset($id) && $id != "" &&  $id != 0) {
        $data[$key]['is_favourite'] = in_array($value['product_id'], $fav_array) ? true : false;
      } else {
        $data[$key]['is_favourite'] = false;
      }
      $data[$key]['category_name'] = $value['category_name'] == null ? '' : $value['category_name'];
      $count = AssignProduct::where('assign_catid', $value['category_id'])->leftJoin('products', 'products.product_id', 'assign_product.assign_proid')->whereNull('deleted_at')->where('assign_proid', '!=', '0')->get()->count();
      $data[$key]['assign_product'] = $count;
    }
    return response()
      ->json([
        'status' => true,
        'data' => $data,

      ]);
  }
  public function productDetail(Request $request)
  {
    $id = $request->input('id');
    $token = $request->input('token');
    $p_id = $request->input('product_id');

    $data = Product::select('product_id', 'product_name', 'product_article_number', 'alcohol', 'product_type', 'product_price', 'vat_price', 'products.image', 'products.is_popular', 'products.category_id', 'category_name', 'products.description')
      ->leftjoin('categories', 'products.category_id', 'categories.category_id')
      ->where('product_id', $p_id)->whereNull('products.deleted_at')->first();
    $allergen =  AssignAllergen::select(DB::raw('group_concat(name) as allergen_names'))
      ->leftjoin('allergen', 'allergen.allergen_id', 'assign_allergen.allergen_id')->where('product_id', $p_id)->groupBy('product_id')->first();
    // dd($allergen['allergen_names']);
    $data['description'] = $data['description'] == null ? '' :  $data['description'];
    $data['category_name'] = $data['category_name'] == null ? '' :  $data['category_name'];
    $data['alcohol'] = $data['alcohol'] == null ? '' :  $data['alcohol'];
    $data['product_article_number'] = $data['product_article_number'] == null ? '' :  $data['product_article_number'];
    $data['allergen'] = $allergen['allergen_names'] == null ? '' : $allergen['allergen_names'];
    $count = AssignProduct::where('assign_catid', $data['category_id'])->leftJoin('products', 'products.product_id', 'assign_product.assign_proid')->whereNull('deleted_at')->where('assign_proid', '!=', '0')->get()->count();
    $data['assign_product'] = $count;
    $data['is_favourite'] = false;
    if (isset($id) && $id != "" &&  $id != 0) {
      $fav_array = Favourite::where('fav_custid', $id)->pluck('fav_itemid')->toArray();
      $data['is_favourite'] = in_array($data['product_id'], $fav_array) ? true : false;
    }

    return response()
      ->json([
        'status' => true,
        'data' => $data,

      ]);
  }
  public function popularProductList(Request $request)
  {
    $id = $request->input('id');
    $token = $request->input('token');
    $search = $request->input('search');
    $page_no = $request->input('page_no');
    $start_from = ($page_no - 1) * 20;
    $per_page = 20;
    $query = Product::select('product_id', 'product_name', 'product_type', 'product_price', 'vat_price', 'products.image', 'products.is_popular', 'products.category_id', 'products.category_id', 'category_name')
      ->leftjoin('categories', 'products.category_id', 'categories.category_id')
      ->where('products.is_show', 1)->where('products.is_popular', 1)->whereNull('products.deleted_at')->whereNull('categories.deleted_at');

    $rawQuery = '';

    $column_search = ['product_name']; //set column field database for datatable searchable 
    //Search 
    if ($request->input('search') && $request->input('search') != '') {

      $search = $request->input('search');
      $i = 0;
      foreach ($column_search as $key => $value) {
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
    $data = $query->skip($start_from)->limit($per_page)->get();
    if (isset($id) && $id != "" &&  $id != 0) {
      $fav_array = Favourite::where('fav_custid', $id)->pluck('fav_itemid')->toArray();
    }
    foreach ($data as $key => $value) {
      if (isset($id) && $id != "" &&  $id != 0) {
        $data[$key]['is_favourite'] = in_array($value['product_id'], $fav_array) ? true : false;
      } else {
        $data[$key]['is_favourite'] = false;
      }
      $data[$key]['category_name'] = $value['category_name'] == null ? '' : $value['category_name'];
      $count = AssignProduct::where('assign_catid', $value['category_id'])->leftJoin('products', 'products.product_id', 'assign_product.assign_proid')->whereNull('deleted_at')->where('assign_proid', '!=', '0')->get()->count();
      $data[$key]['assign_product'] = $count;
    }
    return response()
      ->json([
        'status' => true,
        'data' => $data,

      ]);
  }

  public function assignExtraProductList(Request $request)
  {
    $id = $request->input('id');
    $token = $request->input('token');
    $product_id = $request->input('product_id');

    $data['product'] = Product::select('product_id', 'product_name', 'product_type', 'product_price', 'vat_price', 'products.image', 'products.category_id', 'category_name')->leftJoin('categories', 'categories.category_id', 'products.category_id')->where('product_id', $product_id)->first();
    $cart_exit = Cart::where('cart_custid', $id)->where('cart_itemid', $product_id)->first();

    if ($cart_exit) {
      $data['product']['is_in_cart'] = true;
      $data['product']['cart_qty'] = $cart_exit['cart_qty'];
      $data['product']['cart_vattotal'] = $cart_exit['cart_vattotal'];
    } else {
      $data['product']['is_in_cart'] = false;
      $data['product']['cart_qty'] = 0;
      $data['product']['cart_vattotal'] = "0.00";
    }

    $category_id = $data['product']['category_id'];

    /* Assign Product List*/
    $get_assignproduct = AssignProduct::where('assign_catid', $category_id)->where('assign_proid', '!=', '0')->pluck('assign_proid')->toarray();
    $data['extra_product'] = Product::select('product_id', 'product_name', 'product_type', 'product_price', 'vat_price', 'products.image', 'products.category_id')
      ->where('product_type', '1')->whereIn('product_id', $get_assignproduct)->whereNull('deleted_at')
      ->get();
    foreach ($data['extra_product'] as $key => $value) {
      $cart_exit = Cart::where('cart_custid', $id)->where('cart_itemid', $value['product_id'])->first();
      $data['extra_product'][$key]['category_name'] = "";
      if ($cart_exit) {
        $data['extra_product'][$key]['is_in_cart'] = true;
        $data['extra_product'][$key]['cart_qty'] = $cart_exit['cart_qty'];
        $data['extra_product'][$key]['cart_vattotal'] = $cart_exit['cart_vattotal'];
      } else {
        $data['extra_product'][$key]['is_in_cart'] = false;
        $data['extra_product'][$key]['cart_qty'] = 0;
        $data['extra_product'][$key]['cart_vattotal'] = "0.00";
      }
    }

    return response()
      ->json([
        'status' => true,
        'data' => $data,

      ]);
  }
}
