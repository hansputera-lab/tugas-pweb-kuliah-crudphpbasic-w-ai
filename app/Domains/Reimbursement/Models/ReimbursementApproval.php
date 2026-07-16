<?php

namespace App\Domains\Reimbursement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReimbursementApproval extends Model
{
    protected $fillable = [
        'reimbursement_claim_id',
        'approver_id',
        'level',
        'action',
        'notes',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(ReimbursementClaim::class, 'reimbursement_claim_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function isApproved(): bool
    {
        return $this->action === 'approved';
    }
}
