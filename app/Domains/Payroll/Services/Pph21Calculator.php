<?php

namespace App\Domains\Payroll\Services;

use App\Domains\Payroll\DTOs\Pph21Result;
use App\Domains\Payroll\Repositories\Pph21SettingRepository;
use App\Domains\Payroll\Models\Pph21TerRate;
use App\Domains\Payroll\Models\Pph21Setting;
use App\Domains\Payroll\Models\EmployeeTaxStatus;
use App\Domains\Employee\Models\Employee;
use App\Domains\Payroll\Services\BpjsCalculator;
use Carbon\Carbon;

class Pph21Calculator
{
    public function __construct(
        protected Pph21SettingRepository $pph21SettingRepo,
        protected BpjsCalculator $bpjsCalculator,
    ) {}

    public function calculateMonthlyAmount(
        Employee $employee,
        EmployeeTaxStatus $taxStatus,
        float $gajiPokok,
        array $allowances = [],
        array $deductions = [],
        ?BpjsResult $bpjs = null,
        int $year = 2026,
        int $month = 1,
    ): Pph21Result {
        $setting = $this->pph21SettingRepo->findActive($year);

        $category = $this->getTerCategory($taxStatus);
        $monthlyGross = $gajiPokok + array_sum($allowances);

        $pph21PaidThisYear = $this->getPph21PaidYtd($employee, $year, $month);

        $janToMonthGross = ($monthlyGross * $month) + array_sum($this->getBonusYtd($employee, $year, $month));

        $bpjsEmployeeMonthly = $bpjs ? $bpjs->getTotalEmployee() : 0;
        $bpjsEmployeeYtd = $bpjsEmployeeMonthly * $month;

        $biayaJabatanPerBulan = min(
            round($monthlyGross * ($setting->biaya_jabatan_persen / 100)),
            $setting->biaya_jabatan_max_bulan
        );
        $biayaJabatanYtd = $biayaJabatanPerBulan * $month;

        $janjiPensiun = $deductions['pension'] ?? 0;
        $janjiPensiunYtd = $janjiPensiun * $month;

        $nettoPerBulan = $monthlyGross - $biayaJabatanPerBulan - $bpjsEmployeeMonthly - $janjiPensiun;
        $nettoYtd = $janToMonthGross - $biayaJabatanYtd - $bpjsEmployeeYtd - $janjiPensiunYtd;

        $ptkp = $this->getPtkp($taxStatus, $setting);
        $pkpYtd = max(0, $nettoYtd - $ptkp);

        $pph21TerutangSetahun = $this->progressivePph21($pkpYtd, $setting);

        $pph21PerBulanProgressive = $pph21TerutangSetahun / 12;
        $pph21PerBulanSebelumnya = $pph21PaidThisYear;
        $pph21BulanIniProgressive = round($pph21PerBulanProgressive * $month - $pph21PerBulanSebelumnya);

        $terRateValue = $this->getTerRateValue($monthlyGross, $category, $year);
        $pph21PerBulanTer = round($monthlyGross * ($terRateValue / 100));

        $pph21PerBulan = min($pph21PerBulanTer, $pph21BulanIniProgressive);

        if ($month === 12) {
            $pph21PerBulan = $pph21BulanIniProgressive;
        }

        if ($setting->dtp_enabled && $monthlyGross <= $setting->dtp_max_gaji) {
            $pph21PerBulan = 0;
        }

        return new Pph21Result(
            grossIncome: $monthlyGross,
            biayaJabatan: $biayaJabatanPerBulan,
            bpjsDeduction: $bpjsEmployeeMonthly,
            pensionDeduction: $janjiPensiun,
            nettoPerBulan: $nettoPerBulan,
            nettoYtd: $nettoYtd,
            ptkp: $ptkp,
            pkpYtd: $pkpYtd,
            pph21TerutangSetahun: $pph21TerutangSetahun,
            pph21PerBulan: $pph21PerBulan,
            pph21PerBulanProgressive: $pph21BulanIniProgressive,
            pph21PerBulanTer: $pph21PerBulanTer,
            terRatePct: $terRateValue,
            category: $category,
        );
    }

    public function getTerCategory(EmployeeTaxStatus $taxStatus): string
    {
        $status = $taxStatus->tax_status;
        $tanggungan = $taxStatus->jumlah_tanggungan ?? 0;

        $map = [
            'tk0' => 'A', 'tk1' => 'A', 'k0' => 'A',
            'tk2' => 'B', 'tk3' => 'B', 'k1' => 'B', 'k2' => 'B',
            'k3' => 'C',
        ];

        $key = strtolower(substr($status, 0, 1)) . $tanggungan;
        $key = str_replace(' ', '', $key);

        if ($status === 'TK') {
            $key = 'tk' . min($tanggungan, 3);
        } elseif ($status === 'K') {
            $key = 'k' . min($tanggungan, 3);
        }

        return $map[$key] ?? 'A';
    }

    protected function getPtkp(EmployeeTaxStatus $taxStatus, Pph21Setting $setting): float
    {
        $status = $taxStatus->tax_status;
        $tanggungan = min($taxStatus->jumlah_tanggungan ?? 0, 3);

        if ($status === 'TK') {
            $key = 'ptkp_tk' . $tanggungan;
        } else {
            $key = 'ptkp_k' . $tanggungan;
        }

        return $setting->{$key} ?? $setting->ptkp_tk0;
    }

    protected function progressivePph21(float $pkp, Pph21Setting $setting): float
    {
        if ($pkp <= 0) return 0;

        $layers = [
            ['limit' => $setting->tarif_batas1, 'rate' => $setting->tarif_layer1],
            ['limit' => $setting->tarif_batas2, 'rate' => $setting->tarif_layer2],
            ['limit' => $setting->tarif_batas3, 'rate' => $setting->tarif_layer3],
            ['limit' => $setting->tarif_batas4, 'rate' => $setting->tarif_layer4],
            ['limit' => INF, 'rate' => $setting->tarif_layer5],
        ];

        $remaining = $pkp;
        $tax = 0;
        $prevLimit = 0;

        foreach ($layers as $layer) {
            $limit = $layer['limit'];
            $rate = $layer['rate'] / 100;

            if ($remaining <= 0) break;

            $taxableInLayer = min($remaining, $limit - $prevLimit);
            $tax += round($taxableInLayer * $rate);
            $remaining -= $taxableInLayer;
            $prevLimit = $limit;
        }

        return $tax;
    }

    protected function getTerRateValue(float $monthlyGross, string $category, int $year): float
    {
        $rate = Pph21TerRate::where('tax_year', $year)
            ->where('category', $category)
            ->where('min_income', '<=', $monthlyGross)
            ->where(function ($q) use ($monthlyGross) {
                $q->where('max_income', '>=', $monthlyGross)
                  ->orWhereNull('max_income');
            })
            ->first();

        return $rate ? $rate->rate : 0;
    }

    protected function getPph21PaidYtd(Employee $employee, int $year, int $month): float
    {
        // TODO: query actual paid PPh 21 from payroll_items for months < current month
        return 0;
    }

    protected function getBonusYtd(Employee $employee, int $year, int $month): array
    {
        // TODO: query bonuses from payroll_items for months <= current month
        return [];
    }
}
