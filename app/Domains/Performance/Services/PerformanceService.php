<?php

namespace App\Domains\Performance\Services;

use App\Domains\Employee\Models\Employee;
use App\Domains\Performance\Models\Appraisal;
use App\Domains\Performance\Models\Kpi;
use App\Domains\Performance\Repositories\AppraisalRepository;
use App\Domains\Performance\Repositories\KpiRepository;
use App\Domains\Settings\Services\SettingService;
use App\Models\User;

class PerformanceService
{
    public function __construct(
        protected AppraisalRepository $appraisalRepo,
        protected KpiRepository $kpiRepo,
        protected SettingService $settingService
    ) {}

    public function getKpis()
    {
        return $this->kpiRepo->getActive();
    }

    public function getAllKpis()
    {
        return $this->kpiRepo->getAll();
    }

    public function getKpi(int $id): ?Kpi
    {
        return $this->kpiRepo->findById($id);
    }

    public function createKpi(array $data): Kpi
    {
        return $this->kpiRepo->create($data);
    }

    public function updateKpi(int $id, array $data): Kpi
    {
        $kpi = $this->kpiRepo->findById($id);
        return $this->kpiRepo->update($kpi, $data);
    }

    public function deleteKpi(int $id): bool
    {
        return $this->kpiRepo->delete($id);
    }

    public function getGradeThresholds(): array
    {
        return [
            'A' => (int) $this->settingService->get('kpi_grade_a_min', config('hris.kpi_grade_a_min', 90)),
            'B' => (int) $this->settingService->get('kpi_grade_b_min', config('hris.kpi_grade_b_min', 80)),
            'C' => (int) $this->settingService->get('kpi_grade_c_min', config('hris.kpi_grade_c_min', 70)),
            'D' => (int) $this->settingService->get('kpi_grade_d_min', config('hris.kpi_grade_d_min', 60)),
        ];
    }

    public function computeGrade(float $score): string
    {
        $t = $this->getGradeThresholds();
        if ($score >= $t['A']) return 'A';
        if ($score >= $t['B']) return 'B';
        if ($score >= $t['C']) return 'C';
        if ($score >= $t['D']) return 'D';
        return 'E';
    }

    public function createAppraisal(Employee $employee, string $period, ?int $reviewerId = null): Appraisal
    {
        $appraisal = $this->appraisalRepo->create($employee, $period, $reviewerId);

        foreach ($this->kpiRepo->getActive() as $kpi) {
            $this->appraisalRepo->createDetail($appraisal, [
                'kpi_id' => $kpi->id,
                'score' => 0,
                'weight' => $kpi->weight,
                'achievement' => null,
                'comment' => null,
            ]);
        }

        return $appraisal->fresh(['employee.user', 'details.kpi', 'feedback']);
    }

    public function evaluate(Appraisal $appraisal, array $scores, ?string $notes, ?int $reviewerId, array $comments = []): Appraisal
    {
        $totalWeight = 0;
        $weightedSum = 0;

        foreach ($scores as $detailId => $score) {
            $detail = $appraisal->details->firstWhere('id', $detailId);
            if (!$detail) {
                continue;
            }
            $score = (float) $score;
            $detail->update([
                'score' => number_format($score, 2, '.', ''),
                'weight' => $detail->weight,
                'comment' => $comments[$detailId] ?? $detail->comment,
            ]);
            $weightedSum += $score * $detail->weight;
            $totalWeight += $detail->weight;
        }

        $totalScore = $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : 0;
        $grade = $this->computeGrade($totalScore);

        return $this->appraisalRepo->complete($appraisal, $totalScore, $grade, $notes, $reviewerId);
    }

    public function addFeedback(Appraisal $appraisal, array $data): \App\Domains\Performance\Models\Feedback360
    {
        return $this->appraisalRepo->addFeedback($appraisal, $data);
    }

    public function getAll()
    {
        return $this->appraisalRepo->getAll();
    }

    public function getById(int $id): ?Appraisal
    {
        return $this->appraisalRepo->findById($id);
    }

    public function getByEmployee(int $employeeId)
    {
        return $this->appraisalRepo->getByEmployee($employeeId);
    }
}
