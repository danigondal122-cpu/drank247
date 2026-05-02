<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\UberStore;
use App\Services\UberEats;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminUberAdminController extends Controller
{
    public function index()
    {
        return view('admin.uber.storelist');
    }

    public function storeView($id): View
    {
        $data = [
            'row'              => UberStore::withTrashed(false)->find($id),
            'time'             => [],
            'uber_item'        => [],
            'uber_categoryids' => [],
            'uber_productids'  => [],
        ];

        if (! $data['row']) {
            abort(404);
        }

        $data['category'] = Category::withCount('products')
            ->having('products_count', '>', 0)
            ->orderBy('category_name', 'asc')
            ->get();
        $data['product'] = Product::orderBy('product_name', 'asc')->get();
        $data['row']['location'] = json_decode($data['row']['location']);

        if ($data['row']['store_menu']) {
            $store_menu = json_decode($data['row']['store_menu']);
            $schedule = $store_menu->menus[0]->service_availability;
            $items = collect($store_menu->items)->sortBy('id')->values();

            $data['uber_categoryids'] = $store_menu->menus[0]->category_ids;
            $data['uber_productids'] = $items->pluck('id')->toArray();
            $time_periods = [];

            foreach ($schedule as $key => $value) {
                $time_periods[$key]['day'] = $value->day_of_week;
                $start_time = explode(':', $value->time_periods[0]->start_time);
                $end_time = explode(':', $value->time_periods[0]->end_time);
                $time_periods[$key]['start_time0'] = $start_time[0];
                $time_periods[$key]['start_time1'] = $start_time[1];
                $time_periods[$key]['end_time0'] = $end_time[0];
                $time_periods[$key]['end_time1'] = $end_time[1];
            }

            $data['time'] = $time_periods;
            $uber_item = [];

            foreach ($items as $key => $value) {
                $uber_item[$key]['id'] = $value->id;
                $uber_item[$key]['name'] = $value->title->translations->en;
                $uber_item[$key]['image_url'] = isset($value->image_url) ? $value->image_url : '';
                $uber_item[$key]['price_info'] = ($value->price_info->price) / 100;
            }

            $data['uber_item'] = $uber_item;
        }

        return view('admin.uber.storeview', $data);
    }

    public function storeList(Request $request)
    {
        $query = UberStore::whereNull('deleted_at');
        $column_order = ['name', 'store_id', 'status']; //set column field database for datatable orderable
        $column_search = ['name', 'store_id', 'status']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;
        $rawQuery = '';
        //Search
        if ($request->search['value'] && $request->search['value'] != '') {
            $search = $request->search['value'];
            $i = 0;
            foreach ($column_search as $key => $value) {
                // dd($value);
                if ($i === 0) { // first loop
                    $rawQuery .= '('; // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $rawQuery .= $value.' LIKE "%'.$search.'%"';
                } else {
                    $rawQuery .= ' OR '.$value.' LIKE "%'.$search.'%"';
                }
                if (count($column_search) - 1 == $i) {
                    //last loop
                    $rawQuery .= ')'; //close bracket
                }
                $i++;
            }
            $query = $query->whereRaw($rawQuery);
        }
        //Sorting
        if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('id', 'ASC');
        }

        $total = $query->get()->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function getProductList(Request $request)
    {
        $category_ids = $request->category_ids;
        $uber_productids = explode(',', $request->uber_productids);
        $category_ids = explode(',', $category_ids);
        $products = Product::whereIn('category_id', $category_ids)->orderBy('product_name', 'ASC')->get();

        $html = '';
        foreach ($products as $value) {
            if (in_array($value['id'], $uber_productids)) {
                $checked = 'selected';
            } else {
                $checked = '';
            }
            $html .= '<option value="'.$value['id'].'" '.$checked.' >'.$value->product_name.'</option> ';
        }

        return response()->json(['status' => true, 'html' => $html]);
    }

    public function getStoreList(): JsonResponse
    {
        $response = UberEats::getStoreList();

        if (isset($response['stores'])) {
            foreach ($response['stores'] as $store) {
                UberStore::updateOrCreate([
                    'store_id' => $store['store_id'],
                ], [
                    'name'           => $store['name'],
                    'location'       => json_encode($store['location']),
                    'status'         => $store['status'],
                    'contact_emails' => implode(',', $store['contact_emails']),
                ]);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Success!!',
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Please try again!!',
        ]);
    }

    public function getStoreMenu(Request $request): JsonResponse
    {
        $response = UberEats::getStoreDetail($request->store_id);

        if ($response->status() == 200) {
            UberStore::where('store_id', $request->store_id)->update([
                'store_menu' => $response->body(),
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Success!!',
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Please try again!!',
        ]);
    }

    public function updateStoreItem(Request $request): JsonResponse
    {
        $response = UberEats::updateStoreItem($request->store_id, $request->item_id, [
            'price_info' => [
                'price'     => (float) ($request->price * 100),
                'overrides' => [],
            ],
        ]);

        if ($response->status() == 200 || $response->status() == 204) {
            $response = UberEats::getStoreDetail($request->store_id);

            if ($response->status() == 200) {
                UberStore::where('store_id', $request->store_id)->update([
                    'store_menu' => $response->body(),
                ]);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Success!!',
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Please try again!!',
        ]);
    }

    public function syncUberMenu(Request $request): JsonResponse
    {
        try {
            $categories = Category::with('products')->whereIn('id', $request->category)->get();
            $data = UberEats::storeUpdateDetailParams($request->all(), $categories, $request->product);
            $response = UberEats::updateStoreDetail($request->store_id, $data);

            if ($response->status() == 200 || $response->status() == 204) {
                $response = UberEats::getStoreDetail($request->store_id);

                if ($response->status() == 200) {
                    UberStore::where('store_id', $request->store_id)->update([
                        'store_menu' => $response->body(),
                    ]);
                }

                return response()->json([
                    'message' => 'success',
                    'status'  => true,
                    'data'    => [],
                ], 200);
            }

            return response()->json([
                'status'  => false,
                'message' => 'Please try again!!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Please try again later.',
                'status'  => false,
                'data'    => [],
            ], 500);
        }
    }

    /**
     * @deprecated Unused
     */
    public function getAccessToken($url, $scope)
    {
        $ToeknReqData = [
            'client_id'     => env('UBER_EATS_CLIENT_ID'),
            'client_secret' => env('UBER_EATS_CLIENT_SECRET'),
            'grant_type'    => 'client_credentials',
            'scope'         => $scope,
        ];

        $client = new GuzzleClient([
            'form_params' => $ToeknReqData,
        ]);
        $url = env('UBER_EATS_TOKEN_URL');
        $tokenResponse = $client->request('POST', $url);

        if ($tokenResponse->getStatusCode() != 200) {
            return response()->json([
                'message' => 'Please try again later.',
                'status'  => false,
                'data'    => [],
            ], 500);
        } else {
            $token = json_decode($tokenResponse->getBody()->getContents())->access_token;

            return [
                'message'      => 'success',
                'status'       => true,
                'access_token' => $token,
            ];
        }
    }

    public function updateUberStoreItem($store_id, $item_id, $price)
    {
        try {
            // Get eats.store token
            $url = env('UBER_EATS_TOKEN_URL');
            $scope = 'eats.store';
            $tokenResponse = UberStore::getAccessToken($url, $scope);

            if ($tokenResponse['status'] == 1) {
                // Set order status
                $headers = [
                    'Content-Type'  => 'application/json',
                    'authorization' => 'Bearer '.$tokenResponse['access_token'],
                ];
                $requestData = [
                    'price_info' => [
                        'price'     => (float) ($price * 100),
                        'overrides' => [],
                    ],
                ];
                $client = new GuzzleClient([
                    'headers' => $headers,
                    'body'    => json_encode($requestData),
                ]);
                $url = env('UBER_EATS_STORE_V2_URL').$store_id.'/menus/items/'.$item_id;
                $response = $client->request('POST', $url);

                // Error response if it has error on getting stores
                if ($response->getStatusCode() != 200 && $response->getStatusCode() != 204) {
                    return response()->json([
                        'message' => 'Please try again later.',
                        'status'  => false,
                        'data'    => [],
                    ], 500);
                } else {
                    return response()->json([
                        'message' => 'Success!!',
                        'status'  => true,
                        'data'    => [],
                    ], 500);
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Please try again later.',
                'status'  => false,
                'data'    => [],
            ], 500);
        }
    }

    // public function getUberStoreList()
    // {
    //   try {
    //     // Get eats.store token
    //     $url = env('UBER_EATS_TOKEN_URL');
    //     $scope = 'eats.store';
    //     $tokenResponse = UberStore::getAccessToken($url, $scope);

    //     if ($tokenResponse['status'] == 1) {

    //       // Sync uber eats stores
    //       $headers = [
    //         'Content-Type' => 'application/json',
    //         'authorization' => 'Bearer ' . $tokenResponse['access_token'],
    //       ];

    //       $client = new GuzzleClient([
    //         'headers' => $headers,
    //       ]);
    //       $url = env('UBER_EATS_STORE_V1_URL');
    //       $response = $client->request('GET', $url);

    //       // Error response if it has error on getting stores
    //       if ($response->getStatusCode() != 200) {
    //         return response()->json([
    //           'message' => 'Please try again later.',
    //           'status' => false,
    //           'data' => []
    //         ], 500);
    //       }

    //       $response = json_decode($response->getBody()->getContents())->stores;

    //       if (count($response) > 0) {
    //         foreach ($response as $key => $store) {
    //           UberStore::updateOrCreate([
    //             'store_id' => $store->store_id,
    //           ], [
    //             'name' => $store->name,
    //             'location' => json_encode($store->location),
    //             'status' => $store->status,
    //             'contact_emails' => count($store->contact_emails) > 0 ? implode(',', $store->contact_emails) : '',
    //           ]);
    //         }
    //         return response()->json([
    //           'message' => 'Success!!.',
    //           'status' => true,
    //           'data' => []
    //         ], 500);
    //       }
    //       // return UberStore::get()->pluck('store_id')->toArray();
    //     }
    //   } catch (\Throwable $th) {
    //     echo $th;
    //     return response()->json([
    //       'message' => 'Please try again later.',
    //       'status' => false,
    //       'data' => []
    //     ], 500);
    //   }
    // }

    /**
     * @deprecated unused
     */
    public static function getUberStoreMenu($store_id)
    {
        try {
            // Get eats.store token
            $url = env('UBER_EATS_TOKEN_URL');
            $scope = 'eats.store';
            $tokenResponse = UberStore::getAccessToken($url, $scope);

            if ($tokenResponse['status'] == 1) {

                // Sync uber eats stores
                $headers = [
                    'Content-Type'  => 'application/json',
                    'authorization' => 'Bearer '.$tokenResponse['access_token'],
                ];

                $client = new GuzzleClient([
                    'headers' => $headers,
                ]);
                $url = env('UBER_EATS_STORE_V2_URL').$store_id.'/menus';
                $response = $client->request('GET', $url);

                // Error response if it has error on getting stores
                if ($response->getStatusCode() != 200) {
                    return response()->json([
                        'message' => 'Please try again later.',
                        'status'  => false,
                        'data'    => [],
                    ], 500);
                } else {
                    $menuDetail = json_decode($response->getBody()->getContents());
                    UberStore::where('store_id', $store_id)->update(['store_menu' => json_encode($menuDetail)]);

                    return response()->json([
                        'message' => 'Success!!.',
                        'status'  => true,
                        'data'    => [],
                    ], 500);
                }
            }
        } catch (\Throwable $th) {
            echo $th;

            return response()->json([
                'message' => 'Please try again later.',
                'status'  => false,
                'data'    => [],
            ], 500);
        }
    }

    /**
     * @deprecated unused
     */
    public static function cancelUberOrder($order_id)
    {
        $url = env('UBER_EATS_TOKEN_URL');
        $scope = 'eats.order';
        $tokenResponse = UberStore::getAccessToken($url, $scope);

        if ($tokenResponse['status'] == 1) {
            // Set order status
            $headers = [
                'Content-Type'  => 'application/json',
                'authorization' => 'Bearer '.$tokenResponse['access_token'],
            ];
            $requestData = [
                'reason' => 'OUT_OF_ITEMS',
            ];
            $client = new GuzzleClient([
                'headers' => $headers,
                'body'    => json_encode($requestData),
            ]);
            $url = env('UBER_EATS_ORDER_V1_URL').$order_id.'/cancel';
            $response = $client->request('POST', $url);

            // Error response if it has error on getting stores
            // if ($response->getStatusCode() != 200) {
            //   return response()->json([
            //     'message' => 'Please try again later.',
            //     'status' => false,
            //     'data' => []
            //   ], 500);
            // }
            DB::table('uber')->insert([
                ['order_id' => $order_id, 'data' => $response->getStatusCode()],
            ]);
        } else {
            return response()->json([
                'message' => 'Please try again later.',
                'status'  => false,
                'data'    => [],
            ], 500);
        }
    }

    /**
     * @deprecated unsused
     */
    public static function updateUberOrderStatus($order_id, $status)
    {
        $array = ['12' => 'started', '6' => 'arriving', '10' => 'delivered'];

        // Get eats.store.orders.restaurantdelivery.status token
        $url = env('UBER_EATS_TOKEN_URL');
        $scope = 'eats.store.orders.restaurantdelivery.status';
        $tokenResponse = UberStore::getAccessToken($url, $scope);

        if ($tokenResponse['status'] == 1) {
            // Set order status
            $headers = [
                'Content-Type'  => 'application/json',
                'authorization' => 'Bearer '.$tokenResponse['access_token'],
            ];
            $requestData = [
                'status' => $array[$status],
            ];
            $client = new GuzzleClient([
                'headers' => $headers,
                'body'    => json_encode($requestData),
            ]);
            $url = env('UBER_EATS_ORDER_V1_URL').$order_id.'/restaurantdelivery/status';
            $response = $client->request('POST', $url);

            // Error response if it has error on getting stores
            // if ($response->getStatusCode() != 200) {
            //   return response()->json([
            //     'message' => 'Please try again later.',
            //     'status' => false,
            //     'data' => []
            //   ], 500);
            // }
            DB::table('ubers')->insert([
                ['order_id' => $order_id, 'data' => $response->getStatusCode()],
            ]);
        } else {
            return response()->json([
                'message' => 'Please try again later.',
                'status'  => false,
                'data'    => [],
            ], 500);
        }
    }
}
