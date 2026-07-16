<?php

namespace App\Http\Controllers;

use App\Domains\Employee\Services\EmployeeService;
use App\Domains\Shift\Services\OvertimeService;
use App\Domains\Shift\Services\ShiftService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function __construct(
        protected ShiftService $shiftService,
        protected OvertimeService $overtimeService,
        protected EmployeeService $employeeService
    ) {}

    // ---------- HR: Shift schedule calendar ----------
    public function index(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $employees = $this->employeeService->getActive();
        if (!$employeeId && $employees->isNotEmpty()) {
            $employeeId = $employees->first()->id;
        }

        $calendar = [];
        $employee = null;
        if ($employeeId) {
            $employee = $this->employeeService->getById($employeeId);
            $calendar = $this->shiftService->getMonthCalendar((int) $employeeId, $year, $month);
        }

        $monthLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        return view('shift.schedule', compact('employees', 'employee', 'calendar', 'year', 'month', 'monthLabel'));
    }

    // ---------- HR: Shift definitions ----------
    public function definitions()
    {
        $shifts = $this->shiftService->getShifts();
        return view('shift.definitions', compact('shifts'));
    }

    public function storeDefinition(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'late_threshold' => 'nullable|date_format:H:i',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7',
        ]);

        $this->shiftService->createShift($data);

        return redirect()->route('shifts.definitions')
            ->with('success', 'Shift created.');
    }

    public function updateDefinition(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'late_threshold' => 'nullable|date_format:H:i',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $this->shiftService->updateShift($id, $data);

        return redirect()->route('shifts.definitions')
            ->with('success', 'Shift updated.');
    }

    public function destroyDefinition(int $id)
    {
        $this->shiftService->deleteShift($id);

        return redirect()->route('shifts.definitions')
            ->with('success', 'Shift deleted.');
    }

    // ---------- HR: Assign shift ----------
    public function assignForm()
    {
        $employees = $this->employeeService->getActive();
        $shifts = $this->shiftService->getActiveShifts();
        return view('shift.assign', compact('employees', 'shifts'));
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'effective_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:effective_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $employee = $this->employeeService->getById($data['employee_id']);
        $this->shiftService->assignShift($employee, $data);

        return redirect()->route('shifts.index', ['employee_id' => $employee->id])
            ->with('success', 'Shift assigned to ' . $employee->full_name);
    }

    // ---------- HR: Overtime management ----------
    public function overtimeIndex(Request $request)
    {
        $status = $request->input('status', 'pending');
        $requests = $status === 'all'
            ? $this->overtimeService->getAll()
            : $this->overtimeService->getPending();

        return view('shift.overtime.index', compact('requests', 'status'));
    }

    public function overtimeShow(int $id)
    {
        $request = $this->overtimeService->getById($id);
        if (!$request) {
            abort(404);
        }
        return view('shift.overtime.show', compact('request'));
    }

    public function overtimeApprove(Request $request, int $id)
    {
        $ot = $this->overtimeService->getById($id);
        if (!$ot) {
            abort(404);
        }
        try {
            $this->overtimeService->approve($ot, Auth::user(), $request->input('notes'));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', 'Overtime request approved.');
    }

    public function overtimeReject(Request $request, int $id)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $ot = $this->overtimeService->getById($id);
        if (!$ot) {
            abort(404);
        }
        try {
            $this->overtimeService->reject($ot, Auth::user(), $request->input('rejection_reason'));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', 'Overtime request rejected.');
    }

    // ---------- Employee: My schedule ----------
    public function mySchedule(Request $request)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'No employee record linked.');
        }

        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $calendar = $this->shiftService->getMonthCalendar($employee->id, $year, $month);
        $monthLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        return view('shift.my.schedule', compact('calendar', 'year', 'month', 'monthLabel', 'employee'));
    }

    // ---------- Employee: My overtime ----------
    public function myOvertime()
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'No employee record linked.');
        }
        $requests = $this->overtimeService->getByEmployee($employee->id);
        return view('shift.my.overtime', compact('requests', 'employee'));
    }

    public function myOvertimeCreate()
    {
        return view('shift.my.overtime-create');
    }

    public function myOvertimeStore(Request $request)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'No employee record linked.');
        }

        $data = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'nullable|string|max:1000',
        ]);

        try {
            $this->overtimeService->submitRequest($employee, $data);
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('my.overtime')
            ->with('success', 'Overtime request submitted.');
    }
}
