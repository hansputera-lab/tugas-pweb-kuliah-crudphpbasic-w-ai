<?php

namespace App\Domains\Position\Models;

use App\Domains\Department\Models\Department;
use App\Domains\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Position extends Model
{
    protected $fillable = [
        'department_id',
        'name',
        'code',
        'base_salary',
        'default_annual_leave_days',
        'default_sick_leave_days',
        'level',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_positions')
            ->withPivot('start_date', 'end_date', 'is_current')
            ->withTimestamps();
    }

    public function getFormattedSalaryAttribute(): string
    {
        return 'Rp ' . number_format($this->base_salary, 0, ',', '.');
    }
}
