<?php

namespace App\Domains\Reimbursement\Repositories;

use App\Domains\Employee\Models\Employee;
use App\Domains\Reimbursement\Models\ReimbursementApproval;
use App\Domains\Reimbursement\Models\ReimbursementClaim;
use Illuminate\Database\Eloquent\Collection;

class ReimbursementClaimRepository
{
    public function __construct(
        protected ReimbursementClaim $model
    ) {}

    public function findById(int $id): ?ReimbursementClaim
    {
        return $this->model->with(['employee.user', 'employee.department', 'category', 'approvals.approver'])
            ->find($id);
    }

    public function create(Employee $employee, array $data): ReimbursementClaim
    {
        return $this->model->create(array_merge($data, [
            'employee_id' => $employee->id,
            'status' => 'pending',
            'current_approval_level' => 1,
        ]));
    }

    public function getPending(): Collection
    {
        return $this->model->with(['employee.user', 'category'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getByEmployee(int $employeeId): Collection
    {
        return $this->model->with(['category', 'approvals'])
            ->where('employee_id', $employeeId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getAll(): Collection
    {
        return $this->model->with(['employee.user', 'employee.department', 'category', 'approvals.approver'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function approve(ReimbursementClaim $claim, ReimbursementApproval $approval, int $nextLevel): ReimbursementClaim
    {
        $claim->update([
            'current_approval_level' => $nextLevel,
            'status' => $nextLevel > $claim->total_approval_levels ? 'approved' : 'pending',
        ]);

        return $claim->fresh(['employee.user', 'employee.department', 'category', 'approvals.approver']);
    }

    public function reject(ReimbursementClaim $claim, ReimbursementApproval $approval, string $reason): ReimbursementClaim
    {
        $claim->update([
            'status' => 'rejected',
            'rejected_reason' => $reason,
        ]);

        return $claim->fresh(['employee.user', 'employee.department', 'category', 'approvals.approver']);
    }
}
