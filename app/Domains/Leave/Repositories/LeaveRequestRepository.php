<?php

namespace App\Domains\Leave\Repositories;

use App\Domains\Employee\Models\Employee;
use App\Domains\Leave\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class LeaveRequestRepository
{
    public function __construct(
        protected LeaveRequest $model
    ) {}

    public function findById(int $id): ?LeaveRequest
    {
        return $this->model->with(['employee.user', 'leaveType', 'reviewer'])->find($id);
    }

    public function create(Employee $employee, array $data): LeaveRequest
    {
        return $this->model->create(array_merge($data, [
            'employee_id' => $employee->id,
            'status' => 'pending',
        ]));
    }

    public function getPending(): Collection
    {
        return $this->model->with(['employee.user', 'leaveType'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getByEmployee(int $employeeId): Collection
    {
        return $this->model->with('leaveType')
            ->where('employee_id', $employeeId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function approve(LeaveRequest $request, User $reviewer, ?string $notes): LeaveRequest
    {
        $request->update([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'review_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        return $request->fresh(['employee.user', 'leaveType', 'reviewer']);
    }

    public function reject(LeaveRequest $request, User $reviewer, ?string $notes): LeaveRequest
    {
        $request->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'review_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        return $request->fresh(['employee.user', 'leaveType', 'reviewer']);
    }

    public function getAll(): Collection
    {
        return $this->model->with(['employee.user', 'leaveType', 'reviewer'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function delete(LeaveRequest $leaveRequest): void
    {
        $leaveRequest->delete();
    }
}
