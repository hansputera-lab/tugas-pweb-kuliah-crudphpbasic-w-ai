<?php

namespace App\Domains\Payroll\Repositories;

use App\Domains\Payroll\Models\PayrollDocument;
use Illuminate\Database\Eloquent\Collection;

class PayrollDocumentRepository
{
    public function __construct(
        protected PayrollDocument $model
    ) {}

    public function findById(int $id): ?PayrollDocument
    {
        return $this->model->with(['payrollItem', 'employee', 'uploadedBy'])->find($id);
    }

    public function getByPayrollItem(int $payrollItemId): Collection
    {
        return $this->model->with('uploadedBy')
            ->where('payroll_item_id', $payrollItemId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function create(array $data): PayrollDocument
    {
        return $this->model->create($data);
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
