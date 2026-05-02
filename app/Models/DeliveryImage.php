<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryImage extends Model
{
    protected $table = 'delivery_images';

    protected $guarded = [
        'id',
    ];

    public function deliveryHistory(): BelongsTo
    {
        return $this->belongsTo(DeliveryHistory::class);
    }
}
