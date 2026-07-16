<?php

namespace App\Domains\Shift\Services;

use App\Domains\Employee\Models\Employee;
use App\Domains\Shift\Models\Shift;
use App\Domains\Shift\Repositories\ShiftRepository;
use Carbon\Carbon;

class ShiftService
{
    public function __construct(
        protected ShiftRepository $shiftRepo
    ) {}

    public function getShifts()
    {
        return $this->shiftRepo->getAll();
    }

    public function getActiveShifts()
    {
        return $this->shiftRepo->getActive();
    }

    public function getShift(int $id): ?Shift
    {
        return $this->shiftRepo->findById($id);
    }

    public function createShift(array $data): Shift
    {
        if (empty($data['late_threshold'])) {
            $data['late_threshold'] = $data['start_time'];
        }
        return $this->shiftRepo->create($data);
    }

    public function updateShift(int $id, array $data): Shift
    {
        $shift = $this->shiftRepo->findById($id);
        return $this->shiftRepo->update($shift, $data);
    }

    public function deleteShift(int $id): bool
    {
        return $this->shiftRepo->delete($id);
    }

    public function assignShift(Employee $employee, array $data): \App\Domains\Shift\Models\EmployeeShift
    {
        return $this->shiftRepo->assign($employee, $data);
    }

    public function getAssignments(int $employeeId)
    {
        return $this->shiftRepo->getAssignments($employeeId);
    }

    public function getAllAssignments()
    {
        return $this->shiftRepo->getAllAssignments();
    }

    public function getMonthCalendar(int $employeeId, int $year, int $month): array
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $assignments = $this->shiftRepo->getScheduleForRange($employeeId, $start, $end);

        $calendar = [];
        $day = $start->copy();
        while ($day->lte($end)) {
            $shift = $this->resolveShiftForDate($assignments, $day);
            $calendar[$day->toDateString()] = $shift;
            $day->addDay();
        }

        return $calendar;
    }

    private function resolveShiftForDate(\Illuminate\Database\Eloquent\Collection $assignments, Carbon $date): ?Shift
    {
        foreach ($assignments as $assignment) {
            if ($assignment->isActiveOn($date)) {
                return $assignment->shift;
            }
        }
        return null;
    }
}
