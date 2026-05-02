<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Assuming this is for extra product
 */
class StockOrder extends Model
{
    use SoftDeletes;

    protected $table = 'stock_orders';

    protected $guarded = [
        'id',
    ];

    protected $appends = ['order_date', 'pickup_date'];

    public function orderDate(): Attribute
    {
        return Attribute::get(
            fn () => $this->created_at ? now()->parse($this->created_at)->format('d-m-Y H:i:s') : null
        )
            ->shouldCache();
    }

    public function pickupDate(): Attribute
    {
        return Attribute::get(
            fn () => $this->pickup_delivery_date ? now()->parse($this->pickup_delivery_date)->format('d-m-Y H:i:s') : null
        )
            ->shouldCache();
    }

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    public function stockOrderDetails(): HasMany
    {
        return $this->hasMany(StockOrderDetail::class);
    }
}
