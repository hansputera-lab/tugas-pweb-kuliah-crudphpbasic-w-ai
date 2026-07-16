<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Repositories\PermissionRepository;
use Illuminate\Database\Eloquent\Collection;

class PermissionService
{
    public function __construct(
        protected PermissionRepository $repository
    ) {}

    public function getAll(): Collection
    {
        return $this->repository->getAll();
    }

    public function getGroupedByModule(): Collection
    {
        return $this->repository->getGroupedByModule();
    }

    public function findByModule(string $module): Collection
    {
        return $this->repository->findByModule($module);
    }

    public function bulkCreate(array $permissions): void
    {
        foreach ($permissions as $perm) {
            $this->repository->upsertByName([
                'name' => $perm['name'],
                'label' => $perm['label'],
                'module' => $perm['module'],
            ]);
        }
    }
}
