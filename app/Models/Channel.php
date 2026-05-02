<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Channel extends Model
{
    const CREATED_AT = null;

    const UPDATED_AT = null;

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
