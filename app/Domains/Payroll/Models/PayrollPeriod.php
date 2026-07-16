<?php

namespace App\Domains\Payroll\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    protected $fillable = [
        'year',
        'month',
        'status',
        'finalized_at',
        'finalized_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'finalized_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    public function finalizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function getLabelAttribute(): string
    {
        return \Carbon\Carbon::createFromDate($this->year, $this->month, 1)
            ->translatedFormat('F Y');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
