<?php

namespace App\Http\Controllers;

use App\Domains\Department\Models\Department;
use App\Domains\Employee\Models\Employee;
use App\Domains\Position\Models\Position;
use App\Domains\Recruitment\Services\RecruitmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecruitmentController extends Controller
{
    public function __construct(
        protected RecruitmentService $recruitmentService
    ) {}

    public function index(): View
    {
        $jobPostings = $this->recruitmentService->getJobPostings();
        return view('recruitment.job-postings.index', compact('jobPostings'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        return view('recruitment.job-postings.create', compact('departments', 'positions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'employment_type' => 'required|in:full_time,part_time,contract,internship',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,open,closed',
        ]);

        $this->recruitmentService->createJobPosting($validated);
        return redirect()->route('recruitment.index')->with('success', 'Job posting created.');
    }

    public function show(int $id): View
    {
        $posting = $this->recruitmentService->getJobPosting($id);
        if (!$posting) abort(404);
        $applications = $this->recruitmentService->getApplicationsByJobPosting($id);
        return view('recruitment.job-postings.show', compact('posting', 'applications'));
    }

    public function edit(int $id): View
    {
        $posting = $this->recruitmentService->getJobPosting($id);
        if (!$posting) abort(404);
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        return view('recruitment.job-postings.edit', compact('posting', 'departments', 'positions'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'employment_type' => 'required|in:full_time,part_time,contract,internship',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,open,closed',
        ]);

        $this->recruitmentService->updateJobPosting($id, $validated);
        return redirect()->route('recruitment.index')->with('success', 'Job posting updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->recruitmentService->deleteJobPosting($id);
        return redirect()->route('recruitment.index')->with('success', 'Job posting deleted.');
    }

    public function candidates(): View
    {
        $candidates = $this->recruitmentService->getCandidates();
        return view('recruitment.candidates.index', compact('candidates'));
    }

    public function createCandidate(): View
    {
        return view('recruitment.candidates.create');
    }

    public function storeCandidate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'source' => 'required|in:job_board,referral,social_media,direct_apply,other',
            'notes' => 'nullable|string',
        ]);

        $candidate = $this->recruitmentService->createCandidate($validated);
        return redirect()->route('recruitment.candidates.show', $candidate->id)
            ->with('success', 'Candidate created.');
    }

    public function showCandidate(int $id): View
    {
        $candidate = $this->recruitmentService->getCandidate($id);
        if (!$candidate) abort(404);
        return view('recruitment.candidates.show', compact('candidate'));
    }

    public function updateCandidate(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'source' => 'required|in:job_board,referral,social_media,direct_apply,other',
            'notes' => 'nullable|string',
            'resume_path' => 'nullable|string',
        ]);

        $this->recruitmentService->updateCandidate($id, $validated);
        return redirect()->route('recruitment.candidates.show', $id)
            ->with('success', 'Candidate updated.');
    }

    public function apply(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'job_posting_id' => 'required|exists:job_postings,id',
            'candidate_id' => 'required|exists:candidates,id',
            'notes' => 'nullable|string',
        ]);

        $this->recruitmentService->createApplication($validated);
        return redirect()->route('recruitment.show', $validated['job_posting_id'])
            ->with('success', 'Application submitted.');
    }

    public function updateApplicationStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:applied,screening,interview,offer,hired,rejected',
        ]);

        $application = $this->recruitmentService->updateApplicationStatus($id, $validated['status']);

        if ($validated['status'] === 'hired') {
            $candidate = $application->candidate;
            $employee = Employee::where('user_id', auth()->id())->first();
            $this->recruitmentService->startOnboarding([
                'employee_id' => $employee->id,
                'job_application_id' => $id,
            ]);
        }

        return back()->with('success', 'Application status updated.');
    }

    public function scheduleInterview(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'job_application_id' => 'required|exists:job_applications,id',
            'interviewer_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'location' => 'nullable|string',
            'meeting_link' => 'nullable|url',
        ]);

        $this->recruitmentService->scheduleInterview($validated);
        return back()->with('success', 'Interview scheduled.');
    }

    public function updateInterview(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled,no_show',
            'feedback' => 'nullable|string',
            'rating' => 'nullable|numeric|min:1|max:5',
        ]);

        $this->recruitmentService->updateInterview($id, $validated);
        return back()->with('success', 'Interview updated.');
    }

    public function onboarding(int $id): View
    {
        $onboarding = \App\Domains\Recruitment\Models\Onboarding::with(['employee.user', 'jobApplication.candidate'])
            ->findOrFail($id);
        return view('recruitment.onboarding.show', compact('onboarding'));
    }

    public function updateOnboarding(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'checklist' => 'required|array',
            'checklist.*.task' => 'required|string',
            'checklist.*.done' => 'required|boolean',
            'status' => 'nullable|in:pending,in_progress,completed',
        ]);

        $data = ['checklist' => $validated['checklist']];
        if ($request->has('status')) {
            $data['status'] = $validated['status'];
        }

        $this->recruitmentService->updateOnboarding($id, $data);
        return back()->with('success', 'Onboarding updated.');
    }
}
