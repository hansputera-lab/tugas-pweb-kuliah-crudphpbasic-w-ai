<?php

namespace App\Domains\Position\Services;

use App\Domains\Position\Models\Position;
use App\Domains\Position\Repositories\PositionRepository;

class PositionService
{
    public function __construct(
        protected PositionRepository $positionRepo
    ) {}

    public function getAll()
    {
        return $this->positionRepo->getAll();
    }

    public function getById(int $id): ?Position
    {
        return $this->positionRepo->findById($id);
    }

    public function getByDepartment(int $departmentId)
    {
        return $this->positionRepo->getByDepartment($departmentId);
    }

    public function create(array $data): Position
    {
        return $this->positionRepo->create($data);
    }

    public function update(int $id, array $data): Position
    {
        return $this->positionRepo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->positionRepo->delete($id);
    }
}
