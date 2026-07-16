<?php

namespace App\Http\Controllers;

use App\Domains\Attendance\Services\AttendanceService;
use App\Domains\Employee\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService,
        protected EmployeeService $employeeService
    ) {}

    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $attendances = $this->attendanceService->getMonthReport($year, $month);
        $employees = $this->employeeService->getActive();

        return view('attendance.index', compact('attendances', 'year', 'month', 'employees'));
    }

    public function checkIn()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return back()->with('error', 'No employee profile found.');
        }

        $todayAttendance = $this->attendanceService->getTodayForEmployee($employee);

        if ($todayAttendance && $todayAttendance->check_in_time) {
            return back()->with('error', 'You have already checked in today.');
        }

        $this->attendanceService->checkIn($employee);

        return back()->with('success', 'Check-in recorded successfully.');
    }

    public function checkOut()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return back()->with('error', 'No employee profile found.');
        }

        $todayAttendance = $this->attendanceService->getTodayForEmployee($employee);

        if (!$todayAttendance || !$todayAttendance->check_in_time) {
            return back()->with('error', 'You have not checked in yet today.');
        }

        if ($todayAttendance->check_out_time) {
            return back()->with('error', 'You have already checked out today.');
        }

        $this->attendanceService->checkOut($employee);

        return back()->with('success', 'Check-out recorded successfully.');
    }

    public function myHistory(Request $request)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            abort(404, 'No employee profile found.');
        }

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $attendances = $this->attendanceService->getMonthForEmployee($employee, $year, $month);

        $totalDays = $attendances->count();
        $presentDays = $attendances->where('status', 'present')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $absentDays = $attendances->where('status', 'absent')->count();

        return view('attendance.history', compact('attendances', 'year', 'month', 'totalDays', 'presentDays', 'lateDays', 'absentDays'));
    }

    public function report(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $departmentId = $request->get('department_id');

        $attendances = $this->attendanceService->getMonthReport($year, $month);
        $employees = $this->employeeService->getActive();
        $departments = \App\Domains\Department\Models\Department::all();

        if ($departmentId) {
            $employees = $employees->where('department_id', $departmentId);
            $attendances = $attendances->filter(fn ($a) => $a->employee && $a->employee->department_id == $departmentId);
        }

        $totalEmployees = $employees->count();
        $totalPresent = $attendances->where('status', 'present')->count();
        $totalLate = $attendances->where('status', 'late')->count();
        $totalAbsent = $attendances->where('status', 'absent')->count();
        $totalRecords = $attendances->count();
        $avgAttendance = $totalRecords > 0 ? round(($totalPresent / $totalRecords) * 100) : 0;

        $reportData = $employees->map(function ($emp) use ($attendances) {
            $empAttendances = $attendances->where('employee_id', $emp->id);
            return [
                'employee_name' => $emp->full_name,
                'department' => $emp->department->name ?? '-',
                'working_days' => $empAttendances->count(),
                'present' => $empAttendances->where('status', 'present')->count(),
                'late' => $empAttendances->where('status', 'late')->count(),
                'absent' => $empAttendances->where('status', 'absent')->count(),
            ];
        })->values();

        return view('attendance.report', compact('attendances', 'employees', 'departments', 'year', 'month', 'reportData', 'totalEmployees', 'avgAttendance', 'totalLate', 'totalAbsent'));
    }
}
