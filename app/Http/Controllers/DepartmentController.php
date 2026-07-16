<?php

namespace App\Http\Controllers;

use App\Domains\Department\Services\DepartmentService;
use App\Http\Requests\DepartmentRequest;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentService $departmentService
    ) {}

    public function index()
    {
        $departments = $this->departmentService->getAll();

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(DepartmentRequest $request)
    {
        $this->departmentService->create($request->validated());

        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function edit(int $id)
    {
        $department = $this->departmentService->getById($id);

        if (!$department) {
            abort(404);
        }

        return view('departments.edit', compact('department'));
    }

    public function update(DepartmentRequest $request, int $id)
    {
        $this->departmentService->update($id, $request->validated());

        return redirect()->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(int $id)
    {
        $this->departmentService->delete($id);

        return redirect()->route('departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
