<?php

namespace App\Domains\Shift\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'late_threshold',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
        'late_threshold' => 'string',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('name');
    }

    public function getLabelAttribute(): string
    {
        return $this->name . ' (' . substr($this->start_time, 0, 5) . '-' . substr($this->end_time, 0, 5) . ')';
    }
}
