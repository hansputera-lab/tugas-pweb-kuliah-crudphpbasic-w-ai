<?php

namespace App\Domains\Recruitment\Services;

use App\Domains\Recruitment\Repositories\CandidateRepository;
use App\Domains\Recruitment\Repositories\InterviewRepository;
use App\Domains\Recruitment\Repositories\JobApplicationRepository;
use App\Domains\Recruitment\Repositories\JobPostingRepository;
use App\Domains\Recruitment\Repositories\OnboardingRepository;

class RecruitmentService
{
    public function __construct(
        protected JobPostingRepository $jobPostingRepo,
        protected CandidateRepository $candidateRepo,
        protected JobApplicationRepository $applicationRepo,
        protected InterviewRepository $interviewRepo,
        protected OnboardingRepository $onboardingRepo,
    ) {}

    public function getJobPostings()
    {
        return $this->jobPostingRepo->getAll();
    }

    public function getJobPosting(int $id)
    {
        return $this->jobPostingRepo->findById($id);
    }

    public function createJobPosting(array $data)
    {
        $data['created_by'] = auth()->id();
        if ($data['status'] === 'open') {
            $data['posted_at'] = now();
        }
        return $this->jobPostingRepo->create($data);
    }

    public function updateJobPosting(int $id, array $data)
    {
        if (isset($data['status']) && $data['status'] === 'open') {
            $data['posted_at'] = now();
        }
        if (isset($data['status']) && $data['status'] === 'closed') {
            $data['closed_at'] = now();
        }
        return $this->jobPostingRepo->update($id, $data);
    }

    public function deleteJobPosting(int $id): bool
    {
        return $this->jobPostingRepo->delete($id);
    }

    public function getCandidates()
    {
        return $this->candidateRepo->getAll();
    }

    public function getCandidate(int $id)
    {
        return $this->candidateRepo->findById($id);
    }

    public function createCandidate(array $data)
    {
        return $this->candidateRepo->create($data);
    }

    public function updateCandidate(int $id, array $data)
    {
        return $this->candidateRepo->update($id, $data);
    }

    public function deleteCandidate(int $id): bool
    {
        return $this->candidateRepo->delete($id);
    }

    public function getApplicationsByJobPosting(int $jobPostingId)
    {
        return $this->applicationRepo->getByJobPosting($jobPostingId);
    }

    public function createApplication(array $data)
    {
        return $this->applicationRepo->create($data);
    }

    public function updateApplicationStatus(int $id, string $status)
    {
        return $this->applicationRepo->updateStatus($id, $status, auth()->id());
    }

    public function scheduleInterview(array $data)
    {
        return $this->interviewRepo->create($data);
    }

    public function updateInterview(int $id, array $data)
    {
        return $this->interviewRepo->update($id, $data);
    }

    public function startOnboarding(array $data)
    {
        $data['checklist'] = [
            ['task' => 'Prepare employment contract', 'done' => false],
            ['task' => 'Create email account', 'done' => false],
            ['task' => 'Prepare workstation', 'done' => false],
            ['task' => 'Schedule orientation', 'done' => false],
            ['task' => 'Assign mentor', 'done' => false],
        ];
        $data['started_at'] = now();
        return $this->onboardingRepo->create($data);
    }

    public function updateOnboarding(int $id, array $data)
    {
        if (isset($data['checklist'])) {
            $allDone = collect($data['checklist'])->every(fn($item) => $item['done']);
            if ($allDone) {
                $data['status'] = 'completed';
                $data['completed_at'] = now();
            }
        }
        return $this->onboardingRepo->update($id, $data);
    }

    public function getOpenPostingsCount(): int
    {
        return $this->jobPostingRepo->countOpen();
    }

    public function countApplicationsByStatus(string $status): int
    {
        return $this->applicationRepo->countByStatus($status);
    }
}
