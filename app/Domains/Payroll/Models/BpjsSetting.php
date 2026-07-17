<?php

namespace App\Domains\Payroll\Models;

use Illuminate\Database\Eloquent\Model;

class BpjsSetting extends Model
{
    protected $fillable = [
        'component',
        'rate_employer',
        'rate_employee',
        'max_wage',
        'min_wage',
        'risk_level',
        'risk_rate',
        'effective_date',
        'is_active',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'is_active' => 'boolean',
        'rate_employer' => 'decimal:2',
        'rate_employee' => 'decimal:2',
        'max_wage' => 'decimal:2',
        'min_wage' => 'decimal:2',
        'risk_rate' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeComponent($query, string $component)
    {
        return $query->where('component', $component);
    }

    public static function getEffective(string $component, ?string $date = null): ?self
    {
        $date = $date ?? now()->format('Y-m-d');
        return static::where('component', $component)
            ->where('is_active', true)
            ->where('effective_date', '<=', $date)
            ->orderByDesc('effective_date')
            ->first();
    }
}
