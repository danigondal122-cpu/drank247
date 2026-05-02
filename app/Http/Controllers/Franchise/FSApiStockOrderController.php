<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Mail\StockOrderMail;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockOrder;
use App\Models\StockOrderDetail;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FSApiStockOrderController extends Controller
{
    public function getProductStock(Request $request)
    {

        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Basic dV9kcmFuazo5eHVXbzRXOVRERWdYY1pXMVBCaA==',
        ];

        $client = new GuzzleClient([
            // 'headers' => $headers,
            'http_errors' => false,
        ]);
        //TODO!: API ini belum diketahui apakah bekerja.
        $url = 'https://stock-connector.azurewebsites.net/api/stock-34-6908-01?code=auCMvLnBGoQX3yIDjo5e06vH60hlUbNFbYWAydUvQrevxpa16YJ10Q==&type=simple&articleNumber=' + $request->no;
        $response = $client->request('GET', $url);
        $detail = json_decode($response->getBody()->getContents(), true);
        $availableStock = $detail['products'][0]['availableStock'];

        return $availableStock;
    }

    public function updateStock(Request $request)
    {

        /** @var array<string|int, mixed> $products */
        $products = Product::where('order_from', '0')->pluck('product_article_number')->all();

        foreach (array_filter($products) as $key => $value) {
            $client = new GuzzleClient([
                'http_errors' => false,
            ]);

            //TODO!: API ini belum diketahui apakah bekerja.
            $url = 'https://stock-connector.azurewebsites.net/api/stock-34-6908-01?code=auCMvLnBGoQX3yIDjo5e06vH60hlUbNFbYWAydUvQrevxpa16YJ10Q==&type=simple&articleNumber='.$value;
            $response = $client->request('GET', $url);
            $detail = json_decode($response->getBody()->getContents(), true);

            if (isset($detail['products'][0])) {
                $availableStock = $detail['products'][0]['availableStock'];
            } else {
                $availableStock = 0;
            }
            Product::where('product_article_number', $value)->update(['api_available_stock' => $availableStock]);
        }

        return response()->json(['status' => true, 'message' => 'Stock Updated!!']);
    }

    public function selectStock(Request $request)
    {
        // TODO!: Kalau ada waktu, buat validasi product_id ada di db & kodingan yang lebih singkat
        /** @var array<int,array{product_id:string,stock:string,qty:string,qty:string}> $productStock */
        $productStock = $request->product_stock;

        $order_to = $request->order_to;
        //order to STOCK API //
        if ($order_to == 0) {
            $rules = [];
            foreach ($productStock as $key => $value) {
                $product_id = $productStock[$key]['product_id'];
                $qty = $productStock[$key]['qty'];
                $stock = $productStock[$key]['stock'];
                $id = (int) $product_id;
                $qty = (int) $qty;
                $stock = (int) $stock;
                if ($qty == '') {
                    $rules['qty'.$id] = 'required';
                }
                if ($qty > $stock) {
                    $rules['qty'.$id] = 'required';
                }
                $messages = [
                    'required' => 'You can not add quantity greater than Available Stock',
                    'max'      => 'You can not add quantity greater than Available Stock',
                ];
            }
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()
                    ->json([
                        'status' => false,
                        'type'   => 'validation',
                        'errors' => $validator->errors(),
                    ]);
            } else {
                $order = new StockOrder;
                $order->franchise_id = auth('franchise')->id();
                $order->order_to = $order_to;
                $order->save();

                foreach ($productStock as $key => $value) {
                    $product_id = $productStock[$key]['product_id'];
                    $qty = $productStock[$key]['qty'];
                    $stock = $productStock[$key]['stock'];
                    $orderDetail = new StockOrderDetail;
                    $orderDetail->stock_order_id = $order->id;
                    $orderDetail->product_id = $product_id;
                    $orderDetail->qty = $qty;
                    $orderDetail->save();
                }

                return response()->json(['status' => true, 'message' => 'Save In Db!!', 'page' => 'franchise/stockorder/list', 'order_id' => $order->id]);
            }
        } else {
            // Order to Other Wholesale Company or 247Drank own Warehouse
            $order = new StockOrder;
            $order->franchise_id = auth('franchise')->id();
            $order->order_to = $order_to;
            $order->save();
            foreach ($productStock as $key => $value) {
                $product_id = $productStock[$key]['product_id'];
                $qty = $productStock[$key]['qty'];
                $stock = $productStock[$key]['stock'];
                $orderDetail = new StockOrderDetail;
                $orderDetail->stock_order_id = $order->id;
                $orderDetail->product_id = $product_id;
                $orderDetail->qty = $qty;
                $orderDetail->save();
            }
            if ($order) {
                Mail::to(env('ADMIN_EMAIL'))
                    ->send(new StockOrderMail(
                        orderId: $order->id,
                        franchise: auth('franchise')->user(),
                        stockOrderDetails: $order->stockOrderDetails->load('product:id,product_name')
                    ));
            }

            return response()->json(['status' => true, 'message' => 'Email Sent Successfully!!', 'page' => 'franchise/stockproduct/list', 'order_id' => $order->id]);
        }
    }

    public function sendStockOrder(Request $request)
    {
        $id = $request->id;
        $fs_detail = StockOrder::join('franchises', 'franchises.id', 'stock_orders.franchise_id')->first();
        $order_detail = StockOrderDetail::join('products', 'products.id', 'stock_order_details.product_id')->where('stock_order_id', $id)->get(['products.id', 'products.product_article_number', 'qty']);

        $arr = [];
        $arr['order_reference'] = $id.'drrank';
        $arr['pickup_delivery'] = $request->order_type;
        $arr['pickup_delivery_date'] = '2018-04-06T10:57:59';
        $arr['currency'] = 'Euro';
        $arr['Email'] = $fs_detail['franchises_email'];

        foreach ($order_detail as $key => $value) {
            $detail = [];
            $detail['linenumber'] = $key + 1;
            $detail['productnumber'] = $value['product_article_number'];
            $detail['quantity'] = $value['qty'];
            $arr['OrderLines'][] = $detail;
        }

        $arr['ShippingAddress']['name'] = $fs_detail['franchises_name'];
        $arr['ShippingAddress']['shipping_company'] = $fs_detail['company_name'];
        $arr['ShippingAddress']['street'] = $fs_detail['house_no_street'];
        $arr['ShippingAddress']['zip'] = $fs_detail['post_code'];
        $arr['ShippingAddress']['city'] = $fs_detail['residence'];
        $arr['ShippingAddress']['country'] = $fs_detail['landmark'];
        $arr['ShippingAddress']['phonenumber'] = $fs_detail['mobile_no'];
        $arr['ShippingLabels'][]['label_url'] = 'http://www.bs-htg.com/testfile.pdf';
        $data = json_encode($arr)."\n";

        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Basic dV9kcmFuazo5eHVXbzRXOVRERWdYY1pXMVBCaA==',
        ];
        $client = new GuzzleClient([
            'headers'     => $headers,
            'http_errors' => true,
            'body'        => $data,
        ]);

        //TODO!: API ini belum diketahui apakah bekerja.
        $url = 'https://htgapi-test.azurewebsites.net/api/order';
        $response = $client->request('post', $url);
        // logger($url, ['request'=>[ 'headers' => $headers, 'body' => $arr, ], 'response' => ['status' => $response->getStatusCode(), 'headers'=> $response->getHeaders(), 'body' => $response->getBody()->getContents()]]);

        $response = json_decode($response->getBody()->getContents(), true);
        if ($response != '') {
            StockOrder::where('id', $id)->update(['order_reference' => $response['id'], 'order_type' => $request->order_type, 'pickup_delivery_date' => $request->date]);

            return response()->json(['status' => true, 'message' => 'Ordered Successfully!!', 'page' => 'franchise/stockorder/list']);
        } else {
            StockOrder::where('id', $id)->delete();
            StockOrderDetail::where('stock_order_id', $id)->delete();

            return response()->json(['status' => false]);
        }
    }

    public function stockOrderList()
    {
        return view('franchise.stockorder.orderlist');
    }

    public function processStockOrderList(Request $request)
    {
        $franchise_id = auth('franchise')->id();
        $query = StockOrder::where('franchise_id', $franchise_id)->where(
            function ($query) {
                return $query
                    ->where('order_to', '=', '1')
                    ->orWhere('order_to', '=', '2')
                    ->orWhere(
                        function ($query2) {
                            return $query2
                                ->where('order_to', '=', 0)
                                ->where('order_reference', '!=', '""');
                        }
                    );
            }
        );
        if ($request->get('order_to') != '') {
            $query = $query->where('stock_orders.order_to', $request->get('order_to'));
            if ($request->get('order_to') == '0') {
                $query = $query->where('order_reference', '!=', '""');
            }
        }
        $column_order = ['id', 'order_reference', 'created_at', 'created_at']; //set column field database for datatable orderable
        $column_search = ['order_reference', 'order_reference', 'created_at', 'created_at']; //set column field database for datatable searchable
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
            $query = $query->orderBy('id', 'DESC');
        }

        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function stockOrderView(StockOrder $id)
    {
        $data['row'] = [];
        if ($id) {
            $data['row'] = $id;
            $data['orderdetail'] = StockOrderDetail::leftJoin('products', 'products.id', 'stock_order_details.product_id')->where('stock_order_id', $id->id)->get(['stock_order_details.product_id', 'product_name', 'product_article_number', 'qty']);
        }

        return view('franchise.stockorder.viewstockorder', $data);
    }

    public function stockProductList()
    {
        $data['products'] = Product::whereNull('deleted_at')->where('product_article_number', '!=', '')->get();
        $data['row'] = [];

        return view('franchise.stockorder.productlist', $data);
    }

    public function processStockProductList(Request $request)
    {

        $query = Product::whereNull('deleted_at');
        $column_order = []; //set column field database for datatable orderable
        $column_search = ['product_name', 'product_article_number', 'api_available_stock']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;
        //Search
        if ($request->get('order_to') != null) {
            $query = $query->where('order_from', $request->get('order_to'));
        } else {
            $query = $query->where('order_from', '0');
        }
        //Search
        if ($request->search['value'] && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }
        //Sorting
        if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('id', 'DESC');
        }

        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        // $data = $query->get();
        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function changeOrderStatus(Request $request)
    {
        $franchise_id = auth('franchise')->id();
        $order_id = $request->id;
        $detail = StockOrder::where('id', $order_id)->first();

        if ($detail['order_status'] == 'PENDING') {
            $orderDetail = StockOrderDetail::query()->where('stock_order_id', $order_id)->get();
            foreach ($orderDetail as $key => $value) {
                $stockdetail = Stock::query()->where('product_id', $value->product_id)->where('franchise_id', $franchise_id)->get();
                $count = $stockdetail->count();

                if ($count == 0) {
                    $stock = new Stock;
                    $stock->product_id = $value->product_id;
                    $stock->franchise_id = $franchise_id;
                    $stock->stock_current = $value->qty;
                    $stock->save();
                } else {
                    $total = $stockdetail[0]['stock_current'] + $value->qty;
                    Stock::where('id', $stockdetail[0]->id)->update(['stock_current' => $total]);
                }
            }
            StockOrder::where('id', $order_id)->update(['order_status' => 'COMPLETED']);
        }

        return response()
            ->json([
                'status'  => true,
                'message' => 'Status Updated!!',
                'page'    => 'franchise/stockorder/list',
            ]);
    }

    public function removeStockOrder(Request $request)
    {
        StockOrder::where('id', $request->id)->delete();
        StockOrderDetail::where('stock_order_id', $request->id)->delete();

        return response()
            ->json([
                'status'  => true,
                'message' => 'Odrer Deleted!!',
                'page'    => 'franchise/stockproduct/list',
            ]);
    }
}
