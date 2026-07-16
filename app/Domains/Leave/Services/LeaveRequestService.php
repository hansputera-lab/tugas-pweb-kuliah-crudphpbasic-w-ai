<?php

namespace App\Domains\Leave\Services;

use App\Domains\Employee\Models\Employee;
use App\Domains\Leave\Models\LeaveRequest;
use App\Domains\Leave\Repositories\LeaveBalanceRepository;
use App\Domains\Leave\Repositories\LeaveRequestRepository;
use App\Models\User;
use Carbon\Carbon;

class LeaveRequestService
{
    public function __construct(
        protected LeaveRequestRepository $leaveRepo,
        protected LeaveBalanceRepository $balanceRepo
    ) {}

    public function requestLeave(Employee $employee, array $data): LeaveRequest
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        $balance = $this->balanceRepo->getBalance(
            employee: $employee,
            leaveTypeId: $data['leave_type_id'],
            year: now()->year
        );

        if (!$balance || $balance->remaining_days < $totalDays) {
            $available = $balance ? $balance->remaining_days : 0;
            throw new \App\Domains\Leave\Exceptions\InsufficientLeaveBalanceException(
                "Insufficient leave balance. Available: {$available} days, Requested: {$totalDays} days."
            );
        }

        return $this->leaveRepo->create($employee, array_merge($data, [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'total_days' => $totalDays,
        ]));
    }

    public function approve(LeaveRequest $request, User $reviewer, ?string $notes): LeaveRequest
    {
        $this->balanceRepo->deductBalance(
            employee: $request->employee,
            leaveTypeId: $request->leave_type_id,
            days: $request->total_days
        );

        return $this->leaveRepo->approve($request, $reviewer, $notes);
    }

    public function reject(LeaveRequest $request, User $reviewer, ?string $notes): LeaveRequest
    {
        return $this->leaveRepo->reject($request, $reviewer, $notes);
    }

    public function getPending()
    {
        return $this->leaveRepo->getPending();
    }

    public function getByEmployee(int $employeeId)
    {
        return $this->leaveRepo->getByEmployee($employeeId);
    }

    public function getById(int $id): ?LeaveRequest
    {
        return $this->leaveRepo->findById($id);
    }

    public function getAll()
    {
        return $this->leaveRepo->getAll();
    }

    public function cancel(LeaveRequest $leaveRequest): void
    {
        $this->leaveRepo->delete($leaveRequest);
    }
}
