<?php

namespace App\Domains\Shift\Repositories;

use App\Domains\Employee\Models\Employee;
use App\Domains\Shift\Models\OvertimeRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class OvertimeRequestRepository
{
    public function __construct(
        protected OvertimeRequest $model
    ) {}

    public function findById(int $id): ?OvertimeRequest
    {
        return $this->model->with(['employee.user', 'employee.department', 'approver'])->find($id);
    }

    public function create(Employee $employee, array $data): OvertimeRequest
    {
        return $this->model->create(array_merge($data, [
            'employee_id' => $employee->id,
            'status' => 'pending',
        ]));
    }

    public function getPending(): Collection
    {
        return $this->model->with(['employee.user', 'employee.department'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getByEmployee(int $employeeId): Collection
    {
        return $this->model->with(['approver'])
            ->where('employee_id', $employeeId)
            ->orderByDesc('date')
            ->get();
    }

    public function getAll(): Collection
    {
        return $this->model->with(['employee.user', 'employee.department', 'approver'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function approve(OvertimeRequest $request, User $approver, ?string $notes): OvertimeRequest
    {
        $request->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);

        return $request->fresh(['employee.user', 'employee.department', 'approver']);
    }

    public function reject(OvertimeRequest $request, User $approver, string $reason): OvertimeRequest
    {
        $request->update([
            'status' => 'rejected',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return $request->fresh(['employee.user', 'employee.department', 'approver']);
    }
}
