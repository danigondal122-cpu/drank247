<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Deliverect
{
	protected static string $mode, $origin;

	protected static array $status = [
		1 => 10,
		2 => 10,
		3 => 50,
		4 => 60,
		5 => 70,
		6 => 80,
		7 => 110,
		8 => 110,
		9 => 50,
		10 => 90,
		11 => 110,
		12 => 20,
	];

	// Drank Order Status:
	// 1 = ORDER PLACED
	// 2 = APPROVED
	// 3 = PREPARING
	// 4 = PREPARED
	// 5 = READY FOR PICKUP
	// 6 = DELIVERED
	// 7 = REJECTED
	// 8 = FAILED
	// 9 = PENDING
	// 10 = FINALIZED
	// 11 = CANCELED
	// 12 = ACCEPTED
	// 22 = COMPLETED
	// 23 = IN PROGRESS

	// Deliverect Order Status:
	// 10 = New
	// 20 = Accepted
	// 40 = Printed
	// 50 = Preparing
	// 60 = Prepared
	// 70 = Pickup Ready
	// 90 = Finalized
	// 95 = Auto_Finalized
	// 110 = Canceled
	// 120 = Failed

	protected static function init(): self
	{
		$origins = [
			'Staging' => 'https://api.staging.deliverect.com',
			'Production' => 'https://api.deliverect.com'
		];
		self::$mode = config('services.deliverect.mode', 'Staging');
		self::$origin = $origins[self::$mode];

		return new self();
	}

    public static function getToken(): array
	{
        $token = Cache::get('deliverect_token');

        if (!$token)
        {
            $url = self::$origin . '/oauth/token';
            $data = [
                'client_id' => config('services.deliverect.client_id'),
                'client_secret' => config('services.deliverect.client_secret'),
                'audience' => 'https://api.deliverect.com',
                'grant_type' => 'client_credentials',
            ];

            $response = Http::withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post($url, $data);
            $token = $response->json();

			if ($response->status() == 200 && isset($token['expires_in']))
			{
				$token['expires_time'] = date('d-m-Y H:i:s', $token['expires_at']);
				$token['created_at'] = time();
				$token['created_time'] = date('d-m-Y H:i:s', $token['created_at']);

				Cache::put('deliverect_token', $token, $token['expires_in']);
			}
			else
            {
                $message = 'Deliverect: '. ($token['description'] ?? 'Response Error.');
                throw new \Exception($message, $response->status());
            }
        }
        else
        {
            $token['expires_in'] = $token['expires_at'] - time();
        }

		return $token;
	}

	protected static function request(string $url, string $method, array $data = []): Response
	{
        $token = self::getToken();
		$response = Http::withHeaders([
				'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token['access_token'],
			])
			->$method($url, $data);

		return $response;
	}

	public static function productAndCategories(string $method, array $data = []): Response
	{
		if (in_array($method, ['create', 'update', 'delete']))
		{
			$url = self::$origin . '/productAndCategories';
			$method = 'post';
			$data['accountId'] = config('services.deliverect.account_id');
			$data['locationId'] = config('services.deliverect.location_id');
		}
		else if ($method == 'get')
		{
			$url = self::$origin . '/productCategories';
			$method = 'get';
			$data = [
				'where' => json_encode(['account' => config('services.deliverect.account_id')]),
			];
		}
		else
		{
			$message = 'Deliverect: Invalid method.';
            throw new \Exception($message, 1);
		}

		return self::request($url, $method, $data);
	}

    /**
     * @param  string  $order_id
     * @param  string  $receipt_id
     * @param int|string status
     */
    public static function deliverectOrderStatus($order_id, $receipt_id, $status): Response
    {
        return self::updateOrderStatus([
            'orderId'   => $order_id,
            'status'    => $status,
            'reason'    => '',
            'timeStamp' => date('yy-m-d H:i:s').'.000Z',
            'receiptId' => $receipt_id,
        ]);
    }

    /**
     * @param  array{orderId:string,status:string|int,reason:string,timeStamp:string|DateTime,receiptId:string}  $data
     */
	public static function updateOrderStatus(array $data): Response
	{
		$url = self::$origin . '/orderStatus' . (isset($data['orderId']) ? '/' . $data['orderId'] : '');
		$status = self::$status[$data['status'] ?? 0] ?? 0;

		if ($status) $data['status'] = $status;

		return self::request($url, 'post', $data);
	}

	public static function getAllAllergens(): Response
	{
		$url = self::$origin . '/allAllergens';

		return self::request($url, 'get');
	}
}

(static function () {
	static::init();
})->bindTo(null, Deliverect::class)();
