<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'orders';

    protected $guarded = [
        'id',
    ];

    protected $appends = [
        'total_order_time',
        'TotalOrderTimeINM',
        'order_payment_status_text',
        'order_date',
        // 'created_date',
        'new_order_date',
        'delivery_date',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'order_approve'         => 'boolean',
            'order_payment_status'  => 'boolean',
            'od_assignedtime'       => 'datetime',
            'od_start_time'         => 'datetime',
            'od_end_time'           => 'datetime',
            'order_delivery_time'   => 'datetime',
        ];
    }

    public function orderPaymentStatusText(): Attribute
    {
        return Attribute::get(fn () => $this->order_payment_status ? 'YES' : 'NO');
    }

    public function totalOrderTime(): Attribute
    {
        return Attribute::get(fn () => gmdate('H:i:s', ($this->od_start_time)?->diffInSeconds($this->od_end_time) ?? 0))
            ->shouldCache();

        // Format yang benar mungkin yang ini tapi masih gunakan kodingan yang di atas.
        $diffInSeconds = $this->od_start_time?->diffInSeconds($this->od_end_time);

        // Calculate the hours, minutes, and seconds
        $hours = floor($diffInSeconds / 3600); // 1 hour = 3600 seconds
        $minutes = floor(($diffInSeconds % 3600) / 60); // 1 minute = 60 seconds
        $seconds = $diffInSeconds % 60;

        // Format the result as hours:minutes:seconds
        $formattedTime = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }

    protected function orderDate(): Attribute
    {
        return Attribute::get(fn () => now()->parse($this->created_at)
            ->setTimezone(session()->get('time_zone', 'UTC'))
            ->format('d-m-Y')
        )
            ->shouldCache();
    }

    protected function newOrderDate(): Attribute
    {
        return Attribute::get(fn () => now()->parse($this->created_at)
            ->setTimezone(session()->get('time_zone', 'UTC'))
            ->format('d-m-Y H:i:s')
        )
            ->shouldCache();
    }

    protected function deliveryDate(): Attribute
    {
        return Attribute::get(
            fn () => $this->order_delivery_time
                ? $this->order_delivery_time
                    ->setTimezone(session()->get('time_zone', 'UTC'))
                    ->format('d-m-Y H:i:s')
                : '-'
        )
            ->shouldCache();
    }

    protected function startDate(): Attribute
    {
        return Attribute::get(
            fn () => $this->od_start_time
                ? $this->od_start_time
                    ->setTimezone(session()->get('time_zone', 'UTC'))
                    ->format('d-m-Y H:i:s')
                : '-'
        )
            ->shouldCache();
    }

    protected function endDate(): Attribute
    {
        return Attribute::get(
            fn () => $this->od_end_time
                ? $this->od_end_time
                    ->setTimezone(session()->get('time_zone', 'UTC'))
                    ->format('d-m-Y H:i:s')
                : '-'
        )
            ->shouldCache();
    }

    public function getTotalOrderTimeINMAttribute()
    {
        // TODO: Ganti jadi attribute laravel 11 total_order_time_in_seconds
        return now()->parse($this->od_start_time)->diffInSeconds($this->od_end_time);
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function orderPayments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function orderStatus(): HasOne
    {
        return $this->HasOne(OrderStatus::class, 'id', 'order_status');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function uberStore(): BelongsTo
    {
        return $this->belongsTo(UberStore::class, 'order_store_id', 'store_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'order_address_id');
    }

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    public function deliveryPerson(): BelongsTo
    {
        return $this->belongsTo(DeliveryPerson::class);
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function helps(): HasMany
    {
        return $this->hasMany(Help::class);
    }

    public function rateAndReviews(): HasOne
    {
        return $this->hasOne(RateAndReview::class);
    }
}
