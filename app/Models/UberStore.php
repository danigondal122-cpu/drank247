<?php

namespace App\Models;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UberStore extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'store_id',
        'name',
        'location',
        'status',
        'contact_emails',
    ];

    /**
     * Get access token
     *
     * @param  string|null  $url
     * @param  string  $scope  see https://developer.uber.com/docs/eats/guides/authentication#scopes
     *
     * @see https://developer.uber.com/docs/eats/guides/authentication
     *
     * @return array{message:'success',status:true,access_token:string}|\Illuminate\Http\JsonResponse
     *
     * @throws TransferException bisa ConnectException, ClientException, ServerException lihat: https://docs.guzzlephp.org/en/stable/quickstart.html#exceptions
     *
     * @todo! kalau ada waktu, hapus $url param
     *
     * @todo! kalau ada waktu, cache access token
     *
     * @todo! kalau ada waktu, buat kodingan yang lebih jelas, hilangkan response()->json()
     */
    public static function getAccessToken($url = null, $scope)
    {
        $TokenReqData = [
            'client_id'     => config('services.uber.client_id'),
            'client_secret' => config('services.uber.client_secret'),
            'grant_type'    => 'client_credentials',
            'scope'         => $scope,
        ];

        $client = new GuzzleClient([
            'form_params' => $TokenReqData,
        ]);
        $url = 'https://login.uber.com/oauth/v2/token';
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

    /**
     * Update harga menu item?
     *
     * @param  string  $store_id
     * @param  string  $item_id
     * @param  string|int|float  $price
     * @return \Illuminate\Http\JsonResponse|void JsonResponse: {original:array{message:string,status:bool,data:array}}
     *
     * @todo! kalau ada waktu, buat kodingan yang lebih jelas, hilangkan response()->json()
     *
     * @see https://developer.uber.com/docs/eats/references/api/v2/post-eats-stores-storeid-menus-items-itemid
     */
    public static function updateUberStoreItem($store_id, $item_id, $price)
    {
        try {
            // Get eats.store token
            $tokenResponse = UberStore::getAccessToken(scope: 'eats.store');

            if ($tokenResponse['status'] == 1) {
                // Set order status
                $headers = [
                    'Content-Type'  => 'application/json',
                    'authorization' => 'Bearer ' . $tokenResponse['access_token'],
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
                $url = env('UBER_EATS_STORE_V2_URL') . $store_id . '/menus/items/' . $item_id;
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

    /**
     * @return \Illuminate\Http\JsonResponse|void JsonResponse: {original:array{message:string,status:bool,data:array}}
     *
     * @todo! kalau ada waktu, buat kodingan yang lebih jelas, hilangkan response()->json()
     *
     * @see https://developer.uber.com/docs/eats/references/api/v1/get-eats-stores
     */
    public static function getUberStoreList()
    {
        try {
            // Get eats.store token
            $tokenResponse = UberStore::getAccessToken(scope: 'eats.store');

            if ($tokenResponse['status'] == 1) {

                // Sync uber eats stores
                $headers = [
                    'Content-Type'  => 'application/json',
                    'authorization' => 'Bearer ' . $tokenResponse['access_token'],
                ];

                $client = new GuzzleClient([
                    'headers' => $headers,
                ]);
                $url = env('UBER_EATS_STORE_V1_URL');
                $response = $client->request('GET', $url);

                // Error response if it has error on getting stores
                if ($response->getStatusCode() != 200) {
                    return response()->json([
                        'message' => 'Please try again later.',
                        'status'  => false,
                        'data'    => [],
                    ], 500);
                }

                /**
                 * @var array<int,array{name:string,store_id:string,location:array,contact_emails:array,raw_hero_url:string,price_bucket:string,avg_prep_time:int,status:string,}> $stores
                 *
                 * @see https://developer.uber.com/docs/eats/references/api/v1/get-eats-stores#example-response
                 * @see https://developer.uber.com/docs/eats/references/api/v1/get-eats-stores-storeid#response-body-parameters
                 */
                $stores = json_decode($response->getBody()->getContents(), true)['stores'] ?? [];

                if (count($stores) > 0) {
                    foreach ($stores as $key => $store) {
                        UberStore::updateOrCreate([
                            'store_id' => $store['store_id'],
                        ], [
                            'name'           => $store['name'],
                            'location'       => json_encode($store['location']),
                            'status'         => $store['status'],
                            'contact_emails' => count($store['contact_emails']) > 0 ? implode(',', $store['contact_emails']) : '',
                        ]);
                    }

                    return response()->json([
                        'message' => 'Success!!.',
                        'status'  => true,
                        'data'    => [],
                    ], 500);
                }
                // return UberStore::get()->pluck('store_id')->toArray();
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
     * @param  string  $store_id
     * @return \Illuminate\Http\JsonResponse|void JsonResponse: {original:array{message:string,status:bool,data:array}}
     *
     * @todo! kalau ada waktu, buat kodingan yang lebih jelas, hilangkan response()->json()
     *
     * @see https://developer.uber.com/docs/eats/references/api/v2/get-eats-stores-storeid-menu
     */
    public static function getUberStoreMenu($store_id)
    {

        try {
            // Get eats.store token
            $tokenResponse = UberStore::getAccessToken(scope: 'eats.store');

            if ($tokenResponse['status'] == 1) {

                // Sync uber eats stores
                $headers = [
                    'Content-Type'  => 'application/json',
                    'authorization' => 'Bearer ' . $tokenResponse['access_token'],
                ];

                $client = new GuzzleClient([
                    'headers' => $headers,
                ]);
                $url = env('UBER_EATS_STORE_V2_URL') . $store_id . '/menus';
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
     * @param  string  $order_id
     * @return \Illuminate\Http\JsonResponse|void JsonResponse: {original:array{message:string,status:bool,data:array}}
     *
     * @todo! kalau ada waktu, buat kodingan yang lebih jelas, hilangkan response()->json()
     *
     * @see https://developer.uber.com/docs/eats/references/api/v1/post-eats-order-orderid-cancel
     */
    public static function cancelUberOrder($order_id)
    {
        $tokenResponse = UberStore::getAccessToken(scope: 'eats.order');

        if ($tokenResponse['status'] == 1) {
            // Set order status
            $headers = [
                'Content-Type'  => 'application/json',
                'authorization' => 'Bearer ' . $tokenResponse['access_token'],
            ];
            $requestData = [
                'reason' => 'OUT_OF_ITEMS',
            ];
            $client = new GuzzleClient([
                'headers' => $headers,
                'body'    => json_encode($requestData),
            ]);
            $url = 'https://api.uber.com/v1/eats/orders/' . $order_id . '/cancel';
            $response = $client->request('POST', $url);

            // Error response if it has error on getting stores
            // if ($response->getStatusCode() != 200) {
            //   return response()->json([
            //     'message' => 'Please try again later.',
            //     'status' => false,
            //     'data' => []
            //   ], 500);
            // }
            logger()->driver('uber')->info('cancelUberOrder', [
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
     * @param  string  $order_id
     * @param  int|string  $status  harus 12, 6 atau 10
     * @return \Illuminate\Http\JsonResponse|void void untuk sukses, JsonResponse untuk gagal: {original:array{message:string,status:bool,data:array}}
     *
     * @todo! kalau ada waktu, buat kodingan yang lebih jelas, hilangkan response()->json()
     *
     * @see https://developer.uber.com/docs/eats/references/api/v1/post-eats-orders-orderid-restaurantdelivery-status
     */
    public static function updateUberOrderStatus($order_id, $status)
    {

        $array = ['12' => 'started', '6' => 'arriving', '10' => 'delivered'];

        // Get eats.store.orders.restaurantdelivery.status token
        $tokenResponse = UberStore::getAccessToken(scope: 'eats.store.orders.restaurantdelivery.status');

        if ($tokenResponse['status'] == 1) {
            // Set order status
            $headers = [
                'Content-Type'  => 'application/json',
                'authorization' => 'Bearer ' . $tokenResponse['access_token'],
            ];
            $requestData = [
                'status' => $array[$status],
            ];
            $client = new GuzzleClient([
                'headers' => $headers,

                'body'    => json_encode($requestData),
            ]);
            $url = 'https://api.uber.com/v1/eats/orders/' . $order_id . '/restaurantdelivery/status';
            $response = $client->request('POST', $url);

            // Error response if it has error on getting stores
            // if ($response->getStatusCode() != 200) {
            //   return response()->json([
            //     'message' => 'Please try again later.',
            //     'status' => false,
            //     'data' => []
            //   ], 500);
            // }
            logger()->driver('uber')->info('updateUberOrderStatus', [
                ['order_id' => $order_id, 'status' => $status, 'data' => $response->getStatusCode()],
            ]);
        } else {
            return response()->json([
                'message' => 'Please try again later.',
                'status'  => false,
                'data'    => [],
            ], 500);
        }
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'order_store_id', 'store_id');
    }
}
