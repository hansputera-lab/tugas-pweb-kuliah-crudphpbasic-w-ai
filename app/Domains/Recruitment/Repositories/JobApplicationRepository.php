<?php

namespace App\Domains\Recruitment\Repositories;

use App\Domains\Recruitment\Models\JobApplication;
use Illuminate\Database\Eloquent\Collection;

class JobApplicationRepository
{
    public function __construct(
        protected JobApplication $model
    ) {}

    public function findById(int $id): ?JobApplication
    {
        return $this->model->with(['jobPosting.department', 'candidate', 'reviewedBy', 'interviews'])->find($id);
    }

    public function getByJobPosting(int $jobPostingId): Collection
    {
        return $this->model->with(['candidate', 'interviews'])
            ->where('job_posting_id', $jobPostingId)
            ->orderBy('applied_at', 'desc')
            ->get();
    }

    public function getByCandidate(int $candidateId): Collection
    {
        return $this->model->with('jobPosting')
            ->where('candidate_id', $candidateId)
            ->orderBy('applied_at', 'desc')
            ->get();
    }

    public function create(array $data): JobApplication
    {
        return $this->model->create($data);
    }

    public function updateStatus(int $id, string $status, ?int $reviewedBy = null): JobApplication
    {
        $app = $this->model->findOrFail($id);
        $data = ['status' => $status];
        if ($reviewedBy) {
            $data['reviewed_by'] = $reviewedBy;
            $data['reviewed_at'] = now();
        }
        $app->update($data);
        return $app->fresh(['jobPosting', 'candidate']);
    }

    public function countByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }
}
