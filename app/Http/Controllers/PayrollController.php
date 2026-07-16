<?php

namespace App\Http\Controllers;

use App\Domains\Payroll\Models\PayrollComponent;
use App\Domains\Payroll\Models\PayrollPeriod;
use App\Domains\Payroll\Services\PayrollService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService
    ) {}

    public function index()
    {
        $periodList = $this->payrollService->getAllPeriods();
        $config = $this->payrollService->getConfig();

        return view('payroll.index', compact('periodList', 'config'));
    }

    public function show(int $id)
    {
        $period = $this->payrollService->getPeriod($id);
        $items = $this->payrollService->getItems($id);
        $config = $this->payrollService->getConfig();

        $summary = [
            'employees' => $items->count(),
            'total_net' => $items->sum('net_salary'),
            'total_base' => $items->sum('base_salary'),
            'total_deduction' => $items->sum('total_deduction'),
            'total_allowance' => $items->sum('total_allowance'),
            'total_overtime' => $items->sum('overtime_pay'),
        ];

        return view('payroll.show', compact('period', 'items', 'config', 'summary'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|between:2000,2100',
            'month' => 'required|integer|between:1,12',
        ]);

        $period = $this->payrollService->getPeriodsByYearMonth((int) $request->year, (int) $request->month);

        if (!$period) {
            $period = PayrollPeriod::create([
                'year' => (int) $request->year,
                'month' => (int) $request->month,
                'status' => 'draft',
            ]);
        }

        $count = $this->payrollService->generate($period);

        return redirect()->route('payroll.show', $period->id)
            ->with('success', "Payroll generated for {$count} employees.");
    }

    public function regenerate(int $id)
    {
        $period = $this->payrollService->getPeriod($id);

        if ($period->isPaid()) {
            return back()->with('error', 'Cannot regenerate a paid payroll period.');
        }

        $count = $this->payrollService->generate($period);

        return redirect()->route('payroll.show', $period->id)
            ->with('success', "Payroll recalculated for {$count} employees.");
    }

    public function finalize(int $id)
    {
        $period = $this->payrollService->getPeriod($id);
        $period = $this->payrollService->finalize($period, Auth::user());

        return redirect()->route('payroll.show', $period->id)
            ->with('success', 'Payroll period finalized.');
    }

    public function pay(int $id)
    {
        $period = $this->payrollService->getPeriod($id);
        $this->payrollService->markPaid($period);
        $this->payrollService->generatePayslips($period);

        return redirect()->route('payroll.show', $period->id)
            ->with('success', 'Payroll marked as paid and payslips generated.');
    }

    public function editItem(int $itemId)
    {
        $item = $this->payrollService->getItem($itemId);

        if (!$item) {
            abort(404);
        }

        return view('payroll.items.edit', compact('item'));
    }

    public function updateItem(Request $request, int $itemId)
    {
        $item = $this->payrollService->getItem($itemId);

        if (!$item) {
            abort(404);
        }

        if ($item->period->isPaid()) {
            return back()->with('error', 'Cannot edit a paid payroll item.');
        }

        $data = $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'allowance_transport' => 'nullable|numeric|min:0',
            'allowance_meal' => 'nullable|numeric|min:0',
            'allowance_other' => 'nullable|numeric|min:0',
            'deduction_late' => 'nullable|numeric|min:0',
            'deduction_absent' => 'nullable|numeric|min:0',
            'deduction_other' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_pay' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $this->payrollService->updateItem($item, $data);

        return redirect()->route('payroll.show', $item->payroll_period_id)
            ->with('success', 'Payroll item updated successfully.');
    }

    public function payslip(int $itemId)
    {
        $item = $this->payrollService->getPayslip($itemId);

        if (!$item) {
            abort(404);
        }

        return view('payroll.payslip', compact('item'));
    }

    public function documents(int $itemId)
    {
        $item = $this->payrollService->getItem($itemId);

        if (!$item) {
            abort(404);
        }

        $documents = $this->payrollService->getDocuments($itemId);

        return view('payroll.documents.index', compact('item', 'documents'));
    }

    public function uploadDocument(Request $request, int $itemId)
    {
        $item = $this->payrollService->getItem($itemId);

        if (!$item) {
            abort(404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:200',
            'file_type' => 'required|in:payslip,tax_form,supporting_doc,contract,other',
            'notes' => 'nullable|string|max:500',
            'file' => 'required|file|max:10240',
        ]);

        $this->payrollService->uploadDocument(
            $itemId,
            $item->employee_id,
            $data,
            $request->file('file')
        );

        return redirect()->route('payroll.documents', $itemId)
            ->with('success', 'Document uploaded.');
    }

    public function downloadDocument(int $documentId)
    {
        $doc = \App\Domains\Payroll\Models\PayrollDocument::findOrFail($documentId);
        $path = storage_path('app/public/' . $doc->file_path);

        if (!file_exists($path)) {
            return back()->with('error', 'File not found.');
        }

        return response()->download($path, $doc->name . '.' . pathinfo($doc->file_path, PATHINFO_EXTENSION));
    }

    public function deleteDocument(int $documentId)
    {
        $this->payrollService->deleteDocument($documentId);
        return back()->with('success', 'Document deleted.');
    }

    public function components()
    {
        $components = PayrollComponent::orderBy('type')->orderBy('sort_order')->get();

        return view('payroll.components.index', compact('components'));
    }

    public function storeComponent(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:allowance,deduction',
            'calculation' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['is_active'] = true;
        PayrollComponent::create($data);

        return redirect()->route('payroll.components')
            ->with('success', 'Payroll component added.');
    }

    public function updateComponent(Request $request, int $id)
    {
        $component = PayrollComponent::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:allowance,deduction',
            'calculation' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['is_active'] = $request->has('is_active');
        $component->update($data);

        return redirect()->route('payroll.components')
            ->with('success', 'Payroll component updated.');
    }

    public function destroyComponent(int $id)
    {
        PayrollComponent::findOrFail($id)->delete();

        return redirect()->route('payroll.components')
            ->with('success', 'Payroll component deleted.');
    }

    public function myPayslips()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'No employee record linked to your account.');
        }

        $items = $this->payrollService->getEmployeePayslips($employee->id);

        return view('payroll.my.payslips', compact('items', 'employee'));
    }

    public function myPayslip(int $periodId)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'No employee record linked to your account.');
        }

        $item = $this->payrollService->getEmployeePayslipForPeriod($periodId, $employee->id);

        if (!$item) {
            abort(404);
        }

        $item->payslip?->markViewed();

        return view('payroll.my.payslip', compact('item', 'employee'));
    }
}
