<?php

namespace App\Domains\Performance\Repositories;

use App\Domains\Employee\Models\Employee;
use App\Domains\Performance\Models\Appraisal;
use App\Domains\Performance\Models\AppraisalDetail;
use App\Domains\Performance\Models\Feedback360;
use Illuminate\Database\Eloquent\Collection;

class AppraisalRepository
{
    public function __construct(
        protected Appraisal $model,
        protected AppraisalDetail $detailModel,
        protected Feedback360 $feedbackModel
    ) {}

    public function findById(int $id): ?Appraisal
    {
        return $this->model->with(['employee.user', 'employee.department', 'reviewer', 'details.kpi', 'feedback.reviewer'])
            ->find($id);
    }

    public function create(Employee $employee, string $period, ?int $reviewerId = null): Appraisal
    {
        return $this->model->updateOrCreate(
            ['employee_id' => $employee->id, 'period' => $period],
            ['status' => 'draft', 'reviewer_id' => $reviewerId]
        );
    }

    public function createDetail(Appraisal $appraisal, array $data): AppraisalDetail
    {
        return $this->detailModel->updateOrCreate(
            ['appraisal_id' => $appraisal->id, 'kpi_id' => $data['kpi_id']],
            $data
        );
    }

    public function addFeedback(Appraisal $appraisal, array $data): Feedback360
    {
        return $this->feedbackModel->create(array_merge($data, [
            'appraisal_id' => $appraisal->id,
        ]));
    }

    public function complete(Appraisal $appraisal, float $totalScore, ?string $grade, ?string $notes, ?int $reviewerId): Appraisal
    {
        $appraisal->update([
            'status' => 'completed',
            'total_score' => number_format($totalScore, 2, '.', ''),
            'final_grade' => $grade,
            'notes' => $notes,
            'reviewed_at' => now(),
            'reviewer_id' => $reviewerId,
        ]);

        return $appraisal->fresh(['employee.user', 'employee.department', 'reviewer', 'details.kpi', 'feedback.reviewer']);
    }

    public function getAll(): Collection
    {
        return $this->model->with(['employee.user', 'employee.department', 'reviewer'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getByEmployee(int $employeeId): Collection
    {
        return $this->model->with(['details.kpi', 'feedback'])
            ->where('employee_id', $employeeId)
            ->orderByDesc('created_at')
            ->get();
    }
}
