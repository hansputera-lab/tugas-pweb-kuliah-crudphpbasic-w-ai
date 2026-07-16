<?php

namespace App\Domains\Shift\Models;

use App\Domains\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeShift extends Model
{
    protected $fillable = [
        'employee_id',
        'shift_id',
        'effective_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function isActiveOn(\Carbon\Carbon $date): bool
    {
        $afterStart = !$this->effective_date || $date->gte($this->effective_date);
        $beforeEnd = !$this->end_date || $date->lte($this->end_date);

        return $afterStart && $beforeEnd;
    }
}
