<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PayNL
{
	// Doc: https://docs.pay.nl/paymentoptions
	
	protected static array $paymentOptions = [
		'bancontact' => 436,
		'giropay' => 694,
		'sofort_banking' => 559,
		'trustly' => 2718,
		'eps_uberweisung' => 2062,
		'przelewy24' => 2151,
		'klarna' => 1717,
		'paypal_express_checkout' => 138,
		'ideal' => 10,
	];

	protected static string $auth;

	protected static function request(string $url, array $data = []): Response
	{
		$auth = config('services.paynl.auth', []);
		$username = $auth[self::$auth]['username'] ?? null;
		$password = $auth[self::$auth]['password'] ?? null;
		$data = array_merge($data, [
			'serviceid' => config('services.paynl.service_id'),
			'testMode' => config('services.paynl.test_mode'),
			'language' => 'NL',
		]);

		$response = Http::withoutVerifying()
			->withHeaders([
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Cache-Control' => 'no-cache',
			])
			->withBasicAuth($username, $password)
			->asForm()
			->post($url, $data);

		return $response;
	}

	public static function getBanks(string $param): Response
	{
		if ($param == 'ideal')
		{
			$url = 'https://rest-api.pay.nl/v5/Transaction/getBanks/json';
			$response = Http::get($url);
		}
		else
		{
			self::$auth = 'idin';
			$url = 'https://rest-api.pay.nl/v1/IDIN/getIssuers/json';
			$response = self::request($url);
		}

		return $response;
	}

	public static function transactionCreate(array $data, string $paymentMethod = ''): Response
	{
		self::$auth = 'transaction';
		$url = 'https://rest-api.pay.nl/v7/Transaction/start/json';

		if ($paymentMethod && isset(self::$paymentOptions[$paymentMethod]))
		{
			$data['paymentOptionId'] = self::$paymentOptions[$paymentMethod];
		}

		return self::request($url, $data);
	}

	public static function transactionStatus(array $data): Response
	{
		self::$auth = 'transaction';
		$url = 'https://rest-api.pay.nl/v13/Transaction/status/json';

		return self::request($url, $data);
	}

	public static function idinTransaction(array $data): Response
	{
		self::$auth = 'idin';
		$url = 'https://rest-api.pay.nl/v1/IDIN/authenticate/json';

		return self::request($url, $data);
	}

	public static function idinStatus(array $data): Response
	{
		self::$auth = 'idin';
		$url = 'https://rest-api.pay.nl/v1/IDIN/status/json';

		return self::request($url, $data);
	}

	public static function isPaymentMethodExist(string $paymentMethod): bool
	{
		if ($paymentMethod == 'idin') return true;

		return isset(self::$paymentOptions[$paymentMethod]);
	}
}