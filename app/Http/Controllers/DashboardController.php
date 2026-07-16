<?php

namespace App\Http\Controllers;

use App\Domains\Attendance\Services\AttendanceService;
use App\Domains\Department\Services\DepartmentService;
use App\Domains\Employee\Services\EmployeeService;
use App\Domains\Leave\Services\LeaveBalanceService;
use App\Domains\Leave\Services\LeaveRequestService;
use App\Domains\Payroll\Services\PayrollService;
use App\Domains\Settings\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService,
        protected EmployeeService $employeeService,
        protected LeaveRequestService $leaveRequestService,
        protected LeaveBalanceService $leaveBalanceService,
        protected DepartmentService $departmentService,
        protected SettingService $settingService,
        protected PayrollService $payrollService
    ) {}

    public function index()
    {
        $user = Auth::user();

        $todaySummary = $this->attendanceService->getTodaySummary();

        $stats = [
            'total_employees' => $this->employeeService->countActive(),
            'active_employees' => $this->employeeService->countActive(),
            'present_today' => $todaySummary['present'] ?? 0,
            'pending_leave' => $this->leaveRequestService->getPending()->count(),
        ];

        $latestPayroll = \App\Domains\Payroll\Models\PayrollPeriod::orderByDesc('year')->orderByDesc('month')->first();
        $payrollSummary = null;
        if ($latestPayroll) {
            $payrollSummary = [
                'period' => $latestPayroll->label,
                'status' => $latestPayroll->status,
                'total_net' => $latestPayroll->items()->sum('net_salary'),
                'employees' => $latestPayroll->items()->count(),
            ];
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;
        $monthlyStats = $this->attendanceService->getMonthlyStats($currentYear, $currentMonth);

        $chartData = [
            'attendance_labels' => ['Present', 'Late', 'Absent'],
            'attendance_present' => [$monthlyStats['present'] ?? 0],
            'attendance_late' => [$monthlyStats['late'] ?? 0],
            'attendance_absent' => [$monthlyStats['absent'] ?? 0],
            'leave_labels' => ['Pending', 'Approved', 'Rejected'],
            'leave_data' => [
                $this->leaveRequestService->getPending()->count(),
                $this->leaveRequestService->getAll()->where('status', 'approved')->count(),
                $this->leaveRequestService->getAll()->where('status', 'rejected')->count(),
            ],
        ];

        $employee = $user->employee;
        $leaveBalances = [];
        $todayAttendance = null;

        if ($employee) {
            $leaveBalances = $this->leaveBalanceService->getBalancesForEmployee($employee);
            $todayAttendance = $this->attendanceService->getTodayForEmployee($employee);
        }

        return view('dashboard', compact(
            'stats',
            'todaySummary',
            'chartData',
            'employee',
            'leaveBalances',
            'todayAttendance',
            'payrollSummary'
        ));
    }
}
