<?php

namespace App\Domains\Performance\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback360 extends Model
{
    protected $table = 'feedback_360';

    protected $fillable = [
        'appraisal_id',
        'reviewer_id',
        'reviewer_name',
        'relationship',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
    ];

    public function appraisal(): BelongsTo
    {
        return $this->belongsTo(Appraisal::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
