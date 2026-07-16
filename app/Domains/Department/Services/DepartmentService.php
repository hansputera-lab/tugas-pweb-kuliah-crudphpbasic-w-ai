<?php

namespace App\Domains\Department\Services;

use App\Domains\Department\Models\Department;
use App\Domains\Department\Repositories\DepartmentRepository;

class DepartmentService
{
    public function __construct(
        protected DepartmentRepository $departmentRepo
    ) {}

    public function getAll()
    {
        return $this->departmentRepo->getAll();
    }

    public function getById(int $id): ?Department
    {
        return $this->departmentRepo->findById($id);
    }

    public function create(array $data): Department
    {
        return $this->departmentRepo->create($data);
    }

    public function update(int $id, array $data): Department
    {
        return $this->departmentRepo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->departmentRepo->delete($id);
    }
}
