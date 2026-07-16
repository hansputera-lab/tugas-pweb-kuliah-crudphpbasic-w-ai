<?php

namespace App\Domains\Auth\Repositories;

use App\Domains\Auth\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository
{
    public function __construct(
        protected Permission $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->orderBy('module')->orderBy('name')->get();
    }

    public function findByName(string $name): ?Permission
    {
        return $this->model->where('name', $name)->first();
    }

    public function findByModule(string $module): Collection
    {
        return $this->model->where('module', $module)->get();
    }

    public function getGroupedByModule(): Collection
    {
        return $this->model->orderBy('module')->get()->groupBy('module');
    }

    public function create(array $data): Permission
    {
        return $this->model->create($data);
    }

    public function upsertByName(array $data): Permission
    {
        return $this->model->updateOrCreate(['name' => $data['name']], $data);
    }
}
