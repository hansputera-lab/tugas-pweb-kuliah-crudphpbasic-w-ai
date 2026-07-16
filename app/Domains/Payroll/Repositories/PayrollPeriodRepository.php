<?php

namespace App\Domains\Payroll\Repositories;

use App\Domains\Payroll\Models\PayrollPeriod;
use Illuminate\Database\Eloquent\Collection;

class PayrollPeriodRepository
{
    public function __construct(
        protected PayrollPeriod $model
    ) {}

    public function findById(int $id): ?PayrollPeriod
    {
        return $this->model->with(['items.employee.user', 'items.employee.department', 'finalizer'])
            ->find($id);
    }

    public function findOrFail(int $id): PayrollPeriod
    {
        return $this->model->with(['items.employee.user', 'items.employee.department', 'finalizer'])
            ->findOrFail($id);
    }

    public function findByMonth(int $year, int $month): ?PayrollPeriod
    {
        return $this->model->where('year', $year)->where('month', $month)->first();
    }

    public function create(int $year, int $month): PayrollPeriod
    {
        return $this->model->create([
            'year' => $year,
            'month' => $month,
            'status' => 'draft',
        ]);
    }

    public function getAll(): Collection
    {
        return $this->model->orderByDesc('year')->orderByDesc('month')->get();
    }

    public function updateStatus(PayrollPeriod $period, string $status, ?int $finalizedBy = null): PayrollPeriod
    {
        $period->update([
            'status' => $status,
            'finalized_at' => $status === 'finalized' ? now() : $period->finalized_at,
            'finalized_by' => $finalizedBy,
        ]);

        return $period->fresh(['items.employee.user', 'items.employee.department', 'finalizer']);
    }
}
