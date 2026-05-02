<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class CustomerSocialAuthController extends Controller
{
	protected $drivers = ['google', 'facebook'];

	public function redirect($driver): RedirectResponse
	{
		if (in_array($driver, $this->drivers))
		{
			return Socialite::driver($driver)->redirect();
		}

		abort(404);
	}

	public function callback($driver): RedirectResponse
	{
		if (in_array($driver, $this->drivers))
		{
			try
			{
				$socialite = Socialite::driver($driver)->user();
				$customer = Customer::firstOrCreate([
					'social_login_id' => $socialite->getId(),
					'login_type' => Str::upper($driver),
				], [
					'customer_name' => $socialite->getName(),
					'customer_email' => $socialite->getEmail(),
					'password' => Hash::make($socialite->getId()),
					'customer_type' => 0,
					'is_verified' => 'TRUE',
				]);

				$credentials = [
					'customer_email' => $customer->customer_email,
					'password' => $customer->social_login_id,
				];

				if ($driver == 'facebook')
				{
					$credentials['social_login_id'] = $socialite->getId();
				}
				
				if (auth('customer')->attempt($credentials))
				{
					return redirect('/');
				}

				return redirect()->route('social.auth', $driver);
			}
			catch (Exception $e)
			{
				return redirect()->route('social.auth', $driver);
			}
		}

		abort(404);
	}
}