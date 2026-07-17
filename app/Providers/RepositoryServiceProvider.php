<?php

namespace App\Providers;

use App\Domains\ActivityLog\Models\ActivityLog;
use App\Domains\ActivityLog\Repositories\ActivityLogRepository;
use App\Domains\Attendance\Models\Attendance;
use App\Domains\Attendance\Repositories\AttendanceRepository;
use App\Domains\Auth\Models\Permission;
use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Repositories\PermissionRepository;
use App\Domains\Auth\Repositories\RoleRepository;
use App\Domains\Recruitment\Models\Candidate;
use App\Domains\Recruitment\Models\Interview;
use App\Domains\Recruitment\Models\JobApplication;
use App\Domains\Recruitment\Models\JobPosting;
use App\Domains\Recruitment\Models\Onboarding;
use App\Domains\Recruitment\Repositories\CandidateRepository;
use App\Domains\Recruitment\Repositories\InterviewRepository;
use App\Domains\Recruitment\Repositories\JobApplicationRepository;
use App\Domains\Recruitment\Repositories\JobPostingRepository;
use App\Domains\Recruitment\Repositories\OnboardingRepository;
use App\Domains\Department\Models\Department;
use App\Domains\Department\Repositories\DepartmentRepository;
use App\Domains\Employee\Models\Employee;
use App\Domains\Employee\Repositories\EmployeeRepository;
use App\Domains\Leave\Models\LeaveBalance;
use App\Domains\Leave\Models\LeaveRequest;
use App\Domains\Leave\Models\LeaveType;
use App\Domains\Leave\Repositories\LeaveBalanceRepository;
use App\Domains\Leave\Repositories\LeaveRequestRepository;
use App\Domains\Position\Models\Position;
use App\Domains\Position\Repositories\PositionRepository;
use App\Domains\Payroll\Models\BpjsSetting;
use App\Domains\Payroll\Models\EmployeeBpjsOverride;
use App\Domains\Payroll\Models\EmployeeTaxStatus;
use App\Domains\Payroll\Models\PayrollDocument;
use App\Domains\Payroll\Models\PayrollItem;
use App\Domains\Payroll\Models\PayrollPeriod;
use App\Domains\Payroll\Models\PayrollRunDetail;
use App\Domains\Payroll\Models\Pph21Setting;
use App\Domains\Payroll\Repositories\BpjsSettingRepository;
use App\Domains\Payroll\Repositories\EmployeeBpjsOverrideRepository;
use App\Domains\Payroll\Repositories\EmployeeTaxStatusRepository;
use App\Domains\Payroll\Repositories\PayrollDocumentRepository;
use App\Domains\Payroll\Repositories\PayrollItemRepository;
use App\Domains\Payroll\Repositories\PayrollPeriodRepository;
use App\Domains\Payroll\Repositories\PayrollRunDetailRepository;
use App\Domains\Payroll\Repositories\Pph21SettingRepository;
use App\Domains\Reimbursement\Models\ExpenseCategory;
use App\Domains\Reimbursement\Models\ReimbursementClaim;
use App\Domains\Reimbursement\Repositories\ExpenseCategoryRepository;
use App\Domains\Reimbursement\Repositories\ReimbursementClaimRepository;
use App\Domains\Settings\Models\Setting;
use App\Domains\Shift\Models\EmployeeShift;
use App\Domains\Shift\Models\OvertimeRequest;
use App\Domains\Shift\Models\Shift;
use App\Domains\Shift\Repositories\OvertimeRequestRepository;
use App\Domains\Shift\Repositories\ShiftRepository;
use App\Domains\Performance\Models\Appraisal;
use App\Domains\Performance\Models\AppraisalDetail;
use App\Domains\Performance\Models\Feedback360;
use App\Domains\Performance\Models\Kpi;
use App\Domains\Performance\Repositories\AppraisalRepository;
use App\Domains\Performance\Repositories\KpiRepository;
use App\Domains\Settings\Repositories\SettingRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ActivityLogRepository::class, fn () => new ActivityLogRepository(new ActivityLog));
        $this->app->singleton(SettingRepository::class, fn () => new SettingRepository(new Setting));
        $this->app->singleton(EmployeeRepository::class, fn () => new EmployeeRepository(new Employee));
        $this->app->singleton(DepartmentRepository::class, fn () => new DepartmentRepository(new Department));
        $this->app->singleton(PositionRepository::class, fn () => new PositionRepository(new Position));
        $this->app->singleton(AttendanceRepository::class, fn () => new AttendanceRepository(new Attendance));
        $this->app->singleton(LeaveRequestRepository::class, fn () => new LeaveRequestRepository(new LeaveRequest));
        $this->app->singleton(LeaveBalanceRepository::class, fn () => new LeaveBalanceRepository(new LeaveBalance));
        $this->app->singleton(PayrollPeriodRepository::class, fn () => new PayrollPeriodRepository(new PayrollPeriod));
        $this->app->singleton(PayrollItemRepository::class, fn () => new PayrollItemRepository(new PayrollItem));
        $this->app->singleton(PayrollDocumentRepository::class, fn () => new PayrollDocumentRepository(new PayrollDocument));
        $this->app->singleton(ExpenseCategoryRepository::class, fn () => new ExpenseCategoryRepository(new ExpenseCategory));
        $this->app->singleton(ReimbursementClaimRepository::class, fn () => new ReimbursementClaimRepository(new ReimbursementClaim));
        $this->app->singleton(ShiftRepository::class, fn () => new ShiftRepository(new Shift, new EmployeeShift));
        $this->app->singleton(OvertimeRequestRepository::class, fn () => new OvertimeRequestRepository(new OvertimeRequest));
        $this->app->singleton(KpiRepository::class, fn () => new KpiRepository(new Kpi));
        $this->app->singleton(AppraisalRepository::class, fn () => new AppraisalRepository(new Appraisal, new AppraisalDetail, new Feedback360));
        $this->app->singleton(PermissionRepository::class, fn () => new PermissionRepository(new Permission));
        $this->app->singleton(RoleRepository::class, fn () => new RoleRepository(new Role));
        $this->app->singleton(JobPostingRepository::class, fn () => new JobPostingRepository(new JobPosting));
        $this->app->singleton(CandidateRepository::class, fn () => new CandidateRepository(new Candidate));
        $this->app->singleton(JobApplicationRepository::class, fn () => new JobApplicationRepository(new JobApplication));
        $this->app->singleton(InterviewRepository::class, fn () => new InterviewRepository(new Interview));
        $this->app->singleton(OnboardingRepository::class, fn () => new OnboardingRepository(new Onboarding));
        $this->app->singleton(EmployeeTaxStatusRepository::class, fn () => new EmployeeTaxStatusRepository(new EmployeeTaxStatus));
        $this->app->singleton(BpjsSettingRepository::class, fn () => new BpjsSettingRepository(new BpjsSetting));
        $this->app->singleton(EmployeeBpjsOverrideRepository::class, fn () => new EmployeeBpjsOverrideRepository(new EmployeeBpjsOverride));
        $this->app->singleton(Pph21SettingRepository::class, fn () => new Pph21SettingRepository(new Pph21Setting));
        $this->app->singleton(PayrollRunDetailRepository::class, fn () => new PayrollRunDetailRepository(new PayrollRunDetail));
    }

    public function boot(): void
    {
    }
}
