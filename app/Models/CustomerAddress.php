<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'default',
        'address',
        'post_code',
        'latitude',
        'longitude',
        'house_no',
        'manual'
    ];

    public function scopeWhereDefault(Builder $query): void
    {
        $query->where('default', '1');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'order_address_id');
    }
}
