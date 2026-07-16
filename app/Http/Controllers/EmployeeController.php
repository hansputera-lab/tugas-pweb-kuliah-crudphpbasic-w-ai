<?php

namespace App\Http\Controllers;

use App\Domains\Department\Services\DepartmentService;
use App\Domains\Employee\DTOs\CreateEmployeeData;
use App\Domains\Employee\DTOs\UpdateEmployeeData;
use App\Domains\Employee\Services\EmployeeService;
use App\Domains\Position\Services\PositionService;
use App\Http\Requests\EmployeeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeService $employeeService,
        protected DepartmentService $departmentService,
        protected PositionService $positionService
    ) {}

    public function index()
    {
        $employees = $this->employeeService->getAll();
        $departments = $this->departmentService->getAll();

        return view('employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $departments = $this->departmentService->getAll();
        $positions = $this->positionService->getAll();
        $managers = $this->employeeService->getManagerSelectOptions();

        return view('employees.create', compact('departments', 'positions', 'managers'));
    }

    public function store(EmployeeRequest $request)
    {
        $user = User::create([
            'name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);

        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('employees', 'public');
            $photo = basename($photo);
        }

        $employee = $this->employeeService->create(new CreateEmployeeData(
            user_id: $user->id,
            nip: $request->nip,
            full_name: $request->full_name,
            gender: $request->gender,
            date_of_birth: $request->date_of_birth,
            phone: $request->phone,
            address: $request->address,
            photo: $photo,
            join_date: $request->join_date,
            status: $request->status ?? 'active',
            department_id: $request->department_id,
        ));

        if ($request->position_id) {
            $this->employeeService->assignPosition($employee, $request->position_id, $request->join_date);
        }

        return redirect()->route('employees.index')
            ->with('success', "Employee {$employee->full_name} created successfully.");
    }

    public function show(int $id)
    {
        $employee = $this->employeeService->getById($id);

        if (!$employee) {
            abort(404);
        }

        $employee->load(['attendances' => function ($q) {
            $q->orderByDesc('date')->limit(7);
        }, 'leaveBalances.leaveType']);

        return view('employees.show', compact('employee'));
    }

    public function edit(int $id)
    {
        $employee = $this->employeeService->getById($id);

        if (!$employee) {
            abort(404);
        }

        $departments = $this->departmentService->getAll();
        $positions = $this->positionService->getAll();

        $managers = $this->employeeService->getManagerSelectOptions();

        return view('employees.edit', compact('employee', 'departments', 'positions', 'managers'));
    }

    public function update(EmployeeRequest $request, int $id)
    {
        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('employees', 'public');
            $photo = basename($photo);
        }

        $this->employeeService->update($id, new UpdateEmployeeData(
            nip: $request->nip,
            full_name: $request->full_name,
            gender: $request->gender,
            date_of_birth: $request->date_of_birth,
            phone: $request->phone,
            address: $request->address,
            photo: $photo,
            join_date: $request->join_date,
            status: $request->status,
            department_id: $request->department_id,
        ));

        if ($request->position_id) {
            $employee = $this->employeeService->getById($id);
            $this->employeeService->assignPosition($employee, $request->position_id, $request->join_date);
        }

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function suspend(int $id)
    {
        $employee = $this->employeeService->suspend($id);
        return redirect()->route('employees.show', $employee)
            ->with('success', "Employee {$employee->full_name} has been suspended.");
    }

    public function unsuspend(int $id)
    {
        $employee = $this->employeeService->unsuspend($id);
        return redirect()->route('employees.show', $employee)
            ->with('success', "Employee {$employee->full_name} has been reactivated.");
    }

    public function destroy(int $id)
    {
        $this->employeeService->delete($id);

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    public function myProfile()
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('info', 'No employee profile linked to your account. Contact HR to set up your profile.');
        }

        return view('employees.show', compact('employee'));
    }
}
