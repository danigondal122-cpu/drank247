<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class IDIN
{
	private static $config;

	private static function init(): self
	{
		self::$config = [
			'merchant_key' => env('IDIN_MERCHANT_KEY'),
			'url' => env('IDIN_REQUEST_URL'),
		];

		return new self;
	}

	protected static function request(string $url, array $params = []): Response
	{
		$data['merchant_token'] = self::$config['merchant_key'];

		if ($params)
		{
			$data = array_merge($data, $params);
		}

		$response = Http::withBody(
			http_build_query($data),
			'application/x-www-form-urlencoded'
		)->post($url);

		return $response;
	}

	public static function banks(): array
	{
		$url = self::$config['url'] . 'directory';
		$response = self::request($url);

		return $response->json();
	}

	public static function create(array $data): array
	{
		// Example $data: [
		//     'entrance_code' => sha1(rand(0, 5)),
		//     'merchant_return_url' => url('idin-thankyou'),
		//     'use_case' => '00',
		//     'expiration' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
		//     'size' => 200,
		//     'format' => 'png',
		//     'language' => 'nl',
		// ]

		$url = self::$config['url'] . 'qr/create';
		$response = self::request($url, $data);

		return $response->json();
	}

	public static function status(array $data): array
	{
		$url = self::$config['url'] . 'qr/status';
		$response = self::request($url, $data);

		return $response->json();
	}
}

(static function () {
	static::init();
})->bindTo(null, IDIN::class)();