<?php

namespace App\Http\Controllers;

use App\Domains\Department\Services\DepartmentService;
use App\Domains\Position\Services\PositionService;
use App\Http\Requests\PositionRequest;

class PositionController extends Controller
{
    public function __construct(
        protected PositionService $positionService,
        protected DepartmentService $departmentService
    ) {}

    public function index()
    {
        $positions = $this->positionService->getAll();
        $departments = $this->departmentService->getAll();

        return view('positions.index', compact('positions', 'departments'));
    }

    public function create()
    {
        $departments = $this->departmentService->getAll();

        return view('positions.create', compact('departments'));
    }

    public function store(PositionRequest $request)
    {
        $this->positionService->create($request->validated());

        return redirect()->route('positions.index')
            ->with('success', 'Position created successfully.');
    }

    public function edit(int $id)
    {
        $position = $this->positionService->getById($id);

        if (!$position) {
            abort(404);
        }

        $departments = $this->departmentService->getAll();

        return view('positions.edit', compact('position', 'departments'));
    }

    public function update(PositionRequest $request, int $id)
    {
        $this->positionService->update($id, $request->validated());

        return redirect()->route('positions.index')
            ->with('success', 'Position updated successfully.');
    }

    public function destroy(int $id)
    {
        $this->positionService->delete($id);

        return redirect()->route('positions.index')
            ->with('success', 'Position deleted successfully.');
    }
}
