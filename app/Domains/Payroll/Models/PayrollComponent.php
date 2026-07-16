<?php

namespace App\Domains\Payroll\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PayrollComponent extends Model
{
    protected $fillable = [
        'name',
        'type',
        'calculation',
        'value',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function isAllowance(): bool
    {
        return $this->type === 'allowance';
    }

    public function isDeduction(): bool
    {
        return $this->type === 'deduction';
    }

    public function isFixed(): bool
    {
        return $this->calculation === 'fixed';
    }

    public function isPercentage(): bool
    {
        return $this->calculation === 'percentage';
    }
}
