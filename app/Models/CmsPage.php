<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsPage extends Model
{
    // use SoftDeletes;

    protected $table = 'cms_pages';

    protected $guarded = [
        'id',
    ];

    public function scopeWherePage(Builder $query, string $value): void
    {
        $query->where('page_name', $value);
    }

    public function localeContent(): string
    {
        return isLocale('nl') ? $this->page_content_dutch : $this->page_content_eng;
    }
}
