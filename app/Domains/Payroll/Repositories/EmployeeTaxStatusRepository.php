<?php

namespace App\Domains\Payroll\Repositories;

use App\Domains\Payroll\Models\EmployeeTaxStatus;
use Illuminate\Database\Eloquent\Collection;

class EmployeeTaxStatusRepository
{
    public function __construct(
        protected EmployeeTaxStatus $model
    ) {}

    public function findByEmployee(int $employeeId): ?EmployeeTaxStatus
    {
        return $this->model->where('employee_id', $employeeId)->first();
    }

    public function createOrUpdate(int $employeeId, array $data): EmployeeTaxStatus
    {
        return $this->model->updateOrCreate(
            ['employee_id' => $employeeId],
            $data
        );
    }

    public function getAll(): Collection
    {
        return $this->model->with('employee.user')->get();
    }
}
