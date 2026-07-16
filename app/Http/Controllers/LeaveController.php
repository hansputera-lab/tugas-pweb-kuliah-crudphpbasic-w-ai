<?php

namespace App\Http\Controllers;

use App\Domains\Leave\Models\LeaveType;
use App\Domains\Leave\Services\LeaveBalanceService;
use App\Domains\Leave\Services\LeaveRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function __construct(
        protected LeaveRequestService $leaveRequestService,
        protected LeaveBalanceService $leaveBalanceService
    ) {}

    public function index()
    {
        $leaveRequests = $this->leaveRequestService->getAll();
        $leaveTypes = LeaveType::all();

        return view('leaves.index', compact('leaveRequests', 'leaveTypes'));
    }

    public function create()
    {
        $employee = Auth::user()->employee;
        $leaveTypes = LeaveType::all();
        $leaveBalances = $employee ? $this->leaveBalanceService->getBalancesForEmployee($employee) : collect();

        return view('leaves.create', compact('leaveTypes', 'leaveBalances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $employee = Auth::user()->employee;

        try {
            $this->leaveRequestService->requestLeave($employee, $request->only([
                'leave_type_id',
                'start_date',
                'end_date',
                'reason',
            ]));

            return redirect()->route('my.leaves')
                ->with('success', 'Leave request submitted successfully.');
        } catch (\App\Domains\Leave\Exceptions\InsufficientLeaveBalanceException $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(int $id)
    {
        $leave = $this->leaveRequestService->getById($id);

        if (!$leave) {
            abort(404);
        }

        return view('leaves.show', compact('leave'));
    }

    public function approve(int $id, Request $request)
    {
        $leaveRequest = $this->leaveRequestService->getById($id);

        if (!$leaveRequest || !$leaveRequest->isPending()) {
            return back()->with('error', 'Invalid leave request.');
        }

        $this->leaveRequestService->approve($leaveRequest, Auth::user(), $request->notes);

        return back()->with('success', 'Leave request approved.');
    }

    public function reject(int $id, Request $request)
    {
        $leaveRequest = $this->leaveRequestService->getById($id);

        if (!$leaveRequest || !$leaveRequest->isPending()) {
            return back()->with('error', 'Invalid leave request.');
        }

        $this->leaveRequestService->reject($leaveRequest, Auth::user(), $request->notes);

        return back()->with('success', 'Leave request rejected.');
    }

    public function myLeaves()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            if (Auth::user()->canManageHR()) {
                return redirect()->route('leaves.index');
            }
            abort(404, 'No employee profile found.');
        }

        $leaveRequests = $this->leaveRequestService->getByEmployee($employee->id);

        return view('leaves.my', compact('leaveRequests'));
    }

    public function destroy(int $id)
    {
        $leaveRequest = $this->leaveRequestService->getById($id);

        if (!$leaveRequest || !$leaveRequest->isPending()) {
            return back()->with('error', 'Cannot cancel this leave request.');
        }

        $employee = Auth::user()->employee;
        if (!$employee) {
            return back()->with('error', 'No employee profile found.');
        }
        if ($leaveRequest->employee_id !== $employee->id) {
            abort(403);
        }

        $this->leaveRequestService->cancel($leaveRequest);

        return redirect()->route('my.leaves')
            ->with('success', 'Leave request cancelled.');
    }

    public function myBalance()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            if (Auth::user()->canManageHR()) {
                return redirect()->route('leaves.index');
            }
            abort(404, 'No employee profile found.');
        }

        $balances = $this->leaveBalanceService->getBalancesForEmployee($employee);

        return view('leaves.balance', compact('balances'));
    }
}
