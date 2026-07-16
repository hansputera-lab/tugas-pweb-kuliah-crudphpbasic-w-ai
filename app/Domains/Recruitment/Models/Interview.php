<?php

namespace App\Domains\Recruitment\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    protected $fillable = [
        'job_application_id',
        'interviewer_id',
        'scheduled_at',
        'duration_minutes',
        'location',
        'meeting_link',
        'status',
        'feedback',
        'rating',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'rating' => 'decimal:2',
        ];
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}
