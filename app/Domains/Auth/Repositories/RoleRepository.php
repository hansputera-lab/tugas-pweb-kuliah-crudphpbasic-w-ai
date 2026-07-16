<?php

namespace App\Domains\Auth\Repositories;

use App\Domains\Auth\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository
{
    public function __construct(
        protected Role $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->with('permissions')->orderBy('name')->get();
    }

    public function findByName(string $name): ?Role
    {
        return $this->model->with('permissions')->where('name', $name)->first();
    }

    public function findById(int $id): ?Role
    {
        return $this->model->with('permissions')->find($id);
    }

    public function create(array $data): Role
    {
        return $this->model->create($data);
    }

    public function syncPermissions(Role $role, array $permissionIds): void
    {
        $role->permissions()->sync($permissionIds);
    }
}
