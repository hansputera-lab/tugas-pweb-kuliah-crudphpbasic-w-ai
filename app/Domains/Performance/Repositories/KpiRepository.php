<?php

namespace App\Domains\Performance\Repositories;

use App\Domains\Performance\Models\Kpi;
use Illuminate\Database\Eloquent\Collection;

class KpiRepository
{
    public function __construct(
        protected Kpi $model
    ) {}

    public function findById(int $id): ?Kpi
    {
        return $this->model->find($id);
    }

    public function getActive(): Collection
    {
        return $this->model->active()->get();
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('category')->orderBy('title')->get();
    }

    public function create(array $data): Kpi
    {
        $data['is_active'] = true;
        return $this->model->create($data);
    }

    public function update(Kpi $kpi, array $data): Kpi
    {
        $kpi->update($data);
        return $kpi;
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
