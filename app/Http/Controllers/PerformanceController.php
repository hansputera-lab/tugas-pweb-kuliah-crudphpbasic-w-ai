<?php

namespace App\Http\Controllers;

use App\Domains\Employee\Services\EmployeeService;
use App\Domains\Performance\Services\PerformanceService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    public function __construct(
        protected PerformanceService $performanceService,
        protected EmployeeService $employeeService
    ) {}

    // ---------- KPI management ----------
    public function kpis()
    {
        $kpis = $this->performanceService->getAllKpis();
        return view('performance.kpis.index', compact('kpis'));
    }

    public function storeKpi(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:competency,goal,behavior',
            'target_value' => 'nullable|numeric|min:0',
            'weight' => 'required|integer|min:0|max:100',
            'measurement_unit' => 'nullable|string|max:50',
        ]);

        $this->performanceService->createKpi($data);

        return redirect()->route('performance.kpis')
            ->with('success', 'KPI added.');
    }

    public function updateKpi(Request $request, int $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:competency,goal,behavior',
            'target_value' => 'nullable|numeric|min:0',
            'weight' => 'required|integer|min:0|max:100',
            'measurement_unit' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $this->performanceService->updateKpi($id, $data);

        return redirect()->route('performance.kpis')
            ->with('success', 'KPI updated.');
    }

    public function destroyKpi(int $id)
    {
        $this->performanceService->deleteKpi($id);

        return redirect()->route('performance.kpis')
            ->with('success', 'KPI deleted.');
    }

    // ---------- Appraisals ----------
    public function appraisals()
    {
        $appraisals = $this->performanceService->getAll();
        $thresholds = $this->performanceService->getGradeThresholds();
        return view('performance.appraisals.index', compact('appraisals', 'thresholds'));
    }

    public function createAppraisal()
    {
        $employees = $this->employeeService->getActive();
        $kpis = $this->performanceService->getKpis();
        return view('performance.appraisals.create', compact('employees', 'kpis'));
    }

    public function storeAppraisal(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period' => 'required|string|max:20',
        ]);

        $employee = $this->employeeService->getById($data['employee_id']);
        $appraisal = $this->performanceService->createAppraisal($employee, $data['period'], Auth::id());

        return redirect()->route('performance.appraisals.evaluate', $appraisal)
            ->with('success', 'Appraisal created. Score the KPIs below.');
    }

    public function show(int $id)
    {
        $appraisal = $this->performanceService->getById($id);
        if (!$appraisal) {
            abort(404);
        }
        $thresholds = $this->performanceService->getGradeThresholds();
        return view('performance.appraisals.show', compact('appraisal', 'thresholds'));
    }

    public function evaluate(int $id)
    {
        $appraisal = $this->performanceService->getById($id);
        if (!$appraisal) {
            abort(404);
        }
        if ($appraisal->isCompleted()) {
            return redirect()->route('performance.appraisals.show', $appraisal)
                ->with('info', 'This appraisal is already completed.');
        }
        return view('performance.appraisals.evaluate', compact('appraisal'));
    }

    public function storeEvaluate(Request $request, int $id)
    {
        $appraisal = $this->performanceService->getById($id);
        if (!$appraisal) {
            abort(404);
        }

        $scores = $request->input('scores', []);
        $comments = $request->input('comments', []);
        $notes = $request->input('notes');

        $this->performanceService->evaluate($appraisal, $scores, $notes, Auth::id(), $comments);

        return redirect()->route('performance.appraisals.show', $appraisal)
            ->with('success', 'Appraisal evaluated and completed.');
    }

    public function storeFeedback(Request $request, int $id)
    {
        $appraisal = $this->performanceService->getById($id);
        if (!$appraisal) {
            abort(404);
        }

        $data = $request->validate([
            'reviewer_name' => 'required|string|max:255',
            'relationship' => 'required|in:manager,peer,subordinate,self',
            'rating' => 'nullable|numeric|min:0|max:100',
            'comment' => 'nullable|string|max:1000',
        ]);

        $data['reviewer_id'] = Auth::id();
        $this->performanceService->addFeedback($appraisal, $data);

        return redirect()->route('performance.appraisals.show', $appraisal)
            ->with('success', '360 feedback added.');
    }

    // ---------- Employee self-service ----------
    public function myAppraisals()
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'No employee record linked.');
        }
        $appraisals = $this->performanceService->getByEmployee($employee->id);
        return view('performance.my.appraisals', compact('appraisals', 'employee'));
    }

    public function myAppraisalShow(int $id)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'No employee record linked.');
        }
        $appraisal = $this->performanceService->getById($id);
        if (!$appraisal || $appraisal->employee_id !== $employee->id) {
            abort(403);
        }
        $thresholds = $this->performanceService->getGradeThresholds();
        return view('performance.my.appraisal', compact('appraisal', 'thresholds', 'employee'));
    }
}
