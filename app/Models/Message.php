<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $table = 'messages';

    protected $guarded = [
        'id',
    ];

    public function messageUsers(): HasMany
    {
        return $this->hasMany(MessageUser::class);
    }
}
