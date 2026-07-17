<?php

namespace App\Domains\Payroll\Repositories;

use App\Domains\Payroll\Models\PayrollRunDetail;
use Illuminate\Database\Eloquent\Collection;

class PayrollRunDetailRepository
{
    public function __construct(
        protected PayrollRunDetail $model
    ) {}

    public function findByPayrollItem(int $payrollItemId): ?PayrollRunDetail
    {
        return $this->model->where('payroll_item_id', $payrollItemId)->first();
    }

    public function findByPeriod(int $periodId): Collection
    {
        return $this->model->with(['employee.user', 'employee.department'])
            ->where('payroll_period_id', $periodId)
            ->orderBy('employee_id')
            ->get();
    }

    public function createOrUpdate(int $payrollItemId, array $data): PayrollRunDetail
    {
        return $this->model->updateOrCreate(
            ['payroll_item_id' => $payrollItemId],
            $data
        );
    }

    public function deleteForPeriod(int $periodId): void
    {
        $this->model->where('payroll_period_id', $periodId)->delete();
    }
}
