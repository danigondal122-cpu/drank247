<?php

namespace App\Services;

use App\Models\Category;
use App\Models\DeliveryTimeSchedule;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;
use ReflectionClass;

class GlobalData
{
	protected $customer;

	public function __construct()
	{
		$this->customer = auth('customer')->check() ? auth('customer')->user() : null;
	}

	public static function get(string $view, string|array $methods = 'all'): mixed
	{
		$data = [];
		$reflection = new ReflectionClass(self::class);
		$instance = new self();

		foreach ($reflection->getMethods() as $method) {
			$comment = $method->getDocComment();

			if (
				$method->isProtected() &&
				preg_match('/@view\s+' . preg_quote($view, '/') . '/', $comment)
			) {
				$methodName = $method->getName();

				if (is_string($methods) && $methods == 'all' || is_array($methods) && !$methods) {
					$data[$methodName] = $method->invoke($instance);
				} else if (is_string($methods) && $methods == $methodName) {
					$data = $method->invoke($instance);
				} else if (is_array($methods) && in_array($methodName, $methods)) {
					if (count($methods) > 1) {
						$data[$methodName] = $method->invoke($instance);
					} else {
						$data = $method->invoke($instance);
					}
				}
			}
		}

		return $data;
	}

	/**
	 * @view franchise
	 */
	protected function n_count(): int
	{
		return Notification::where('user_type', 'franchise')->where('to_id', auth('franchise')->user()->franchise_id)->where('status', '0')->get()->count();
	}

	/**
	 * @view franchise
	 */
	protected function Notification(): Collection
	{
		return Notification::where('user_type', 'franchise')->where('to_id', auth('franchise')->user()->franchise_id)->orderBy('id', 'DESC')->get();
	}

	/**
	 * @view user
	 */
	protected function category_nav(): Collection
	{
		$data = Category::whereNull('category_id')
			->where('is_show', 1)
			->whereNull('deleted_at')
			->orderBy('category_order', 'ASC')
			->get();

		return $data;
	}

	/**
	 * @view user
	 */
	protected function favourite_count(): int
	{
		$data = $this->customer
			?->favourites()
			->get()
			->count() ?? 0;

		return $data;
	}

	/**
	 * @view user
	 */
	protected function settings(): Setting|null
	{
		return Setting::latest()->first();
	}

	/**
	 * @view user
	 */
	protected function dayschedule(): Collection
	{
		return DeliveryTimeSchedule::all();
	}
}
