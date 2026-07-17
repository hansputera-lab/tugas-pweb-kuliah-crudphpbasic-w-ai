<?php

namespace App\Domains\Payroll\Models;

use App\Domains\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTaxStatus extends Model
{
    protected $table = 'employee_tax_status';

    protected $fillable = [
        'employee_id',
        'tax_status',
        'jumlah_tanggungan',
        'npwp',
        'ptkp_status',
        'effective_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'jumlah_tanggungan' => 'integer',
        'effective_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getPtkpLabelAttribute(): string
    {
        $labels = [
            'TK0' => 'Tidak Kawin (0 tanggungan)',
            'TK1' => 'Tidak Kawin (1 tanggungan)',
            'TK2' => 'Tidak Kawin (2 tanggungan)',
            'TK3' => 'Tidak Kawin (3 tanggungan)',
            'K0' => 'Kawin (0 tanggungan)',
            'K1' => 'Kawin (1 tanggungan)',
            'K2' => 'Kawin (2 tanggungan)',
            'K3' => 'Kawin (3 tanggungan)',
        ];
        $key = strtoupper($this->tax_status) . $this->jumlah_tanggungan;
        return $labels[$key] ?? ($this->tax_status . '/' . $this->jumlah_tanggungan);
    }

    public function getPtkpKeyAttribute(): string
    {
        return strtolower($this->tax_status) . $this->jumlah_tanggungan;
    }

    public function getTerCategoryAttribute(): string
    {
        $key = $this->ptkp_key;
        return match (true) {
            in_array($key, ['tk0', 'tk1', 'k0']) => 'A',
            in_array($key, ['tk2', 'tk3', 'k1', 'k2']) => 'B',
            $key === 'k3' => 'C',
            default => 'A',
        };
    }
}
