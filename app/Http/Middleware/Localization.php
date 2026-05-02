<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
	public function handle(Request $request, Closure $next): Response
	{
		$locale = session()->has('locale') ? session()->get('locale') : null;

		if (in_array($locale, ['en', 'nl']))
		{
			App::setLocale($locale);
		}

		return $next($request);
	}
}
