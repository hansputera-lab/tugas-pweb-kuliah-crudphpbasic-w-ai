<?php

namespace App\Domains\Payroll\Models;

use App\Domains\Employee\Models\Employee;
use App\Domains\Payroll\Models\PayrollPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payslip extends Model
{
    protected $fillable = [
        'payroll_item_id',
        'employee_id',
        'payroll_period_id',
        'payslip_number',
        'generated_at',
        'viewed_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'viewed_at' => 'datetime',
    ];

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

    public function markViewed(): void
    {
        if (!$this->viewed_at) {
            $this->update(['viewed_at' => now()]);
        }
    }
}
