<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class UberEats
{
	protected static string $scope = '';

	public static function getToken(string $scope = 'all'): array
	{
		$token = Cache::get('uber_eats_token', []);

		if ($scope != 'all' && !isset($token[$scope]))
		{
			$url = 'https://login.uber.com/oauth/v2/token';
			$data = [
				'client_id' => config('services.uber.client_id'),
				'client_secret' => config('services.uber.client_secret'),
				'grant_type' => 'client_credentials',
				'scope' => $scope,
			];

			$response = Http::asForm()->post($url, $data);
			$token[$scope] = $response->json();

			if ($response->status() == 200 && isset($token[$scope]['expires_in']))
			{
				Cache::put('uber_eats_token', $token, $token[$scope]['expires_in']);
			}
			else
			{
				$message = 'Uber Eats: ' . ($token[$scope]['error_description'] ?? 'Response Error.');
				throw new \Exception($message, $response->status());
			}
		}

		return $token[$scope] ?? $token;
	}

	protected static function request(string $url, string $method, array $data = []): Response
	{
		$token = self::getToken(self::$scope);
		$response = Http::withHeaders([
				'Content-Type' => 'application/json',
				'authorization' => 'Bearer ' . $token['access_token'],
			])
			->$method($url, $data);

		return $response;
	}

	public static function getStoreList(): Response
	{
		self::$scope = 'eats.store';
		$url = 'https://api.uber.com/v1/eats/stores';

		return self::request($url, 'get');
	}

	public static function getStoreDetail(string $storeId): Response
	{
		self::$scope = 'eats.store';
		$url = "https://api.uber.com/v2/eats/stores/$storeId/menus";

		return self::request($url, 'get');
	}

	public static function updateStoreDetail(string $storeId, array $data): Response
	{
		// Get the $data array from storeUpdateDetailParams();

		if (!$data)
		{
			$message = 'Uber Eats: empty data parameters.';
			throw new \Exception($message, 1);
		}

		self::$scope = 'eats.store';
		$url = "https://api.uber.com/v2/eats/stores/$storeId/menus";

		return self::request($url, 'put', $data);
	}

	public static function updateStoreItem(string $storeId, int|string $itemId, array $data): Response
	{
		// Example $data:
		// [
		// 	'price_info' => [
		// 		'price'	=> (float) ($price * 100),
		// 		'overrides' => [],
		// 	]
		// ]

		self::$scope = 'eats.store';
		$url = "https://api.uber.com/v2/eats/stores/$storeId/menus/items/$itemId";

		return self::request($url, 'post', $data);
	}

	public static function getOrderDetail(string $orderId): Response
	{
		self::$scope = 'eats.order';
		$url = "https://api.uber.com/v2/eats/order/$orderId";

		return self::request($url, 'get');
	}

	public static function cancelOrder(string $orderId, array $data): Response
	{
		// Example $data:
		// [
		// 	'reason' => 'OUT_OF_ITEMS',
		// ]

		self::$scope = 'eats.order';
		$url = "https://api.uber.com/v1/eats/orders/$orderId/cancel";

		return self::request($url, 'post', $data);
	}

	public static function updateOrderStatus(string $orderId, int $status): Response
	{
		self::$scope = 'eats.store.orders.restaurantdelivery.status';
		$url = "https://api.uber.com/v1/eats/orders/$orderId/restaurantdelivery/status";
		$statusList = [
			12 => 'started',
			6 => 'arriving',
			10 => 'delivered',
		];

		if (!isset($statusList[$status]))
		{
			$message = 'Uber Eats: Invalid status.';
			throw new \Exception($message, 1);
		}

		return self::request($url, 'post', [
			'status' => $statusList[$status]
		]);
	}

	public static function storeUpdateDetailParams(
		array $request,
		null|Collection $categories = null,
		array $products = []
	): array
	{
		$data = [
			'items' => [],
			'categories' => [],
			'menus' => [
				0 => [
					'service_availability' => [],
					'category_ids' => [],
					'id' => 'All-day',
					'title' => [
						'translations' => [
							'en_us' => 'All day',
						],
					]
				]
			],
			'modifier_groups' => [],
			'display_options' => [
				'disable_item_instructions' => true
			]
		];

		$days = [
			1 => 'monday',
			2 => 'tuesday',
			3 => 'wednesday',
			4 => 'thursday',
			5 => 'friday',
			6 => 'saturday',
			7 => 'sunday'
		];

		foreach ($days as $index => $day)
		{
			$start_time = ($request["starttime0_$index"] ?? '00') . ':' . ($request["starttime1_$index"] ?? '00');
			$end_time = ($request["endtime0_$index"] ?? '00') . ':' . ($request["endtime1_$index"] ?? '00');
			$openingHours[$index - 1] = [
				'time_periods' => [
					0 => [
						'start_time' => $start_time,
						'end_time' => $end_time
					],
				],
				'day_of_week' => $day,
			];
		}

		$data['menus'][0]['service_availability'] = $openingHours;

		if ($categories)
		{
			$data['menus'][0]['category_ids'] = $categories->pluck('category_name')->toArray();
	
			foreach ($categories as $category)
			{
				$cat = [
					'entities' => [],
					'id' => $category->category_name,
					'title' => [
						'translations' => ['en_us' => $category->category_name]
					]
				];
	
				foreach ($category->products as $product)
				{
					if (in_array($product->id, $products))
					{
						$data['items'][] = [
							'id' => (string) $product->id,
							'title' => [
								'translations' => [
									'en' => $product->product_name,
								],
							],
							'description' => [
								'translations' => [
									'en' => $product->description,
								],
							],
							'image_url' => $product->image,
							'price_info' => [
								'price' => round((float) ($product->vat_price * 100)),
								'overrides' => [],
							],
							'tax_info' => (object) [],
							'dish_info' => [
								'classifications' => [
									'ingredients' => NULL,
									'additives' => NULL,
									'alcoholic_items' => (int) $product->alcoholic_items,
								],
							],
							'product_info' => [
								'product_traits' => NULL,
								'countries_of_origin' => NULL,
								'product_type' => $product->productType?->product_type ?? ''
							],
							'bundled_items' => NULL,
						];
		
						$cat['entities'][] = [
							'type' => 'ITEM',
							'id' => (string) $product->id
						];
					}
				}
	
				$data['categories'][] = $cat;
			}
		}

		return $data;
	}
}