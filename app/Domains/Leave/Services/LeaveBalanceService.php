<?php

namespace App\Domains\Leave\Services;

use App\Domains\Employee\Models\Employee;
use App\Domains\Leave\Repositories\LeaveBalanceRepository;

class LeaveBalanceService
{
    public function __construct(
        protected LeaveBalanceRepository $balanceRepo
    ) {}

    public function getBalancesForEmployee(Employee $employee, int $year = null)
    {
        $year = $year ?? now()->year;
        return $this->balanceRepo->getBalancesForEmployee($employee, $year);
    }

    public function getBalance(Employee $employee, int $leaveTypeId, int $year = null)
    {
        $year = $year ?? now()->year;
        return $this->balanceRepo->getBalance($employee, $leaveTypeId, $year);
    }
}
