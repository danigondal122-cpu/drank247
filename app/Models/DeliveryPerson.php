<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DeliveryPerson extends Model
{
    use SoftDeletes;

    protected $table = 'delivery_people';

    protected $guarded = [
        'id',
    ];

    public function dpImage(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => filled($value) ? asset("uploads/deliveryperson/$value") : asset('img/247-Drank-Logo.png')
        )
            ->shouldCache();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function subDeliveryPeople(): HasMany
    {
        return $this->hasMany(SubDeliveryPerson::class);
    }

    public function deliveryHistories(): HasMany
    {
        return $this->hasMany(DeliveryHistory::class);
    }

    public function helps(): HasMany
    {
        return $this->hasMany(Help::class);
    }

    public function scheduleAbsenses(): HasMany
    {
        return $this->hasMany(ScheduleAbsense::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function rateAndReviews(): HasMany
    {
        return $this->hasMany(RateAndReview::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\DeliveryPerson>
     */
    public static function getDeliveryPersonFromPoolIdOrNearestDistance(
        int $pool_id,
        float|string $lat,
        float|string $long,
    ) {
        $deliverypersonid = DeliveryPerson::query()
            ->where('dp_onoff', 'online')
            ->whereHas('subDeliveryPeople', function ($query) use ($pool_id) {
                /** @var \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Builder $query */
                $query->whereHas('pools', function ($query2) use ($pool_id) {
                    /** @var \Illuminate\Database\Eloquent\Relations\BelongsToMany|\Illuminate\Database\Eloquent\Builder $query2 */
                    $query2->where('pools.id', $pool_id);
                });
            })
            ->get(['id', 'dp_name']);

        if (count($deliverypersonid) == 0) {
            /** @var \Illuminate\Database\Eloquent\Collection<int,DeliveryPerson> $deliverypersonid */
            $deliverypersonid = DeliveryPerson::select(
                'id',
                'dp_name',
                DB::raw('SQRT(POW(69.1  * (dp_lat - '.$lat.'), 2) + POW(69.1 * ('.$long.' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance')
            )
                ->where('dp_onoff', 'online')
                ->orderBy('distance', 'ASC')
                ->get();
        }

        return $deliverypersonid;
    }
}
