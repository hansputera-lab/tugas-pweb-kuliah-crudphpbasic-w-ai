<?php

namespace App\Domains\Attendance\Repositories;

use App\Domains\Attendance\Models\Attendance;
use App\Domains\Employee\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AttendanceRepository
{
    public function __construct(
        protected Attendance $model
    ) {}

    public function checkIn(Employee $employee, Carbon $time, string $status): Attendance
    {
        return $this->model->updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $time->toDateString(),
            ],
            [
                'check_in_time' => $time->format('H:i:s'),
                'status' => $status,
            ]
        );
    }

    public function checkOut(Employee $employee, Carbon $time): ?Attendance
    {
        $attendance = $this->model->where('employee_id', $employee->id)
            ->where('date', $time->toDateString())
            ->first();

        if ($attendance) {
            $attendance->update(['check_out_time' => $time->format('H:i:s')]);
        }

        return $attendance;
    }

    public function getTodayForEmployee(Employee $employee): ?Attendance
    {
        return $this->model->where('employee_id', $employee->id)
            ->where('date', now()->toDateString())
            ->first();
    }

    public function getMonthForEmployee(Employee $employee, int $year, int $month): Collection
    {
        return $this->model->where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();
    }

    public function getMonthReport(int $year, int $month): Collection
    {
        return $this->model->with(['employee.user', 'employee.department'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();
    }

    public function getTodaySummary(): array
    {
        $today = now()->toDateString();

        return [
            'present' => $this->model->where('date', $today)->where('status', 'present')->count(),
            'late' => $this->model->where('date', $today)->where('status', 'late')->count(),
            'absent' => $this->model->where('date', $today)->where('status', 'absent')->count(),
            'total' => $this->model->where('date', $today)->count(),
        ];
    }

    public function getMonthlyStats(int $year, int $month): array
    {
        return $this->model->select('status', \DB::raw('count(*) as total'))
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }
}
