<?php

namespace App\Domains\Employee\Repositories;

use App\Domains\Employee\Models\Employee;
use App\Domains\Employee\DTOs\CreateEmployeeData;
use App\Domains\Employee\DTOs\UpdateEmployeeData;
use Illuminate\Database\Eloquent\Collection;

class EmployeeRepository
{
    public function __construct(
        protected Employee $model
    ) {}

    public function findById(int $id): ?Employee
    {
        return $this->model->with(['user', 'department', 'positions', 'manager'])->find($id);
    }

    public function getSubordinates(int $managerId): Collection
    {
        return $this->model->with(['user', 'department'])
            ->where('manager_id', $managerId)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();
    }

    public function isManagerOf(int $managerId, int $employeeId): bool
    {
        return $this->model
            ->where('id', $employeeId)
            ->where('manager_id', $managerId)
            ->exists();
    }

    public function getManagerSelectOptions(): Collection
    {
        return $this->model->with('user')
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();
    }

    public function findByNip(string $nip): ?Employee
    {
        return $this->model->where('nip', $nip)->first();
    }

    public function findWithPositions(int $id): ?Employee
    {
        return $this->model->with(['positions', 'department', 'user'])->find($id);
    }

    public function getActive(): Collection
    {
        return $this->model->with(['user', 'department', 'manager'])
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();
    }

    public function getAll(): Collection
    {
        return $this->model->with(['user', 'department', 'manager'])
            ->orderBy('full_name')
            ->get();
    }

    public function getActiveByDepartment(int $departmentId): Collection
    {
        return $this->model->with(['user', 'positions'])
            ->where('department_id', $departmentId)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();
    }

    public function create(CreateEmployeeData $data): Employee
    {
        return $this->model->create($data->toArray());
    }

    public function update(int $id, UpdateEmployeeData $data): Employee
    {
        $employee = $this->model->findOrFail($id);
        $employee->update($data->toArray());
        return $employee->fresh(['user', 'department', 'positions']);
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function updateStatus(int $id, string $status): Employee
    {
        $employee = $this->model->findOrFail($id);
        $employee->update(['status' => $status]);
        return $employee->fresh(['user', 'department', 'positions']);
    }

    public function countActive(): int
    {
        return $this->model->where('status', 'active')->count();
    }

    public function countByDepartment(): array
    {
        return $this->model->select('department_id', \DB::raw('count(*) as total'))
            ->where('status', 'active')
            ->groupBy('department_id')
            ->pluck('total', 'department_id')
            ->toArray();
    }
}
