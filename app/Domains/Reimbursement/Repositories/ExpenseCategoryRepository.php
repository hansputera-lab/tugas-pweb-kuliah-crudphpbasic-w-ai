<?php

namespace App\Domains\Reimbursement\Repositories;

use App\Domains\Reimbursement\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Collection;

class ExpenseCategoryRepository
{
    public function __construct(
        protected ExpenseCategory $model
    ) {}

    public function findById(int $id): ?ExpenseCategory
    {
        return $this->model->find($id);
    }

    public function getActive(): Collection
    {
        return $this->model->active()->get();
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('sort_order')->get();
    }

    public function create(array $data): ExpenseCategory
    {
        return $this->model->create($data);
    }

    public function update(ExpenseCategory $category, array $data): ExpenseCategory
    {
        $category->update($data);
        return $category;
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
