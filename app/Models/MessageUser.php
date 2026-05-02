<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageUser extends Model
{
    use SoftDeletes;

    protected $table = 'message_users';

    protected $guarded = [
        'id',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
