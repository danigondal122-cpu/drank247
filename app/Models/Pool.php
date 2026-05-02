<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pool extends Model
{
    use SoftDeletes;

    protected $table = 'pools';

    public function scopeWhereAttr(
        Builder $query,
        mixed $postcode = null,
        mixed $payment = null
    ): void
    {
        $query->whereNull('deleted_at')
        ->when($postcode, function ($query, $postcode) {
            $query->where('from_postcode', '<=', $postcode)
                ->where('to_postcode', '>=', $postcode);
        })
        ->when($payment, function ($query, $payment) {
            $query->where('delivery_free_from', '>=', $payment);
        });
    }

    public function franchises(): BelongsToMany
    {
        return $this->belongsToMany(Franchise::class)->withPivot('created_at');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * @return BelongsToMany|Builder|SubDeliveryPerson
     */
    public function subDeliveryPeople(): BelongsToMany
    {
        return $this->belongsToMany(SubDeliveryPerson::class)->withPivot('created_at');
    }
}
