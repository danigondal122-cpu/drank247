<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryHistory extends Model
{
    use SoftDeletes;

    protected $table = 'delivery_histories';

    protected $guarded = [
        'id',
    ];

    protected $appends = ['TotalOrderTime', 'OdoMeter', 'Date', 'TotalOrderTimeINM'];

    protected function casts(): array
    {
        return [
            'history_start_time' => 'datetime',
            'history_end_time'   => 'datetime',
        ];
    }

    public function getTotalOrderTimeAttribute()
    {
        return gmdate('H:i:s', now()->parse($this->history_start_time ?? '0000-00-00 00:00:00')->diffInSeconds($this->history_end_time));
    }

    public function getTotalOrderTimeINMAttribute()
    {
        return now()->parse($this->history_start_time ?? '0000-00-00 00:00:00')->diffInSeconds($this->history_end_time);
    }

    public function getOdoMeterAttribute()
    {
        if ($this->end_odometer != '') {
            return $this->end_odometer - ($this->start_odometer);
        } else {
            return '-';
        }
    }

    public function getDateAttribute()
    {
        return now()->createFromFormat('Y-m-d', $this->history_date)->format('d-m-Y');
    }

    public function deliveryPerson(): BelongsTo
    {
        return $this->belongsTo(DeliveryPerson::class);
    }

    public function deliveryImages(): HasMany
    {
        return $this->hasMany(DeliveryImage::class);
    }
}
