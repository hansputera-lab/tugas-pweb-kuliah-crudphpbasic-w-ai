<?php

namespace App\Domains\Payroll\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeBpjsOverride extends Model
{
    protected $table = 'employee_bpjs_overrides';

    protected $fillable = [
        'employee_id',
        'component',
        'rate_employer',
        'rate_employee',
        'max_wage',
        'min_wage',
        'risk_level',
    ];

    protected $casts = [
        'rate_employer' => 'decimal:2',
        'rate_employee' => 'decimal:2',
        'max_wage' => 'decimal:2',
        'min_wage' => 'decimal:2',
    ];
}
