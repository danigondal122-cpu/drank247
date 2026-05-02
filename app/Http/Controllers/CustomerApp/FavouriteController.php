<?php

namespace App\Http\Controllers\CustomerApp;

use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\Favourite;
use App\Models\AssignProduct;

use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    public function favouriteProductList(Request $request)
    {
        $id = $request->input('id');
        $token = $request->input('token');
        $search = $request->input('search');

        $query = Product::rightJoin('favourite', 'favourite.fav_itemid', 'products.product_id')
            ->select('fav_id', 'products.product_id', 'product_name', 'product_type', 'product_price', 'vat_price', 'products.image', 'products.is_popular', 'products.category_id', 'category_name')
            ->leftjoin('categories', 'categories.category_id', 'products.category_id')
            ->where('products.is_popular', 1)
            ->where('fav_custid', $id)
            ->whereNull('products.deleted_at')
            ->whereNull('categories.deleted_at');


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
        $data = $query->orderBy('favourite.created_at', 'DESC')->get();

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
    public function addToFavourite(Request $request)
    {
        $id = $request->input('id');
        $token = $request->input('token');
        $product_id = $request->input('product_id');
        $Detail = Product::find($product_id);
        $language = $request->input('language');

        $product_price = $Detail['product_price'];
        $vat_price = $Detail['vat_price'];
        $fav = new Favourite();
        $fav->fav_custid = $id;
        $fav->fav_itemid = $product_id;
        $fav->fav_qty = 1;
        $fav->fav_itemprice = $product_price;
        $fav->fav_total =  $product_price;
        $fav->fav_vatprice = $vat_price;
        $fav->fav_vattotal = $vat_price;
        $fav->save();
        $message = ($language == 'nl') ?  'Product toegevoegd als favoriet'  : 'Product added as Favourite';
        return response()
            ->json([
                'status' => true,
                'Message' => $message,

            ]);
    }

    public function removeFromFavourite(Request $request)
    {
        $token = $request->input('token');
        $id = $request->input('id');
        $product_id = $request->input('product_id');
        $language = $request->input('language');

        Favourite::where('fav_custid', $id)->where('fav_itemid', $product_id)->delete();

        $message = ($language == 'nl') ?  'Product verwijderd uit favoriet'  : 'Product Removed From Favourite';

        return response()->json(['status' => true, 'message' => $message]);
    }
}
