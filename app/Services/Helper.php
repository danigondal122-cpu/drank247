<?php

use App\Services\Cart;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

function isLocale(string $lang): bool
{
	return session()->has('locale')
		? session()->get('locale') == $lang
		: $lang == 'en';
}

function cart(): Cart
{
	return new Cart();
}

function nlPostcode(string|int $postcode, string|int $houseNum): Response
{
	// Example
	// postcode: 2012es
	// houseNum: 30

	$postcode = trim(str_replace(' ', '', $postcode));
	$response = Http::withHeaders([
			'Content-Type' => 'application/json',
			'token' => config('services.postcode.api_token'),
		])
		->withoutVerifying()
		->get(config('services.postcode.enpoint'), [
			'postcode' => $postcode,
			'number' => $houseNum,
		]);

	return $response;
}

function bluemConfig(string $brandType): stdClass
{
	// Repo: https://github.com/bluem-development/bluem-php

	$config = new stdClass();
	$brands = [
		'identity' => [
			'id' => '247DrankIdentity',
			'return_url' => env('APP_URL')
		],
		'payment' => [
			'id' => '247DrankPayment',
			'return_url' => env('MERCHANT_RETURN_URL_BASE')
		]
	];

	if (isset($brands[$brandType]))
	{
		$config->environment = env('BLUEM_ENVIRONMENT');
		$config->senderID = env('BLUEM_SENDERID');
		$config->test_accessToken = env('BLUEM_TEST_ACCESSTOKEN');
		$config->production_accessToken = env('BLUEM_PRODUCTION_ACCESSTOKEN');
		$config->merchantID = env('BLUEM_MERCHANTID');
		$config->brandID = $brands[$brandType]['id'];
		$config->merchantReturnURLBase = $brands[$brandType]['return_url'];
	}

	return $config;
}