<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Takeaway
{
	public static function getOrders(): Response
	{
        $storeId = config('services.takeaway.mode') == 'Staging'
            ? config('services.takeaway.staging_store_id')
            : config('services.takeaway.live_store_id')
        ;

        $username = config('services.takeaway.username');
		$password = config('services.takeaway.password');

		// Old Endpoint = 'https://posapi.takeaway.com/1.0/orders/'
        $url = "https://pull-posapi.takeaway.com/1.1/orders/$storeId";

		$response = Http::withoutVerifying()
			->withHeaders([
				'Content-Type' => 'application/json',
				'Apikey' => config('services.takeaway.api_key'),
			])
			->withBasicAuth($username, $password)
			->get($url);

		return $response;
	}

    public static function orderStatus(array $data): Response
	{
		// Old Endpoint = 'https://posapi.takeaway.com/1.0/status'
        $url = 'https://pull-posapi.takeaway.com/1.0/status';
        $response = Http::withoutVerifying()
			->withHeaders([
				'Content-Type' => 'application/json'
			])
			->post($url, $data);
        
        return $response;
    }
}