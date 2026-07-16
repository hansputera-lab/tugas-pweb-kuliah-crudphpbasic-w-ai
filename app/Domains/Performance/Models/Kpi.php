<?php

namespace App\Domains\Performance\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'target_value',
        'weight',
        'measurement_unit',
        'is_active',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'weight' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('category')->orderBy('title');
    }
}
