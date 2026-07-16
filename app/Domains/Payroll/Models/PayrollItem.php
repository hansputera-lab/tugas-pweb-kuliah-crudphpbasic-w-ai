<?php

namespace App\Domains\Payroll\Models;

use App\Domains\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PayrollItem extends Model
{
    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'base_salary',
        'allowance_transport',
        'allowance_meal',
        'allowance_other',
        'total_allowance',
        'deduction_late',
        'deduction_absent',
        'deduction_other',
        'total_deduction',
        'overtime_hours',
        'overtime_pay',
        'net_salary',
        'status',
        'notes',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'allowance_transport' => 'decimal:2',
        'allowance_meal' => 'decimal:2',
        'allowance_other' => 'decimal:2',
        'total_allowance' => 'decimal:2',
        'deduction_late' => 'decimal:2',
        'deduction_absent' => 'decimal:2',
        'deduction_other' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payslip(): HasOne
    {
        return $this->hasOne(Payslip::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PayrollDocument::class);
    }

    public function recompute(): void
    {
        $totalAllowance = (float) $this->allowance_transport
            + (float) $this->allowance_meal
            + (float) $this->allowance_other;

        $totalDeduction = (float) $this->deduction_late
            + (float) $this->deduction_absent
            + (float) $this->deduction_other;

        $netSalary = (float) $this->base_salary
            + $totalAllowance
            + (float) $this->overtime_pay
            - $totalDeduction;

        $this->total_allowance = number_format($totalAllowance, 2, '.', '');
        $this->total_deduction = number_format($totalDeduction, 2, '.', '');
        $this->net_salary = number_format($netSalary, 2, '.', '');
    }
}
