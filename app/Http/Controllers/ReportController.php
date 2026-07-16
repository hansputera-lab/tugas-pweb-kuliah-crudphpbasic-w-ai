<?php

namespace App\Http\Controllers;

use App\Domains\Attendance\Services\AttendanceService;
use App\Domains\Department\Models\Department;
use App\Domains\Department\Services\DepartmentService;
use App\Domains\Employee\Services\EmployeeService;
use App\Domains\Leave\Models\LeaveType;
use App\Domains\Leave\Services\LeaveRequestService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected EmployeeService $employeeService,
        protected AttendanceService $attendanceService,
        protected LeaveRequestService $leaveRequestService,
        protected DepartmentService $departmentService
    ) {}

    public function employeeList(Request $request)
    {
        $departmentId = $request->get('department_id');
        $status = $request->get('status');
        $employees = $this->employeeService->getAll();
        $departments = Department::all();

        if ($departmentId) {
            $employees = $employees->where('department_id', $departmentId);
        }
        if ($status) {
            $employees = $employees->where('status', $status);
        }

        $totalEmployees = $employees->count();
        $activeCount = $employees->where('status', 'active')->count();
        $suspendedCount = $employees->where('status', 'suspended')->count();
        $inactiveCount = $employees->where('status', 'inactive')->count();

        return view('reports.employee-list', compact('employees', 'departments', 'departmentId', 'totalEmployees', 'activeCount', 'suspendedCount', 'inactiveCount'));
    }

    public function attendanceSummary(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $departmentId = $request->get('department_id');

        $attendances = $this->attendanceService->getMonthReport($year, $month);
        $employees = $this->employeeService->getActive();
        $departments = Department::all();

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

        $perfectAttendance = $employees->filter(function ($emp) use ($attendances) {
            $empAtt = $attendances->where('employee_id', $emp->id);
            return $empAtt->count() > 0 && $empAtt->where('status', 'present')->count() === $empAtt->count();
        })->count();

        $mostLate = $attendances->where('status', 'late')->count();
        $mostAbsent = $attendances->where('status', 'absent')->count();

        $departmentSummary = $departments->map(function ($dept) use ($attendances, $employees) {
            $deptEmployees = $employees->where('department_id', $dept->id);
            $deptAttendances = $attendances->filter(fn ($a) => $a->employee && $a->employee->department_id == $dept->id);
            $total = $deptAttendances->count();
            $present = $deptAttendances->where('status', 'present')->count();
            return [
                'department' => $dept->name,
                'employee_count' => $deptEmployees->count(),
                'attendance_rate' => $total > 0 ? round(($present / $total) * 100) : 0,
                'perfect' => $deptAttendances->where('status', 'present')->count(),
                'late' => $deptAttendances->where('status', 'late')->count(),
                'absent' => $deptAttendances->where('status', 'absent')->count(),
            ];
        })->values();

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $chartLabels = collect();
        $chartPresent = collect();
        $chartLate = collect();
        $chartAbsent = collect();
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::createFromDate($year, $month, $d)->toDateString();
            $chartLabels->push($d);
            $dayAtt = $attendances->where('date', $date);
            $chartPresent->push($dayAtt->where('status', 'present')->count());
            $chartLate->push($dayAtt->where('status', 'late')->count());
            $chartAbsent->push($dayAtt->where('status', 'absent')->count());
        }

        return view('reports.attendance-summary', compact(
            'attendances', 'employees', 'departments', 'year', 'month',
            'totalEmployees', 'avgAttendance', 'perfectAttendance', 'mostLate', 'mostAbsent',
            'departmentSummary', 'chartLabels', 'chartPresent', 'chartLate', 'chartAbsent'
        ));
    }

    public function leaveReport(Request $request)
    {
        $year = $request->get('year', now()->year);
        $leaveRequests = $this->leaveRequestService->getAll()->filter(fn ($r) => $r->created_at->year == $year);
        $leaveTypes = LeaveType::all();
        $departments = Department::all();

        $totalRequests = $leaveRequests->count();
        $approvedCount = $leaveRequests->where('status', 'approved')->count();
        $pendingCount = $leaveRequests->where('status', 'pending')->count();
        $rejectedCount = $leaveRequests->where('status', 'rejected')->count();
        $totalDays = $leaveRequests->sum('total_days');

        $leaveTypeNames = $leaveTypes->pluck('name')->toArray();
        $leaveTypeCounts = $leaveTypes->map(fn ($type) => $leaveRequests->where('leave_type_id', $type->id)->count())->toArray();

        $monthlyLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $monthlyApproved = [];
        $monthlyRejected = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthReqs = $leaveRequests->filter(fn ($r) => $r->created_at->month == $m);
            $monthlyApproved[$m] = $monthReqs->where('status', 'approved')->count();
            $monthlyRejected[$m] = $monthReqs->where('status', 'rejected')->count();
        }

        $departmentLeaveData = $departments->map(function ($dept) use ($leaveRequests) {
            $deptRequests = $leaveRequests->filter(fn ($r) => $r->employee && $r->employee->department_id == $dept->id);
            return [
                'department' => $dept->name,
                'total' => $deptRequests->count(),
                'approved' => $deptRequests->where('status', 'approved')->count(),
                'pending' => $deptRequests->where('status', 'pending')->count(),
                'rejected' => $deptRequests->where('status', 'rejected')->count(),
                'total_days' => $deptRequests->sum('total_days'),
            ];
        })->values();

        return view('reports.leave-report', compact(
            'leaveRequests', 'year', 'leaveTypes', 'departments',
            'totalRequests', 'approvedCount', 'pendingCount', 'rejectedCount', 'totalDays',
            'leaveTypeNames', 'leaveTypeCounts', 'monthlyLabels', 'monthlyApproved', 'monthlyRejected',
            'departmentLeaveData'
        ));
    }
}
