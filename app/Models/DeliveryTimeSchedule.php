<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryTimeSchedule extends Model
{
    protected $table = 'delivery_time_schedules';

    protected $guarded = [
        'id',
    ];

    protected function casts(): array
    {
        return [
            'is_checked' => 'boolean',
        ];
    }

    public function time(): string
    {
        return $this->start_time_0 . ':' . $this->start_time_1 . '-' . $this->end_time_0 . ':' . $this->end_time_1;
    }
}
