<?php

namespace App\Models;

use App\Services\Media;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'customers';

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function favourites(): HasMany
    {
        return $this->hasMany(Favourite::class);
    }

    public function usedPromoCodes(): HasMany
    {
        return $this->hasMany(UsedPromoCode::class);
    }

    public function rateAndReviews(): HasMany
    {
        return $this->hasMany(RateAndReview::class);
    }

    public function address()
    {
        return $this->hasOne(CustomerAddress::class, 'customer_id', 'id');
    }

    protected function profile(): Attribute
	{
		return Attribute::make(
			get: fn ($value): Media => new Media(
				$value,
				asset('images/user-circle-solid.svg'),
				[
                    'image' => public_path('uploads/customer/'),
				    'thumb' => public_path('uploads/customer/thumb/'),
                ]
			)
		);
	}
}
