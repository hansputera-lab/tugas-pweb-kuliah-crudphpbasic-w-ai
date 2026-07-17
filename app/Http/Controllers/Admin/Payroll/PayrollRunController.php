<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Domains\Payroll\Models\PayrollPeriod;
use App\Domains\Payroll\Models\PayrollItem;
use App\Domains\Payroll\Models\PayrollRunDetail;
use App\Domains\Payroll\Services\PayrollService;
use App\Domains\Payroll\Services\BpjsCalculator;
use App\Domains\Payroll\Services\Pph21Calculator;
use App\Domains\Payroll\Repositories\EmployeeTaxStatusRepository;
use App\Domains\Payroll\DTOs\BpjsResult;
use App\Domains\Payroll\DTOs\Pph21Result;
use App\Domains\Employee\Repositories\EmployeeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class PayrollRunController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService,
        protected BpjsCalculator $bpjsCalculator,
        protected Pph21Calculator $pph21Calculator,
        protected EmployeeTaxStatusRepository $taxStatusRepo,
        protected EmployeeRepository $employeeRepo,
    ) {}

    public function index()
    {
        Gate::authorize('payroll.preview');

        $periods = PayrollPeriod::orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $currentPeriod = $periods->firstWhere('is_active', true) ?? $periods->first();

        $runDetails = collect();
        $totals = null;
        if ($currentPeriod) {
            $runDetails = PayrollRunDetail::with(['employee.user', 'payrollItem'])
                ->where('payroll_period_id', $currentPeriod->id)
                ->get();
            $totals = [
                'employer_bpjs' => $runDetails->sum('employer_bpjs'),
                'employee_bpjs' => $runDetails->sum('employee_bpjs'),
                'pph21' => $runDetails->sum('pph21_amount'),
                'net_salary' => $runDetails->sum('net_salary'),
            ];
        }

        $payrollItems = $currentPeriod
            ? PayrollItem::where('payroll_period_id', $currentPeriod->id)->get()->keyBy('employee_id')
            : collect();

        return view('payroll.run.index', compact('periods', 'currentPeriod', 'runDetails', 'totals', 'payrollItems'));
    }

    public function preview(Request $request)
    {
        Gate::authorize('payroll.preview');

        $periodId = $request->input('period_id');
        $period = PayrollPeriod::findOrFail($periodId);

        $employees = $this->employeeRepo->getActive();
        $results = [];

        foreach ($employees as $employee) {
            $taxStatus = $this->taxStatusRepo->findByEmployee($employee->id);
            $payrollItem = PayrollItem::where('payroll_period_id', $period->id)
                ->where('employee_id', $employee->id)
                ->first();

            $gajiPokok = (float) ($payrollItem?->base_salary ?? 0);

            $bpjs = $this->bpjsCalculator->calculateForEmployee($employee, $gajiPokok, []);
            $pph21 = $taxStatus
                ? $this->pph21Calculator->calculateMonthlyAmount(
                    $employee, $taxStatus, $gajiPokok, [], [], $bpjs,
                    $period->year, $period->month
                )
                : null;

            $results[] = [
                'employee' => $employee,
                'payrollItem' => $payrollItem,
                'bpjs' => $bpjs,
                'pph21' => $pph21,
            ];
        }

        $periods = PayrollPeriod::orderByDesc('year')->orderByDesc('month')->get();

        return view('payroll.run.index', compact('periods', 'period', 'results'));
    }

    public function run(Request $request)
    {
        Gate::authorize('payroll.run');

        $periodId = $request->input('period_id');
        $period = PayrollPeriod::findOrFail($periodId);

        DB::transaction(function () use ($period) {
            $employees = $this->employeeRepo->getActive();

            PayrollRunDetail::where('payroll_period_id', $period->id)->delete();

            foreach ($employees as $employee) {
                $payrollItem = PayrollItem::where('payroll_period_id', $period->id)
                    ->where('employee_id', $employee->id)
                    ->first();

                if (!$payrollItem) continue;

                $taxStatus = $this->taxStatusRepo->findByEmployee($employee->id);
                $gajiPokok = (float) ($payrollItem->base_salary ?? 0);

                $bpjs = $this->bpjsCalculator->calculateForEmployee($employee, $gajiPokok, []);
                $pph21 = $taxStatus
                    ? $this->pph21Calculator->calculateMonthlyAmount(
                        $employee, $taxStatus, $gajiPokok, [], [], $bpjs,
                        $period->year, $period->month
                    )
                    : null;

                $totalAllowances = $payrollItem->total_allowances ?? 0;
                $totalDeductions = $payrollItem->total_deductions ?? 0;
                $netSalary = $gajiPokok + $totalAllowances - $totalDeductions
                    - ($bpjs->getTotalEmployee()) - ($pph21?->pph21PerBulan ?? 0);

                PayrollRunDetail::create([
                    'payroll_period_id' => $period->id,
                    'payroll_item_id' => $payrollItem->id,
                    'employee_id' => $employee->id,
                    'gross_income' => $gajiPokok,
                    'bpjs_kes_employee' => $bpjs->employeeContributions['kes'] ?? 0,
                    'bpjs_kes_employer' => $bpjs->employerContributions['kes'] ?? 0,
                    'bpjs_jht_employee' => $bpjs->employeeContributions['jht'] ?? 0,
                    'bpjs_jht_employer' => $bpjs->employerContributions['jht'] ?? 0,
                    'bpjs_jp_employee' => $bpjs->employeeContributions['jp'] ?? 0,
                    'bpjs_jp_employer' => $bpjs->employerContributions['jp'] ?? 0,
                    'bpjs_jkk_employer' => $bpjs->employerContributions['jkk'] ?? 0,
                    'bpjs_jkm_employer' => $bpjs->employerContributions['jkm'] ?? 0,
                    'total_bpjs_employee' => $bpjs->getTotalEmployee(),
                    'total_bpjs_employer' => $bpjs->getTotalEmployer(),
                    'net_income_before_tax' => $gajiPokok + $totalAllowances - $totalDeductions,
                    'pph21_monthly' => $pph21?->pph21PerBulan ?? 0,
                    'pph21_ter_rate' => $pph21?->terRatePct ?? null,
                    'pph21_method' => 'ter',
                    'pph21_dtp_amount' => 0,
                    'take_home_pay' => max(0, $netSalary),
                    'calculated_at' => now(),
                ]);
            }
        });

        return redirect()->route('payroll.run.index', ['period_id' => $period->id])
            ->with('success', "Payroll run completed for {$period->period_name}");
    }
}
