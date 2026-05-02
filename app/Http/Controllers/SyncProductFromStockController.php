<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Base\BaseController;

use App\Models\StockProduct;
use App\Models\Product;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;


class SyncProductFromStockController extends BaseController
{
  public function syncProductFromStock()
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Basic dV9kcmFuazo5eHVXbzRXOVRERWdYY1pXMVBCaA==',
    ];

    $client = new GuzzleClient([
      // 'headers' => $headers,
      'http_errors' => false
    ]);
    $url = "https://stock-connector.azurewebsites.net/api/stock-34-6908-01?code=auCMvLnBGoQX3yIDjo5e06vH60hlUbNFbYWAydUvQrevxpa16YJ10Q==";
    $response = $client->request('GET', $url);
    $detail = json_decode($response->getBody()->getContents(), true);
    // dd($detail);
    foreach ($detail['products'] as $key => $value) {
      $detail = array_map('strrev', explode('x', strrev($value['productExtendedDescription'])));
      $par = $value['weight_UnitOfMeasurement'] == 'liter' ? 'Liter' : 'ml';
      $exp = explode($par, $detail[0]);
      $leter = $exp[0] . ' ' . $par;
      $alcohol =  $exp[1] != "" ? '|' . $exp[1] : '';
      $product_name = $value['productDescription'] . ' | ' . $leter . ' ' . $alcohol;
      $articleNumber = $value['articleNumber'];
      $price = $value['price'];
      $description = $value['productExtendedDescription'];
      $alcohol_per = ($exp[1] != "") ? str_replace('%', '', $exp[1]) : '';
      $count = StockProduct::where('_articleNumber', $articleNumber)->get()->count();
      if ($count == '0') {
        $product = new StockProduct();
        $product->_name = $product_name;
        $product->_price = $price;
        $product->_description = $description;
        $product->_articleNumber = $articleNumber;
        $product->_alcohol = $alcohol_per;
        $product->save();
      } else {
        $product = StockProduct::where('_articleNumber', $articleNumber)->first();
        $product->_name = $product_name;
        $product->_price = $price;
        $product->_description = $description;
        $product->_alcohol = $alcohol_per;
        $product->save();
      }
    }
    return response()->json(['status' => true, 'message' => 'Save In Db!!', 'page' => 'franchise/stockorder/list']);
  }
  public function getProductDetailFromStock(Request $request)
  {
    $description = $request->_description;
    $explode = explode('#', $description);
    $id = $explode[0];
    $des = $explode[1];
    $detail = StockProduct::where('name', $des)->where('id', $id)->first();
    $data['_id'] = $detail['id'];
    $data['_name'] = $detail['name'];
    $data['_price'] = $detail['price'];
    $data['_description'] = $detail['description'];
    $data['_articleNumber'] = $detail['article_number'];
    $data['_alcohol'] = $detail['alcohol'];

    return response()->json(['status' => true, 'message' => 'Success!!', 'data' => $data]);
  }
  public function fetchArticleNumber(Request $request)
  {

    $detail = Product::where('product_article_number', '=', null)->get(['product_id', 'description'])->toArray();

    foreach ($detail as $key => $val) {
      $is_exit = StockProduct::where('_description', 'like', '%' . $val['description'] . '%')->first();
      if ($is_exit) {

        Product::where('product_id', $val['product_id'])->update(['product_article_number' => $is_exit['_articleNumber']]);
      }
    }
    return response()->json(['status' => true, 'message' => 'Success!!']);
  }
}
