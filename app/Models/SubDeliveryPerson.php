<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubDeliveryPerson extends Model
{
    use SoftDeletes;

    protected $table = 'sub_delivery_people';

    protected $guarded = ['id'];

    public function deliveryPerson(): BelongsTo
    {
        return $this->belongsTo(DeliveryPerson::class);
    }

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    /**
     * @return BelongsToMany|Builder
     */
    public function pools(): BelongsToMany
    {
        return $this->belongsToMany(Pool::class)->withPivot('created_at');
    }
}
