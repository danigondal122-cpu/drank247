<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    protected $guarded = [
        'id',
    ];

    protected function casts(): array
    {
        return [
            'is_popular' => 'boolean',
            'is_show'    => 'boolean',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Category $category) {
            /** @var \Illuminate\Database\Eloquent\Collection<int, Category> */
            $subCategories = Category::query()->where('category_id', $category->id)->get();
            foreach ($subCategories as $subCategory) {
                // TODO: DELETE PRODUCT CART WHEN CATEGORY IS DELETED
                // $pro= Product::where('category_id',$value->category_id)->get();
                // foreach($pro as $key1=>$value1){
                //   Cart::where('cart_itemid',$value1->id)->delete();
                // }
                Product::where('category_id', $subCategory->category_id)->delete();
                if ($category->id === $category->category_id) {
                    logger()->warning("Category id == category_id ({$category->id} === {$category->category_id})");

                    continue;
                }
                $subCategory->deleteQuietly();
            }
            $products = Product::query()->where('category_id', $category->id)->get();
            foreach ($products as $product) {
                //   Cart::where('cart_itemid',$products->id)->delete();
                $product->delete();
            }
        });
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: function (string $image) {
                if ($image != '') {
                    if (file_exists('uploads/category/'.$image)) {
                        return asset('uploads/category').'/'.$image;
                    } else {
                        return asset('img/247-Drank-Logo.png');
                    }
                } else {
                    return asset('img/247-Drank-Logo.png');
                }
            },
        );
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function extraProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('created_at');
    }

    public function subcat(): HasMany
    {
        return $this->categories()
            ->where('is_show', '1')
            ->whereNull('deleted_at')
            ->orderBy('category_order', 'ASC');
    }

    public static function getAllCategories($parent, $indent = 0, $sub_mark = '- ', $isEdit = 0)
    {
        $output = '';

        /** @var \Illuminate\Database\Eloquent\Collection<int, static> $category */
        $category = self::where('is_show', '1')->where('category_id', $parent)->orderBy('category_order', 'ASC')->get();
        $count = $category->count();

        if ($count > 0) {
            foreach ($category as $row) {
                $href = url('products/'.$row->category_id);
                $output .= "<li><a href='$href'>".$row['category_name'].'</a></li>';
            }
        }

        return $output;
    }
}
