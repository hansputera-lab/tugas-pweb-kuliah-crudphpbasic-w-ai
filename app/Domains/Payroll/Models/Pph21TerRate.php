<?php

namespace App\Domains\Payroll\Models;

use Illuminate\Database\Eloquent\Model;

class Pph21TerRate extends Model
{
    protected $table = 'pph21_ter_rates';

    protected $fillable = [
        'tax_year',
        'category',
        'min_income',
        'max_income',
        'rate',
    ];

    protected $casts = [
        'tax_year' => 'integer',
        'min_income' => 'decimal:2',
        'max_income' => 'decimal:2',
        'rate' => 'decimal:2',
    ];

    public function scopeForYear($query, int $year)
    {
        return $query->where('tax_year', $year);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public static function getRate(float $monthlyIncome, string $category, int $year = 2026): ?self
    {
        return static::where('tax_year', $year)
            ->where('category', $category)
            ->where('min_income', '<=', $monthlyIncome)
            ->where(function ($q) use ($monthlyIncome) {
                $q->where('max_income', '>=', $monthlyIncome)
                  ->orWhereNull('max_income');
            })
            ->first();
    }
}
