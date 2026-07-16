<?php

namespace App\Domains\Reimbursement\Services;

use App\Domains\Employee\Models\Employee;
use App\Domains\Reimbursement\Models\ReimbursementApproval;
use App\Domains\Reimbursement\Models\ReimbursementClaim;
use App\Domains\Reimbursement\Repositories\ExpenseCategoryRepository;
use App\Domains\Reimbursement\Repositories\ReimbursementClaimRepository;
use App\Models\User;

class ReimbursementService
{
    public function __construct(
        protected ReimbursementClaimRepository $claimRepo,
        protected ExpenseCategoryRepository $categoryRepo
    ) {}

    public function getCategories()
    {
        return $this->categoryRepo->getActive();
    }

    public function getAllCategories()
    {
        return $this->categoryRepo->getAll();
    }

    public function getCategory(int $id): ?\App\Domains\Reimbursement\Models\ExpenseCategory
    {
        return $this->categoryRepo->findById($id);
    }

    public function createCategory(array $data): \App\Domains\Reimbursement\Models\ExpenseCategory
    {
        $data['is_active'] = true;
        return $this->categoryRepo->create($data);
    }

    public function updateCategory(int $id, array $data): \App\Domains\Reimbursement\Models\ExpenseCategory
    {
        $category = $this->categoryRepo->findById($id);
        return $this->categoryRepo->update($category, $data);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->categoryRepo->delete($id);
    }

    public function submitClaim(Employee $employee, array $data): ReimbursementClaim
    {
        $category = $this->categoryRepo->findById($data['expense_category_id']);

        if (!$category) {
            throw new \InvalidArgumentException('Invalid expense category.');
        }

        $data['total_approval_levels'] = $category->approval_levels;

        return $this->claimRepo->create($employee, $data);
    }

    public function approve(ReimbursementClaim $claim, User $approver, ?string $notes): ReimbursementClaim
    {
        if (!$claim->isPending()) {
            throw new \InvalidArgumentException('Only pending claims can be approved.');
        }

        $currentLevel = $claim->current_approval_level;

        $approval = ReimbursementApproval::create([
            'reimbursement_claim_id' => $claim->id,
            'approver_id' => $approver->id,
            'level' => $currentLevel,
            'action' => 'approved',
            'notes' => $notes,
        ]);

        $nextLevel = $currentLevel + 1;

        return $this->claimRepo->approve($claim, $approval, $nextLevel);
    }

    public function reject(ReimbursementClaim $claim, User $approver, ?string $notes, string $reason): ReimbursementClaim
    {
        if (!$claim->isPending()) {
            throw new \InvalidArgumentException('Only pending claims can be rejected.');
        }

        $approval = ReimbursementApproval::create([
            'reimbursement_claim_id' => $claim->id,
            'approver_id' => $approver->id,
            'level' => $claim->current_approval_level,
            'action' => 'rejected',
            'notes' => $notes,
        ]);

        return $this->claimRepo->reject($claim, $approval, $reason);
    }

    public function getPending()
    {
        return $this->claimRepo->getPending();
    }

    public function getAll()
    {
        return $this->claimRepo->getAll();
    }

    public function getById(int $id): ?ReimbursementClaim
    {
        return $this->claimRepo->findById($id);
    }

    public function getByEmployee(int $employeeId)
    {
        return $this->claimRepo->getByEmployee($employeeId);
    }
}
