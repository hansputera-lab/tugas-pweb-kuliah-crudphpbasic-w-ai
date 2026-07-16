<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    public function __construct(
        protected RoleRepository $repository
    ) {}

    public function getAll(): Collection
    {
        return $this->repository->getAll();
    }

    public function findByName(string $name): ?\App\Domains\Auth\Models\Role
    {
        return $this->repository->findByName($name);
    }

    public function createWithPermissions(array $data, array $permissionIds): \App\Domains\Auth\Models\Role
    {
        $role = $this->repository->create($data);
        if (!empty($permissionIds)) {
            $this->repository->syncPermissions($role, $permissionIds);
        }
        return $role->fresh('permissions');
    }
}
