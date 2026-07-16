<?php

namespace App\Domains\Position\Repositories;

use App\Domains\Position\Models\Position;
use Illuminate\Database\Eloquent\Collection;

class PositionRepository
{
    public function __construct(
        protected Position $model
    ) {}

    public function findById(int $id): ?Position
    {
        return $this->model->with('department')->find($id);
    }

    public function getAll(): Collection
    {
        return $this->model->with('department')->orderBy('name')->get();
    }

    public function getByDepartment(int $departmentId): Collection
    {
        return $this->model->where('department_id', $departmentId)->orderBy('name')->get();
    }

    public function create(array $data): Position
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Position
    {
        $position = $this->model->findOrFail($id);
        $position->update($data);
        return $position;
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
