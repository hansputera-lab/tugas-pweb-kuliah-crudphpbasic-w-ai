<?php

namespace App\Domains\Performance\Models;

use App\Domains\Performance\Models\Kpi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppraisalDetail extends Model
{
    protected $fillable = [
        'appraisal_id',
        'kpi_id',
        'score',
        'weight',
        'achievement',
        'comment',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'weight' => 'integer',
    ];

    public function appraisal(): BelongsTo
    {
        return $this->belongsTo(Appraisal::class);
    }

    public function kpi(): BelongsTo
    {
        return $this->belongsTo(Kpi::class);
    }

    public function getWeightedScoreAttribute(): float
    {
        $weight = (float) $this->weight;
        if ($weight <= 0) {
            return 0;
        }
        return round(((float) $this->score * $weight) / 100, 2);
    }
}
