<?php

namespace App\Domains\Payroll\Services;

use App\Domains\Attendance\Models\Attendance;
use App\Domains\Attendance\Repositories\AttendanceRepository;
use App\Domains\Employee\Models\Employee;
use App\Domains\Employee\Repositories\EmployeeRepository;
use App\Domains\Payroll\Models\PayrollComponent;
use App\Domains\Payroll\Models\PayrollItem;
use App\Domains\Payroll\Models\PayrollPeriod;
use App\Domains\Payroll\Models\Payslip;
use App\Domains\Payroll\Repositories\PayrollDocumentRepository;
use App\Domains\Payroll\Repositories\PayrollItemRepository;
use App\Domains\Payroll\Repositories\PayrollPeriodRepository;
use App\Domains\Settings\Services\SettingService;
use App\Models\User;
use Carbon\Carbon;

class PayrollService
{
    public function __construct(
        protected PayrollPeriodRepository $periodRepo,
        protected PayrollItemRepository $itemRepo,
        protected PayrollDocumentRepository $documentRepo,
        protected EmployeeRepository $employeeRepo,
        protected AttendanceRepository $attendanceRepo,
        protected SettingService $settingService
    ) {}

    public function getPeriodsByYearMonth(int $year, int $month): ?PayrollPeriod
    {
        return $this->periodRepo->findByMonth($year, $month);
    }

    public function getPeriod(int $id): PayrollPeriod
    {
        return $this->periodRepo->findOrFail($id);
    }

    public function getItems(int $periodId)
    {
        return $this->itemRepo->findByPeriod($periodId);
    }

    public function getItem(int $itemId): ?PayrollItem
    {
        return $this->itemRepo->findById($itemId);
    }

    public function getAllPeriods()
    {
        return $this->periodRepo->getAll();
    }

    public function getConfig(): array
    {
        return [
            'working_days' => (int) $this->settingService->get('payroll_working_days', config('hris.payroll_working_days', 22)),
            'late_deduction_rate' => (float) $this->settingService->get('payroll_late_deduction_rate', config('hris.payroll_late_deduction_rate', 0.5)),
            'absent_deduction_rate' => (float) $this->settingService->get('payroll_absent_deduction_rate', config('hris.payroll_absent_deduction_rate', 1.0)),
            'ot_hourly_multiplier' => (float) $this->settingService->get('payroll_ot_hourly_multiplier', config('hris.payroll_ot_hourly_multiplier', 1.5)),
        ];
    }

    public function generate(PayrollPeriod $period): int
    {
        $config = $this->getConfig();
        $components = PayrollComponent::active()->get();
        $employees = $this->employeeRepo->getActive();
        $count = 0;

        foreach ($employees as $employee) {
            $position = $employee->currentPosition();
            $baseSalary = $position && $position->base_salary ? (float) $position->base_salary : 0;

            $attendances = $this->attendanceRepo->getMonthForEmployee($employee, $period->year, $period->month);
            $lateCount = $attendances->where('status', 'late')->count();
            $absentCount = $attendances->where('status', 'absent')->count();

            $dailyRate = $config['working_days'] > 0 ? $baseSalary / $config['working_days'] : 0;
            $deductionLate = round($lateCount * $dailyRate * $config['late_deduction_rate'], 2);
            $deductionAbsent = round($absentCount * $dailyRate * $config['absent_deduction_rate'], 2);

            $allowanceTransport = 0;
            $allowanceMeal = 0;
            $allowanceOther = 0;
            $deductionOther = 0;

            foreach ($components as $component) {
                if ($component->isAllowance()) {
                    $amount = $component->isPercentage()
                        ? round($baseSalary * ($component->value / 100), 2)
                        : (float) $component->value;

                    if (str_contains(strtolower($component->name), 'transport')) {
                        $allowanceTransport += $amount;
                    } elseif (str_contains(strtolower($component->name), 'makan') || str_contains(strtolower($component->name), 'meal')) {
                        $allowanceMeal += $amount;
                    } else {
                        $allowanceOther += $amount;
                    }
                } else {
                    $amount = $component->isPercentage()
                        ? round($baseSalary * ($component->value / 100), 2)
                        : (float) $component->value;
                    $deductionOther += $amount;
                }
            }

            $overtime = $this->getOvertime($employee, $period->year, $period->month, $dailyRate, $config['ot_hourly_multiplier']);

            $item = $this->itemRepo->upsert($period, $employee, [
                'base_salary' => number_format($baseSalary, 2, '.', ''),
                'allowance_transport' => number_format($allowanceTransport, 2, '.', ''),
                'allowance_meal' => number_format($allowanceMeal, 2, '.', ''),
                'allowance_other' => number_format($allowanceOther, 2, '.', ''),
                'deduction_late' => number_format($deductionLate, 2, '.', ''),
                'deduction_absent' => number_format($deductionAbsent, 2, '.', ''),
                'deduction_other' => number_format($deductionOther, 2, '.', ''),
                'overtime_hours' => number_format($overtime['hours'], 2, '.', ''),
                'overtime_pay' => number_format($overtime['pay'], 2, '.', ''),
                'status' => $period->status === 'paid' ? 'paid' : 'draft',
            ]);
            $item->recompute();
            $item->save();

            $count++;
        }

        return $count;
    }

