<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';

    protected $guarded = [
        'id',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }
}
