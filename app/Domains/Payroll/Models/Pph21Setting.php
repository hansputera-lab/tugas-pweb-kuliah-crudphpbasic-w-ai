<?php

namespace App\Domains\Payroll\Models;

use Illuminate\Database\Eloquent\Model;

class Pph21Setting extends Model
{
    protected $fillable = [
        'tax_year',
        'ptkp_tk0', 'ptkp_tk1', 'ptkp_tk2', 'ptkp_tk3',
        'ptkp_k0', 'ptkp_k1', 'ptkp_k2', 'ptkp_k3',
        'tarif_layer1', 'tarif_layer2', 'tarif_layer3', 'tarif_layer4', 'tarif_layer5',
        'tarif_batas1', 'tarif_batas2', 'tarif_batas3', 'tarif_batas4',
        'dtp_enabled', 'dtp_max_gaji',
        'biaya_jabatan_persen', 'biaya_jabatan_max_bulan', 'biaya_jabatan_max_tahun',
        'non_npwp_multiplier',
        'is_active', 'effective_date',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'is_active' => 'boolean',
        'dtp_enabled' => 'boolean',
        'tax_year' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getForYear(int $year): ?self
    {
        return static::where('tax_year', $year)->where('is_active', true)->first();
    }

    public function getPtkp(string $ptkpStatus): float
    {
        $key = str_replace('/', '', strtolower($ptkpStatus));
        return (float) ($this->{"ptkp_{$key}"} ?? $this->ptkp_tk0);
    }

    public function getTarifLapis(float $pkp): array
    {
        $layers = [
            ['batas' => (float) $this->tarif_batas1, 'rate' => (float) $this->tarif_layer1],
            ['batas' => (float) $this->tarif_batas2, 'rate' => (float) $this->tarif_layer2],
            ['batas' => (float) $this->tarif_batas3, 'rate' => (float) $this->tarif_layer3],
            ['batas' => (float) $this->tarif_batas4, 'rate' => (float) $this->tarif_layer4],
            ['batas' => PHP_FLOAT_MAX, 'rate' => (float) $this->tarif_layer5],
        ];

        $result = [];
        $remaining = $pkp;
        $previous = 0;

        foreach ($layers as $layer) {
            if ($remaining <= 0) break;
            $limit = $layer['batas'] - $previous;
            $taxable = min($remaining, $limit);
            if ($taxable > 0) {
                $result[] = [
                    'from' => $previous,
                    'to' => $previous + $taxable,
                    'rate' => $layer['rate'],
                    'amount' => round($taxable * $layer['rate'] / 100, 2),
                ];
                $remaining -= $taxable;
            }
            $previous = $layer['batas'];
        }

        return $result;
    }
}
