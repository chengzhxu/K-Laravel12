<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'path',
        'component',
        'permission',
        'icon',
        'redirect',
        'type',
        'hidden',
        'status',
        'sort',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->orderBy('sort');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }
}
