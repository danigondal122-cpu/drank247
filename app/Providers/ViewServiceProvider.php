<?php

namespace App\Providers;

use App\Services\GlobalData;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ViewServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		//
	}

	public function boot(): void
	{
		View::composer('*', function ($view) {
			$isUserLayout = $view->getName() == 'layouts.user';
			$isFranchiseLayout = $view->getName() == 'franchise.layout.layout';
			$isUserView = in_array(explode('.', $view->getName())[0] ?? '', [
				'guest',
				'customer',
				'customer_service'
			]);

			if ($isFranchiseLayout) {
				$view->with('global', GlobalData::get('franchise'));
			}
			if ($isUserLayout || $isUserView) {
				$view->with('global', GlobalData::get('user'));
			}
		});
	}
}
