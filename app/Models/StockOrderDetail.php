<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOrderDetail extends Model
{
    use SoftDeletes;

    protected $table = 'stock_order_details';

    public function stockOrder(): BelongsTo
    {
        return $this->belongsTo(StockOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
