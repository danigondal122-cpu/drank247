<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $table = 'warehouses';

    protected $guarded = [
        'id',
    ];

    protected $appends = [
        'image',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->wh_logo != '') {
                    if (file_exists('uploads/warehouse/'.$this->wh_logo)) {
                        return asset('uploads/warehouse').'/'.$this->wh_logo;
                    } else {
                        return asset('img/247-Drank-Logo.png');
                    }

                } else {
                    return asset('img/247-Drank-Logo.png');
                }
            },
        );
    }

    public function franchiseStockOrders(): HasMany
    {
        return $this->hasMany(FranchiseStockOrder::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'order_from', 'id');
    }
}
