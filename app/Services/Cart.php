<?php

namespace App\Services;

use App\Models\Pool;
use ArrayAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LogicException;

class Cart
{
	public static function add(array $data): void
	{
		$cart = session()->get('cart', []);

		if (isset($cart[$data['id'] ?? null]))
		{
			self::update($data['id'], $data);
		}
		else
		{
			if (isset($data['id']))
			{
				$data = [$data];
			}

			foreach ($data as $item)
			{
				if (isset($item['id']))
				{
					$cart[$item['id']] = $item;
				}
				else
				{
					$item['id'] = Str::random(5);
					$cart[$item['id']] = $item;
				}
			}

			session()->put('cart', $cart);
		}
	}

	public static function get(int|string $id = 'all'): Collection
	{
		$cart = session()->get('cart', []);

		if ($id != 'all')
		{
			if (isset($cart[$id]))
			{
				$cart = [$cart[$id]];
			}
			else
			{
				$cart = [];
			}
		}
		
		return collect($cart)->map(function($item) {
			return new class($item) implements ArrayAccess {
				public $attributes;
	
				public function __construct(array $attributes)
				{
					$this->attributes = $attributes;
				}

				protected function getValue(
					string $key,
					mixed $default = null,
					array $arr = [],
					bool $nested = true
				): mixed
				{
					foreach ($arr ?: $this->attributes as $index => $value)
					{
						if ($index == $key)
						{
							return $value;
						}

						if ($nested && is_array($value))
						{
							$result = $this->getValue(
								$key,
								$default,
								$value,
								$nested
							);

							if ($result !== null)
							{
								return $result;
							}
						}
					}

					return $default;
				}

				public function total(string $key): int|float
				{
					$price = $this->getValue($key, 0);
					$qty = $this->getValue('qty', 0);

					return $price * $qty;
				}

				public function format(
					string $key,
					int $decimals = 0,
					string $decimal_separator = '.',
					string $thousands_separator = ','
				): string
				{
					if (Str::contains($key, ':'))
					{
						$arr = explode(':', $key);
						$key = $arr[0] ?? $key;
						$params = array_map('trim', explode(',', $arr[1] ?? ''));
					}
					
					$num = (float) ($this->getValue($key) ?: 
						(method_exists($this, $key)
							? (!empty($params) ? $this->$key(...$params) : $this->$key())
							: 0
						)
					);

					return number_format(
						$num,
						$decimals,
						$decimal_separator,
						$thousands_separator
					);
				}

				public function __get($key): mixed
				{
					$value = $this->getValue(key: $key, nested: false);

					if ($value)
					{
						return is_array($value)
							? (new self($value))
							: $value;
					}

					return $value;
				}

				public function offsetExists($offset): bool
				{
					return isset($this->attributes[$offset]);
				}

				public function offsetGet($offset): mixed
				{
					return $this->attributes[$offset];
				}

				public function offsetSet($offset, $value): void
				{
					throw new LogicException('Cart data may not be mutated using array access.');
				}

				public function offsetUnset($offset): void
				{
					throw new LogicException('Cart data may not be mutated using array access.');
				}
			};
		});
	}

	public static function update(int|string $id, mixed $data): bool
	{
		$cart = session()->get('cart', []);

		if (isset($cart[$id]))
		{
			$item = $cart[$id];

			if (is_int($data) || is_string($data) && isset($item['qty']))
			{
				$item['qty'] = $data;
			}
			else if (is_array($data))
			{
				if (isset($item['qty']) && isset($data['qty']))
				{
					$data['qty'] += $item['qty'];
				}
				
				$item = array_merge($item, $data);
			}

			if ($item['qty'])
			{
				$cart[$id] = $item;
			}
			else
			{
				unset($cart[$id]);
			}

			session()->put('cart', $cart);

			return true;
		}
		
		return false;
	}

	public static function count(): int
	{
		return count(session()->get('cart', []));
	}

	public static function subtotal(string $key = 'vat_price'): int
	{
		$subtotal = 0;

		foreach (self::get() as $item)
		{
			if (in_array($key, ['vat_price', 'original_price']))
			{
				$subtotal += (float) $item->total($key);
			}
		}

		return $subtotal;
	}

	public static function payment(
		null|string $key = null,
		array $discount = [],
		null|string $postcode = null
	): mixed
	{
		$deliveryFreeFrom = 75;
		$deliveryCharge = 0;
		$discountAmount = 0.00;
		$subtotal = self::subtotal();
		$customer = auth('customer')->check() ? auth('customer')->user() : null;
		
		if ($customer && !$postcode)
		{
			$customerAddress = $customer->address()->whereDefault()->first();
			$postcode = $customerAddress?->post_code;
		}
		
		if ($postcode)
		{
			$postcode = preg_replace('/[^0-9.]+/', '', $postcode);
			$pool = Pool::whereAttr($postcode, $subtotal)->first();

			if ($pool)
			{
				$deliveryCharge = $pool->delivery_charge;
			}
		}

		if (!$deliveryCharge && $subtotal < $deliveryFreeFrom)
		{
			$deliveryCharge = 2.50;
		}

		$total = $subtotal ? $subtotal + $deliveryCharge : $subtotal;

		if (isset($discount['type'], $discount['inper']))
		{
			if ($discount['type'] == 0)
			{
				$discountAmount = (float) $discount['inper'];
			}
			else if ($discount['type'] == 1)
			{
				$discountAmount = $total * ((int) $discount['inper'] / 100);
			}

			$totalWithDiscount = $total ? $total - $discountAmount : $total;
		}

		$data = [
			'delivery_charge' => number_format($deliveryCharge, 2),
			'discount_amount' => number_format($discountAmount, 2),
			'total' => number_format($total, 2),
			'total_with_discount' => number_format($totalWithDiscount ?? $total, 2),
		];

		return $key ? ($data[$key] ?? null) : $data;
	}

	public static function remove(int|string $id): bool
	{
		$cart = session()->get('cart', []);

		if (isset($cart[$id]))
		{
			unset($cart[$id]);
			session()->put('cart', $cart);

			return true;
		}
		
		return false;
	}

	public static function destroy(): void
	{
		session()->forget('cart');
	}

	public static function format(
		string $key,
		int $decimals = 0,
		string $decimal_separator = '.',
		string $thousands_separator = ','
	): string
	{
		$cart = session()->get('cart', []);
		$num = (float) (($key == 'subtotal' ? self::$key() : null) ?? $cart[$key] ?? 0);

		return number_format(
			$num,
			$decimals,
			$decimal_separator,
			$thousands_separator
		);
	}
}