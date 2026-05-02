<?php

namespace App\Models;

use App\Mail\franchiseForgotPasswordMail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Mail;

class Franchise extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'franchises';

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [
        'id',
    ];

    protected function casts(): array
    {
        return [
            'password'      => 'hashed',
        ];
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->reset_token = $token;
        $this->save();
        Mail::to($this->franchises_email)
            ->send(new franchiseForgotPasswordMail($this));
    }

    public function image(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => is_string($value) ? asset("uploads/franchiseprofile/$value") : asset('img/247-Drank-Logo.png')
        )
            ->shouldCache();
    }

    public function pools(): BelongsToMany
    {
        return $this->belongsToMany(Pool::class)->withPivot('created_at');
    }

    public function subDeliveryPeople(): HasMany
    {
        return $this->hasMany(SubDeliveryPerson::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function franchiseStockOrders(): HasMany
    {
        return $this->hasMany(FranchiseStockOrder::class);
    }

    public function stockOrders(): HasMany
    {
        return $this->hasMany(StockOrder::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function invoicePdfs(): HasMany
    {
        return $this->hasMany(InvoicePdf::class);
    }

    public static function poolsArray(): array
    {
        return self::whereNull('deleted_at')
            ->where('fs_on_off', 'online')
            ->with('pools')
            ->get()
            ->pluck('pools.*.id')
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
    }
}
