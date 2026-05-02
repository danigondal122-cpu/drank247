<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    protected $append = [
        'picture',
    ];

    protected function casts(): array
    {
        return [
            'is_popular' => 'boolean',
            'is_show'    => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function favourites(): HasMany
    {
        return $this->hasMany(Favourite::class);
    }

    public function franchiseStockOrders(): HasMany
    {
        return $this->hasMany(FranchiseStockOrder::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withPivot('created_at');
    }

    public function allergens(): BelongsToMany
    {
        return $this->belongsToMany(Allergen::class)->withPivot('created_at');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function stockOrderDetails(): HasMany
    {
        return $this->hasMany(StockOrderDetail::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'order_from', 'id');
    }

    public function getImageAttribute($image)
    {
        if ($image != '') {
            return asset('uploads/product').'/thumb/'.$image;
        } else {
            return asset('images/product_image.png');
        }
    }

    public function getPictureAttribute($image)
    {
        if ($image != '') {
            return asset('uploads/product').'/'.$image;
        } else {
            return asset('images/product_image.png');
        }
    }

    public function isFavourite(): bool
    {
        if (!auth('customer')->check())
        {
            return false;
        }
    
        return $this->favourites()
            ->where('customer_id', auth('customer')->id())
            ->exists();
    }

    public function extraProducts(): mixed
    {
        return $this->category
            ?->extraProducts()
            ->where('product_id', '!=', 0)
			->where('product_type', 1)
			->whereNull('deleted_at');
    }
}
