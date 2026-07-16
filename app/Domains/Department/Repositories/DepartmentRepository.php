<?php

namespace App\Domains\Department\Repositories;

use App\Domains\Department\Models\Department;
use Illuminate\Database\Eloquent\Collection;

class DepartmentRepository
{
    public function __construct(
        protected Department $model
    ) {}

    public function findById(int $id): ?Department
    {
        return $this->model->with(['positions', 'employees'])->find($id);
    }

    public function getAll(): Collection
    {
        return $this->model->withCount('employees')->orderBy('name')->get();
    }

    public function create(array $data): Department
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Department
    {
        $department = $this->model->findOrFail($id);
        $department->update($data);
        return $department;
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
