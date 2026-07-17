<?php

namespace App\Domains\Payroll\DTOs;

class Pph21Result
{
    public function __construct(
        public readonly float $grossIncome = 0,
        public readonly float $biayaJabatan = 0,
        public readonly float $bpjsDeduction = 0,
        public readonly float $pensionDeduction = 0,
        public readonly float $nettoPerBulan = 0,
        public readonly float $nettoYtd = 0,
        public readonly float $ptkp = 0,
        public readonly float $pkpYtd = 0,
        public readonly float $pph21TerutangSetahun = 0,
        public readonly float $pph21PerBulan = 0,
        public readonly float $pph21PerBulanProgressive = 0,
        public readonly float $pph21PerBulanTer = 0,
        public readonly float $terRatePct = 0,
        public readonly string $category = 'A',
    ) {}

    public function toArray(): array
    {
        return [
            'gross_income' => $this->grossIncome,
            'biaya_jabatan' => $this->biayaJabatan,
            'bpjs_deduction' => $this->bpjsDeduction,
            'pension_deduction' => $this->pensionDeduction,
            'netto_per_bulan' => $this->nettoPerBulan,
            'netto_ytd' => $this->nettoYtd,
            'ptkp' => $this->ptkp,
            'pkp_ytd' => $this->pkpYtd,
            'pph21_terutang_setahun' => $this->pph21TerutangSetahun,
            'pph21_per_bulan' => $this->pph21PerBulan,
            'pph21_per_bulan_progressive' => $this->pph21PerBulanProgressive,
            'pph21_per_bulan_ter' => $this->pph21PerBulanTer,
            'ter_rate_pct' => $this->terRatePct,
            'category' => $this->category,
        ];
    }
}
