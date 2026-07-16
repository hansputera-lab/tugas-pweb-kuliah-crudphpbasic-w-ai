<?php

namespace App\Domains\Employee\Services;

use App\Domains\Employee\DTOs\CreateEmployeeData;
use App\Domains\Employee\DTOs\UpdateEmployeeData;
use App\Domains\Employee\Models\Employee;
use App\Domains\Employee\Repositories\EmployeeRepository;
use App\Domains\Leave\Models\LeaveBalance;
use App\Domains\Leave\Models\LeaveType;
use App\Domains\Position\Models\Position;

class EmployeeService
{
    public function __construct(
        protected EmployeeRepository $employeeRepo
    ) {}

    public function getAll()
    {
        return $this->employeeRepo->getAll();
    }

    public function getActive()
    {
        return $this->employeeRepo->getActive();
    }

    public function getById(int $id): ?Employee
    {
        return $this->employeeRepo->findById($id);
    }

    public function getByNip(string $nip): ?Employee
    {
        return $this->employeeRepo->findByNip($nip);
    }

    public function getSubordinates(int $managerId)
    {
        return $this->employeeRepo->getSubordinates($managerId);
    }

    public function isManagerOf(int $managerId, int $employeeId): bool
    {
        return $this->employeeRepo->isManagerOf($managerId, $employeeId);
    }

    public function getManagerSelectOptions()
    {
        return $this->employeeRepo->getManagerSelectOptions();
    }

    public function create(CreateEmployeeData $data): Employee
    {
        $employee = $this->employeeRepo->create($data);

        $this->initializeLeaveBalances($employee);

        return $employee;
    }

    public function update(int $id, UpdateEmployeeData $data): Employee
    {
        return $this->employeeRepo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->employeeRepo->delete($id);
    }

    public function suspend(int $id): Employee
    {
        $employee = $this->employeeRepo->updateStatus($id, 'suspended');
        if ($employee->user) {
            $employee->user->update(['is_active' => false]);
        }
        return $employee;
    }

    public function unsuspend(int $id): Employee
    {
        $employee = $this->employeeRepo->updateStatus($id, 'active');
        if ($employee->user) {
            $employee->user->update(['is_active' => true]);
        }
        return $employee;
    }

    public function assignPosition(Employee $employee, int $positionId, string $startDate): void
    {
        $employee->positions()->wherePivot('is_current', true)->updateExistingPivot($employee->id, [
            'is_current' => false,
            'end_date' => now()->toDateString(),
        ]);

        $employee->positions()->attach($positionId, [
            'start_date' => $startDate,
            'is_current' => true,
        ]);
    }

    public function countActive(): int
    {
        return $this->employeeRepo->countActive();
    }

    protected function initializeLeaveBalances(Employee $employee): void
    {
        $leaveTypes = LeaveType::all();

        foreach ($leaveTypes as $leaveType) {
            LeaveBalance::create([
                'employee_id' => $employee->id,
                'leave_type_id' => $leaveType->id,
                'year' => now()->year,
                'total_days' => $leaveType->days_per_year,
                'used_days' => 0,
            ]);
        }
    }
}
