<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Caller
{
	public static function cmPayment(array $data): array
	{
		$url = env('CMTEST_API_URL') . env('MERCHANT_KEY') . '/orders';
		$response = Http::withHeaders([
			'Content-Type' => 'application/json',
			'Authorization' => 'Basic ' . env('CM_AUTHORIZATION_TOKEN'),
			'Cookie' => 'BISCUIT=chocolatechip|YepOl'
		])
			->post($url, $data);

		return $response->json();
	}
}
