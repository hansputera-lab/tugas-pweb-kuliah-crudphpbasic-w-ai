<?php

namespace App\Domains\Leave\Models;

use App\Domains\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'year',
        'total_days',
        'used_days',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function getRemainingDaysAttribute(): int
    {
        return $this->total_days - $this->used_days;
    }

    public function getUsagePercentageAttribute(): float
    {
        if ($this->total_days === 0) {
            return 0;
        }
        return round(($this->used_days / $this->total_days) * 100, 1);
    }
}
