<?php

namespace App\Domains\Reimbursement\Models;

use App\Domains\Employee\Models\Employee;
use App\Domains\Reimbursement\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReimbursementClaim extends Model
{
    protected $fillable = [
        'employee_id',
        'expense_category_id',
        'title',
        'description',
        'amount',
        'expense_date',
        'receipt_path',
        'status',
        'current_approval_level',
        'total_approval_levels',
        'rejected_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'current_approval_level' => 'integer',
        'total_approval_levels' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ReimbursementApproval::class)->orderBy('level');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getReceiptUrlAttribute(): ?string
    {
        return $this->receipt_path ? asset('uploads/reimbursements/' . $this->receipt_path) : null;
    }
}
