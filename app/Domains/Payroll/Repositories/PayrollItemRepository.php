<?php

namespace App\Domains\Payroll\Repositories;

use App\Domains\Employee\Models\Employee;
use App\Domains\Payroll\Models\PayrollItem;
use App\Domains\Payroll\Models\PayrollPeriod;
use Illuminate\Database\Eloquent\Collection;

class PayrollItemRepository
{
    public function __construct(
        protected PayrollItem $model
    ) {}

    public function findById(int $id): ?PayrollItem
    {
        return $this->model->with(['employee.user', 'employee.department', 'employee.positions', 'period'])
            ->find($id);
    }

    public function findByPeriod(int $periodId): Collection
    {
        return $this->model->with(['employee.user', 'employee.department'])
            ->where('payroll_period_id', $periodId)
            ->orderBy('employee_id')
            ->get();
    }

    public function findByEmployee(int $employeeId): Collection
    {
        return $this->model->with(['period', 'payslip'])
            ->where('employee_id', $employeeId)
            ->where('status', '!=', 'draft')
            ->orderByDesc('payroll_period_id')
            ->get();
    }

    public function findForEmployeePeriod(int $periodId, int $employeeId): ?PayrollItem
    {
        return $this->model->with(['period', 'payslip'])
            ->where('payroll_period_id', $periodId)
            ->where('employee_id', $employeeId)
            ->first();
    }

    public function upsert(PayrollPeriod $period, Employee $employee, array $data): PayrollItem
    {
        return $this->model->updateOrCreate(
            [
                'payroll_period_id' => $period->id,
                'employee_id' => $employee->id,
            ],
            $data
        );
    }

    public function update(PayrollItem $item, array $data): PayrollItem
    {
        $item->update($data);
        return $item->fresh(['employee.user', 'employee.department', 'period']);
    }

    public function deleteForPeriod(PayrollPeriod $period): void
    {
        $this->model->where('payroll_period_id', $period->id)->delete();
    }
}
