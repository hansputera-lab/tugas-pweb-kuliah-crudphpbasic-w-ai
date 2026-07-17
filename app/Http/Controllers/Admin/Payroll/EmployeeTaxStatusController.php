<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Domains\Payroll\Repositories\EmployeeTaxStatusRepository;
use App\Domains\Employee\Repositories\EmployeeRepository;
use App\Domains\Payroll\Models\EmployeeTaxStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmployeeTaxStatusController extends Controller
{
    public function __construct(
        protected EmployeeTaxStatusRepository $taxStatusRepo,
        protected EmployeeRepository $employeeRepo,
    ) {}

    public function index()
    {
        Gate::authorize('pph21.view');

        $statuses = EmployeeTaxStatus::with('employee.user')
            ->orderBy('employee_id')
            ->paginate(20);

        return view('payroll.tax-status.index', compact('statuses'));
    }

    public function create()
    {
        Gate::authorize('pph21.manage');

        $employees = $this->employeeRepo->getActive();
        $statuses = collect();
        $taxStatus = null;

        return view('payroll.tax-status.form', compact('employees', 'statuses', 'taxStatus'));
    }

    public function edit(EmployeeTaxStatus $taxStatus)
    {
        Gate::authorize('pph21.manage');

        $employees = collect();
        $statuses = $this->taxStatusRepo->findByEmployee($taxStatus->employee_id);

        return view('payroll.tax-status.form', compact('taxStatus', 'employees', 'statuses'));
    }

    public function store(Request $request)
    {
        Gate::authorize('pph21.manage');

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tax_status' => 'required|string|in:TK,K',
            'jumlah_tanggungan' => 'required|integer|min:0',

            'ptkp_status' => 'nullable|string|in:tk0,tk1,tk2,tk3,k0,k1,k2,k3',
            'effective_date' => 'required|date',
        ]);

        $validated['ptkp_status'] ??= strtolower($validated['tax_status'] . min($validated['jumlah_tanggungan'], 3));

        EmployeeTaxStatus::updateOrCreate(
            ['employee_id' => $validated['employee_id']],
            $validated
        );

        return redirect()->route('payroll.tax-status.index')
            ->with('success', 'Tax status saved.');
    }

    public function update(Request $request, EmployeeTaxStatus $taxStatus)
    {
        Gate::authorize('pph21.manage');

        $validated = $request->validate([
            'tax_status' => 'required|string|in:TK,K',
            'jumlah_tanggungan' => 'required|integer|min:0',
            'npwp' => 'nullable|string|max:20',
            'ptkp_status' => 'nullable|string|in:tk0,tk1,tk2,tk3,k0,k1,k2,k3',
            'effective_date' => 'required|date',
        ]);

        $validated['ptkp_status'] ??= strtolower($validated['tax_status'] . min($validated['jumlah_tanggungan'], 3));

        $taxStatus->update($validated);

        return redirect()->route('payroll.tax-status.index')
            ->with('success', 'Tax status updated.');
    }
}
