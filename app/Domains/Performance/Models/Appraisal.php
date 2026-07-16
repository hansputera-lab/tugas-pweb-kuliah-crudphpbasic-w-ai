<?php

namespace App\Domains\Performance\Models;

use App\Domains\Employee\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appraisal extends Model
{
    protected $fillable = [
        'employee_id',
        'period',
        'reviewer_id',
        'status',
        'total_score',
        'final_grade',
        'reviewed_at',
        'notes',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(AppraisalDetail::class)->orderBy('id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback360::class)->orderBy('id');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
