<?php

namespace App\Domains\Attendance\Services;

use App\Domains\Attendance\Repositories\AttendanceRepository;
use App\Domains\Employee\Models\Employee;
use App\Domains\Settings\Repositories\SettingRepository;
use Carbon\Carbon;

class AttendanceService
{
    public function __construct(
        protected AttendanceRepository $attendanceRepo,
        protected SettingRepository $settingRepo
    ) {}

    public function checkIn(Employee $employee): \App\Domains\Attendance\Models\Attendance
    {
        $now = Carbon::now();
        $workStartTime = $this->settingRepo->getWorkStartTime();
        $gracePeriod = $this->settingRepo->getGracePeriodMinutes();

        $threshold = Carbon::parse($workStartTime)->addMinutes($gracePeriod);
        $status = $now->gt($threshold) ? 'late' : 'present';

        return $this->attendanceRepo->checkIn($employee, $now, $status);
    }

    public function checkOut(Employee $employee): ?\App\Domains\Attendance\Models\Attendance
    {
        return $this->attendanceRepo->checkOut($employee, Carbon::now());
    }

    public function getTodayForEmployee(Employee $employee): ?\App\Domains\Attendance\Models\Attendance
    {
        return $this->attendanceRepo->getTodayForEmployee($employee);
    }

    public function getMonthForEmployee(Employee $employee, int $year, int $month)
    {
        return $this->attendanceRepo->getMonthForEmployee($employee, $year, $month);
    }

    public function getMonthReport(int $year, int $month)
    {
        return $this->attendanceRepo->getMonthReport($year, $month);
    }

    public function getTodaySummary(): array
    {
        return $this->attendanceRepo->getTodaySummary();
    }

    public function getMonthlyStats(int $year, int $month): array
    {
        return $this->attendanceRepo->getMonthlyStats($year, $month);
    }
}
