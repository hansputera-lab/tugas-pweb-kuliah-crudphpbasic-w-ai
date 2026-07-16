<?php

namespace App\Domains\Recruitment\Repositories;

use App\Domains\Recruitment\Models\Candidate;
use Illuminate\Database\Eloquent\Collection;

class CandidateRepository
{
    public function __construct(
        protected Candidate $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->with('applications.jobPosting')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById(int $id): ?Candidate
    {
        return $this->model->with('applications.jobPosting.department')->find($id);
    }

    public function findByEmail(string $email): ?Candidate
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data): Candidate
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Candidate
    {
        $candidate = $this->model->findOrFail($id);
        $candidate->update($data);
        return $candidate->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
