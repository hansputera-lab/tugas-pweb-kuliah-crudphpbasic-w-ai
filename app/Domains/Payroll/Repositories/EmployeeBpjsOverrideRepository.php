<?php

namespace App\Domains\Payroll\Repositories;

use App\Domains\Payroll\Models\EmployeeBpjsOverride;
use Illuminate\Database\Eloquent\Collection;

class EmployeeBpjsOverrideRepository
{
    public function __construct(
        protected EmployeeBpjsOverride $model
    ) {}

    public function findByEmployee(int $employeeId): Collection
    {
        return $this->model->where('employee_id', $employeeId)->get()->keyBy('component');
    }

    public function upsert(int $employeeId, string $component, array $data): EmployeeBpjsOverride
    {
        return $this->model->updateOrCreate(
            ['employee_id' => $employeeId, 'component' => $component],
            $data
        );
    }

    public function deleteForEmployee(int $employeeId): void
    {
        $this->model->where('employee_id', $employeeId)->delete();
    }
}