    public function updateItem(PayrollItem $item, array $data): PayrollItem
    {
        $item->fill($data);
        $item->recompute();
        $item->save();
        return $item;
    }

    public function finalize(PayrollPeriod $period, User $user): PayrollPeriod
    {
        if ($period->items()->count() === 0) {
            $this->generate($period);
        }
        $period->items()->update(['status' => 'finalized']);
        return $this->periodRepo->updateStatus($period, 'finalized', $user->id);
    }

    public function markPaid(PayrollPeriod $period): PayrollPeriod
    {
        $period->items()->update(['status' => 'paid']);
        return $this->periodRepo->updateStatus($period, 'paid');
    }

    public function generatePayslips(PayrollPeriod $period): int
    {
        $count = 0;
        foreach ($period->items as $item) {
            if (!$item->payslip) {
                Payslip::create([
                    'payroll_item_id' => $item->id,
                    'employee_id' => $item->employee_id,
                    'payroll_period_id' => $period->id,
                    'payslip_number' => $this->generatePayslipNumber($period, $item),
                    'generated_at' => now(),
                ]);
            }
            $count++;
        }
        return $count;
    }

    public function getPayslip(int $itemId): ?PayrollItem
    {
        $item = $this->itemRepo->findById($itemId);
        if ($item && $item->payslip) {
            $item->payslip->markViewed();
        }
        return $item;
    }

    public function getEmployeePayslips(int $employeeId)
    {
        return $this->itemRepo->findByEmployee($employeeId);
    }

    public function getEmployeePayslipForPeriod(int $periodId, int $employeeId): ?PayrollItem
    {
        return $this->itemRepo->findForEmployeePeriod($periodId, $employeeId);
    }

    private function generatePayslipNumber(PayrollPeriod $period, PayrollItem $item): string
    {
        return 'PS-' . $period->year . str_pad((string) $period->month, 2, '0', STR_PAD_LEFT)
            . '-' . str_pad((string) $item->employee_id, 4, '0', STR_PAD_LEFT);
    }

    private function getOvertime(Employee $employee, int $year, int $month, float $dailyRate, float $multiplier): array
    {
        if (!class_exists(\App\Domains\Shift\Models\OvertimeRequest::class)) {
            return ['hours' => 0, 'pay' => 0];
        }

        $hours = \App\Domains\Shift\Models\OvertimeRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('hours');

        $hourlyRate = $dailyRate > 0 ? ($dailyRate / 8) * $multiplier : 0;
        $pay = round((float) $hours * $hourlyRate, 2);

        return ['hours' => (float) $hours, 'pay' => $pay];
    }

    public function getDocuments(int $payrollItemId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->documentRepo->getByPayrollItem($payrollItemId);
    }

    public function uploadDocument(int $payrollItemId, int $employeeId, array $data, $file): ?\App\Domains\Payroll\Models\PayrollDocument
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('payroll-documents', $filename, 'public');

        return $this->documentRepo->create([
            'payroll_item_id' => $payrollItemId,
            'employee_id' => $employeeId,
            'name' => $data['name'] ?? $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $data['file_type'] ?? 'other',
            'notes' => $data['notes'] ?? null,
            'uploaded_by' => auth()->id(),
        ]);
    }

    public function deleteDocument(int $id): bool
    {
        $doc = $this->documentRepo->findById($id);
        if (!$doc) return false;

        $fullPath = storage_path('app/public/' . $doc->file_path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        return $this->documentRepo->delete($id);
    }
}
