<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use SoftDeletes;

    protected $table = 'modules';

    protected $guarded = [
        'id',
    ];

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class)->withPivot('created_at');
    }
}
