<?php

namespace App\Domains\Recruitment\Repositories;

use App\Domains\Recruitment\Models\Onboarding;
use Illuminate\Database\Eloquent\Collection;

class OnboardingRepository
{
    public function __construct(
        protected Onboarding $model
    ) {}

    public function findById(int $id): ?Onboarding
    {
        return $this->model->with(['employee.user', 'jobApplication', 'assignedTo'])->find($id);
    }

    public function getByEmployee(int $employeeId): ?Onboarding
    {
        return $this->model->with('assignedTo')->where('employee_id', $employeeId)->first();
    }

    public function getPending(): Collection
    {
        return $this->model->with(['employee.user'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('created_at')
            ->get();
    }

    public function create(array $data): Onboarding
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Onboarding
    {
        $onboarding = $this->model->findOrFail($id);
        $onboarding->update($data);
        return $onboarding->fresh(['employee.user']);
    }
}
