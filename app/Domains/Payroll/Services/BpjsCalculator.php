<?php

namespace App\Domains\Payroll\Services;

use App\Domains\Payroll\DTOs\BpjsResult;
use App\Domains\Payroll\Repositories\BpjsSettingRepository;
use App\Domains\Payroll\Repositories\EmployeeBpjsOverrideRepository;
use App\Domains\Payroll\Models\BpjsSetting;
use App\Domains\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Collection;

class BpjsCalculator
{
    public function __construct(
        protected BpjsSettingRepository $bpjsSettingRepo,
        protected EmployeeBpjsOverrideRepository $overrideRepo,
    ) {}

    public function calculateForEmployee(Employee $employee, float $gajiPokok, array $allowances): BpjsResult
    {
        $totalGaji = max($gajiPokok, 0) + array_sum($allowances);

        $overrides = $this->overrideRepo->findByEmployee($employee->id);

        $employer = [];
        $employeeContrib = [];
        $totalEmployer = 0;
        $totalEmployee = 0;

        $components = ['kes', 'jkk', 'jkm', 'jht', 'jp'];
        foreach ($components as $component) {
            $rate = $this->getRate($component, $employee, $overrides);

            if (!$rate) continue;

            $base = $totalGaji;
            if ($rate->max_wage && $base > $rate->max_wage) $base = $rate->max_wage;
            if ($rate->min_wage && $base < $rate->min_wage) $base = $rate->min_wage;

            $employerAmt = round($base * $rate->rate_employer / 100);
            $employeeAmt = round($base * $rate->rate_employee / 100);

            $employer[$component] = $employerAmt;
            $employeeContrib[$component] = $employeeAmt;
            $totalEmployer += $employerAmt;
            $totalEmployee += $employeeAmt;
        }

        return new BpjsResult(
            employerContributions: $employer,
            employeeContributions: $employeeContrib,
            totalEmployer: $totalEmployer,
            totalEmployee: $totalEmployee,
        );
    }

    protected function getRate(string $component, Employee $employee, ?Collection $overrides): ?BpjsSetting
    {
        if ($overrides && $overrides->has($component)) {
            $o = $overrides->get($component);
            $setting = new BpjsSetting([
                'rate_employer' => $o->rate_employer,
                'rate_employee' => $o->rate_employee ?? 0,
                'max_wage' => $o->max_wage,
                'min_wage' => $o->min_wage,
            ]);
            $setting->exists = true;
            return $setting;
        }

        $settings = $this->bpjsSettingRepo->getActiveByComponent($component);

        if ($component === 'jkk') {
            $riskLevel = $employee->risk_level ?? 'low';
            return $settings->firstWhere('risk_level', $riskLevel);
        }

        return $settings->first();
    }
}
