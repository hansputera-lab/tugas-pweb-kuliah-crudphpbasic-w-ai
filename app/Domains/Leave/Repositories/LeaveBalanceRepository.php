<?php

namespace App\Domains\Leave\Repositories;

use App\Domains\Employee\Models\Employee;
use App\Domains\Leave\Models\LeaveBalance;
use Illuminate\Database\Eloquent\Collection;

class LeaveBalanceRepository
{
    public function __construct(
        protected LeaveBalance $model
    ) {}

    public function getBalance(Employee $employee, int $leaveTypeId, int $year): ?LeaveBalance
    {
        return $this->model->where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->first();
    }

    public function getBalancesForEmployee(Employee $employee, int $year): Collection
    {
        return $this->model->with('leaveType')
            ->where('employee_id', $employee->id)
            ->where('year', $year)
            ->get();
    }

    public function deductBalance(Employee $employee, int $leaveTypeId, int $days): LeaveBalance
    {
        $balance = $this->getBalance($employee, $leaveTypeId, now()->year);

        if ($balance) {
            $balance->increment('used_days', $days);
        }

        return $balance;
    }

    public function restoreBalance(Employee $employee, int $leaveTypeId, int $days): LeaveBalance
    {
        $balance = $this->getBalance($employee, $leaveTypeId, now()->year);

        if ($balance) {
            $balance->decrement('used_days', $days);
        }

        return $balance;
    }
}
