<?php

namespace App\Domains\Recruitment\Repositories;

use App\Domains\Recruitment\Models\Interview;
use Illuminate\Database\Eloquent\Collection;

class InterviewRepository
{
    public function __construct(
        protected Interview $model
    ) {}

    public function findById(int $id): ?Interview
    {
        return $this->model->with(['jobApplication.candidate', 'interviewer'])->find($id);
    }

    public function getByApplication(int $jobApplicationId): Collection
    {
        return $this->model->with('interviewer')
            ->where('job_application_id', $jobApplicationId)
            ->orderBy('scheduled_at')
            ->get();
    }

    public function create(array $data): Interview
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Interview
    {
        $interview = $this->model->findOrFail($id);
        $interview->update($data);
        return $interview->fresh(['interviewer']);
    }
}
