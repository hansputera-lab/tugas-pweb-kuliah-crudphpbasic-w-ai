<?php

namespace App\Domains\Recruitment\Repositories;

use App\Domains\Recruitment\Models\JobPosting;
use Illuminate\Database\Eloquent\Collection;

class JobPostingRepository
{
    public function __construct(
        protected JobPosting $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->with(['department', 'position', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById(int $id): ?JobPosting
    {
        return $this->model->with(['department', 'position', 'createdBy', 'applications.candidate'])
            ->find($id);
    }

    public function getOpen(): Collection
    {
        return $this->model->open()->with(['department', 'position'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): JobPosting
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): JobPosting
    {
        $posting = $this->model->findOrFail($id);
        $posting->update($data);
        return $posting->fresh(['department', 'position']);
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function countOpen(): int
    {
        return $this->model->where('status', 'open')->count();
    }
}
