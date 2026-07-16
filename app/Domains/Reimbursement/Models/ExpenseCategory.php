<?php

namespace App\Domains\Reimbursement\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'requires_receipt',
        'approval_levels',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'requires_receipt' => 'boolean',
        'is_active' => 'boolean',
        'approval_levels' => 'integer',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getRequiresReceiptLabelAttribute(): string
    {
        return $this->requires_receipt ? 'Required' : 'Optional';
    }
}
