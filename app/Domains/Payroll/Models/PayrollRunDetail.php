<?php

namespace App\Domains\Payroll\Models;

use App\Domains\Employee\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollRunDetail extends Model
{
    protected $table = 'payroll_run_details';

    protected $fillable = [
        'payroll_item_id',
        'employee_id',
        'payroll_period_id',
        'gross_income',
        'bpjs_kes_employee', 'bpjs_kes_employer',
        'bpjs_jht_employee', 'bpjs_jht_employer',
        'bpjs_jp_employee', 'bpjs_jp_employer',
        'bpjs_jkk_employer', 'bpjs_jkm_employer',
        'total_bpjs_employee', 'total_bpjs_employer',
        'net_income_before_tax',
        'pph21_monthly', 'pph21_ter_rate', 'pph21_method', 'pph21_dtp_amount',
        'take_home_pay',
        'calculated_by', 'calculated_at',
        'calculation_detail',
    ];

    protected $casts = [
        'calculated_at' => 'datetime',
        'calculation_detail' => 'array',
    ];

    public function getGajiPokokAttribute(): float
    {
        return (float) $this->gross_income;
    }

    public function getTotalAllowancesAttribute(): float
    {
        return 0;
    }

    public function getTotalDeductionsAttribute(): float
    {
        return (float) ($this->bpjs_kes_employee + $this->bpjs_jht_employee + $this->bpjs_jp_employee);
    }

    public function getEmployerBpjsAttribute(): float
    {
        return (float) $this->total_bpjs_employer;
    }

    public function getEmployeeBpjsAttribute(): float
    {
        return (float) $this->total_bpjs_employee;
    }

    public function getPph21AmountAttribute(): float
    {
        return (float) $this->pph21_monthly;
    }

    public function getNetSalaryAttribute(): float
    {
        return (float) $this->take_home_pay;
    }

    public function getBpjsDetailsAttribute(): ?array
    {
        return [
            'kes_employer' => (float) $this->bpjs_kes_employer,
            'kes_employee' => (float) $this->bpjs_kes_employee,
            'jkk_employer' => (float) $this->bpjs_jkk_employer,
            'jkm_employer' => (float) $this->bpjs_jkm_employer,
            'jht_employer' => (float) $this->bpjs_jht_employer,
            'jht_employee' => (float) $this->bpjs_jht_employee,
            'jp_employer' => (float) $this->bpjs_jp_employer,
            'jp_employee' => (float) $this->bpjs_jp_employee,
            'total_employee' => (float) $this->total_bpjs_employee,
            'total_employer' => (float) $this->total_bpjs_employer,
        ];
    }

    public function getPph21DetailsAttribute(): ?array
    {
        return [
            'pph21_per_bulan' => (float) $this->pph21_monthly,
            'ter_rate_pct' => (float) ($this->pph21_ter_rate ?? 0),
            'method' => $this->pph21_method ?? 'ter',
            'dtp_amount' => (float) ($this->pph21_dtp_amount ?? 0),
        ];
    }

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function calculator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }
}
