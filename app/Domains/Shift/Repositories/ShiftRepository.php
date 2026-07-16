<?php

namespace App\Domains\Shift\Repositories;

use App\Domains\Employee\Models\Employee;
use App\Domains\Shift\Models\EmployeeShift;
use App\Domains\Shift\Models\Shift;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ShiftRepository
{
    public function __construct(
        protected Shift $model,
        protected EmployeeShift $assignmentModel
    ) {}

    public function findById(int $id): ?Shift
    {
        return $this->model->find($id);
    }

    public function getActive(): Collection
    {
        return $this->model->active()->get();
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    public function create(array $data): Shift
    {
        $data['is_active'] = true;
        return $this->model->create($data);
    }

    public function update(Shift $shift, array $data): Shift
    {
        $shift->update($data);
        return $shift;
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function assign(Employee $employee, array $data): EmployeeShift
    {
        return $this->assignmentModel->create(array_merge($data, [
            'employee_id' => $employee->id,
        ]));
    }

    public function getAssignments(int $employeeId): Collection
    {
        return $this->assignmentModel->with(['shift'])
            ->where('employee_id', $employeeId)
            ->orderByDesc('effective_date')
            ->get();
    }

    public function getAllAssignments(): Collection
    {
        return $this->assignmentModel->with(['employee.user', 'employee.department', 'shift'])
            ->orderByDesc('effective_date')
            ->get();
    }

    public function getShiftOnDate(Employee $employee, Carbon $date): ?Shift
    {
        $assignment = $this->assignmentModel->with(['shift'])
            ->where('employee_id', $employee->id)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $date->toDateString());
            })
            ->where('effective_date', '<=', $date->toDateString())
            ->orderByDesc('effective_date')
            ->first();

        return $assignment?->shift;
    }

    public function getScheduleForRange(int $employeeId, Carbon $start, Carbon $end): Collection
    {
        return $this->assignmentModel->with(['shift'])
            ->where('employee_id', $employeeId)
            ->where('effective_date', '<=', $end->toDateString())
            ->where(function ($q) use ($start) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $start->toDateString());
            })
            ->get();
    }
}
