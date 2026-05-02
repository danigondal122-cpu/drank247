<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class ReCaptcha implements ValidationRule
{
	public function validate(string $attribute, mixed $value, Closure $fail): void
	{
		$response = Http::get('https://www.google.com/recaptcha/api/siteverify',[
			'secret' => env('GOOGLE_RECAPTCHA_SECRET_KEY'),
			'response' => $value
		]);
		  
		if (!$response->json()['success'])
		{
			$fail('Invalid captcha code.');
		}
	}
}
