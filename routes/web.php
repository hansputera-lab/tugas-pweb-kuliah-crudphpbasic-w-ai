<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\ReimbursementController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Admin\Payroll\BpjsSettingController;
use App\Http\Controllers\Admin\Payroll\Pph21SettingController;
use App\Http\Controllers\Admin\Payroll\EmployeeTaxStatusController;
use App\Http\Controllers\Admin\Payroll\PayrollRunController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Employee self-service (open to all authenticated users)
    Route::prefix('my')->name('my.')->group(function () {
        Route::get('/profile', [EmployeeController::class, 'myProfile'])->name('profile');
        Route::get('/attendance', [AttendanceController::class, 'myHistory'])->name('attendance');
        Route::get('/leaves', [LeaveController::class, 'myLeaves'])->name('leaves');
        Route::get('/leaves/balance', [LeaveController::class, 'myBalance'])->name('leaves.balance');
    });

    // Leave request (open to all with employee record)
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves/request', [LeaveController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/{leaveRequest}', [LeaveController::class, 'show'])->name('leaves.show')->whereNumber('leaveRequest');
    Route::delete('/leaves/{leaveRequest}', [LeaveController::class, 'destroy'])->name('leaves.destroy')->whereNumber('leaveRequest');

    // Employee self-service payroll & claims
    Route::prefix('my')->name('my.')->group(function () {
        Route::get('/payslips', [PayrollController::class, 'myPayslips'])->name('payslips');
        Route::get('/payslips/{period}', [PayrollController::class, 'myPayslip'])->name('payslip')->where('period', '[0-9]+');
        Route::get('/claims', [ReimbursementController::class, 'myClaims'])->name('claims');
        Route::get('/claims/create', [ReimbursementController::class, 'create'])->name('claims.create');
        Route::post('/claims', [ReimbursementController::class, 'store'])->name('claims.store');
        Route::get('/schedule', [ShiftController::class, 'mySchedule'])->name('schedule');
        Route::get('/overtime', [ShiftController::class, 'myOvertime'])->name('overtime');
        Route::get('/overtime/create', [ShiftController::class, 'myOvertimeCreate'])->name('overtime.create');
        Route::post('/overtime', [ShiftController::class, 'myOvertimeStore'])->name('overtime.store');
        Route::get('/appraisals', [PerformanceController::class, 'myAppraisals'])->name('appraisals');
        Route::get('/appraisals/{appraisal}', [PerformanceController::class, 'myAppraisalShow'])->name('appraisal.show')->where('appraisal', '[0-9]+');
    });

    // Attendance check-in/out
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');

    // === HR MANAGEMENT ROUTES (permission-gated) ===

    // Employees
    Route::middleware('permission:view_employees,manage_employees')->group(function () {
        Route::resource('employees', EmployeeController::class)->except('show');
        Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show')->whereNumber('employee');
        Route::post('/employees/{employee}/suspend', [EmployeeController::class, 'suspend'])->name('employees.suspend')->whereNumber('employee');
        Route::post('/employees/{employee}/unsuspend', [EmployeeController::class, 'unsuspend'])->name('employees.unsuspend')->whereNumber('employee');
    });

    // Departments
    Route::middleware('permission:view_departments,manage_departments')->group(function () {
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::resource('departments', DepartmentController::class)->except(['show', 'index']);
    });

    // Positions
    Route::middleware('permission:view_positions,manage_positions')->group(function () {
        Route::get('/positions', [PositionController::class, 'index'])->name('positions.index');
        Route::resource('positions', PositionController::class)->except(['show', 'index']);
    });

    // Attendance management
    Route::middleware('permission:view_attendance,manage_attendance')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    });

    // Leave management
    Route::middleware('permission:view_leave,approve_leave')->group(function () {
        Route::get('/leaves/all', [LeaveController::class, 'index'])->name('leaves.index');
        Route::put('/leaves/{leaveRequest}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
        Route::put('/leaves/{leaveRequest}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
    });

    // Payroll management
    Route::middleware('permission:view_payroll,manage_payroll')->group(function () {
        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/', [PayrollController::class, 'index'])->name('index');
            Route::post('/generate', [PayrollController::class, 'generate'])->name('generate');
            Route::get('/components', [PayrollController::class, 'components'])->name('components');
            Route::post('/components', [PayrollController::class, 'storeComponent'])->name('components.store');
            Route::put('/components/{component}', [PayrollController::class, 'updateComponent'])->name('components.update');
            Route::delete('/components/{component}', [PayrollController::class, 'destroyComponent'])->name('components.destroy');
        });
        Route::get('/payroll/{period}', [PayrollController::class, 'show'])->name('payroll.show')->where('period', '[0-9]+');
        Route::post('/payroll/{period}/regenerate', [PayrollController::class, 'regenerate'])->name('payroll.regenerate')->where('period', '[0-9]+');
        Route::post('/payroll/{period}/finalize', [PayrollController::class, 'finalize'])->name('payroll.finalize')->where('period', '[0-9]+');
        Route::post('/payroll/{period}/pay', [PayrollController::class, 'pay'])->name('payroll.pay')->where('period', '[0-9]+');
        Route::get('/payroll/items/{item}/edit', [PayrollController::class, 'editItem'])->name('payroll.items.edit')->where('item', '[0-9]+');
        Route::put('/payroll/items/{item}', [PayrollController::class, 'updateItem'])->name('payroll.items.update')->where('item', '[0-9]+');
        Route::get('/payroll/payslip/{item}', [PayrollController::class, 'payslip'])->name('payroll.payslip')->where('item', '[0-9]+');
        Route::get('/payroll/items/{item}/documents', [PayrollController::class, 'documents'])->name('payroll.documents')->where('item', '[0-9]+');
        Route::post('/payroll/items/{item}/documents', [PayrollController::class, 'uploadDocument'])->name('payroll.documents.upload')->where('item', '[0-9]+');
        Route::get('/payroll/documents/{document}/download', [PayrollController::class, 'downloadDocument'])->name('payroll.documents.download')->where('document', '[0-9]+');
        Route::delete('/payroll/documents/{document}', [PayrollController::class, 'deleteDocument'])->name('payroll.documents.destroy')->where('document', '[0-9]+');
    });

    // === PAYROLL & TAX ROUTES ===
    Route::middleware('permission:bpjs.view,bpjs.manage,bpjs.configure')->group(function () {
        Route::get('/payroll/bpjs', [BpjsSettingController::class, 'index'])->name('payroll.bpjs.settings');
        Route::put('/payroll/bpjs/{component}', [BpjsSettingController::class, 'update'])->name('payroll.bpjs.update');
    });

    Route::middleware('permission:pph21.view,pph21.manage,pph21.configure')->group(function () {
        Route::get('/payroll/pph21', [Pph21SettingController::class, 'index'])->name('payroll.pph21.settings');
        Route::put('/payroll/pph21', [Pph21SettingController::class, 'update'])->name('payroll.pph21.update');
    });

    Route::middleware('permission:pph21.view,pph21.manage')->group(function () {
        Route::get('/payroll/tax-status', [EmployeeTaxStatusController::class, 'index'])->name('payroll.tax-status.index');
        Route::get('/payroll/tax-status/create', [EmployeeTaxStatusController::class, 'create'])->name('payroll.tax-status.create');
        Route::post('/payroll/tax-status', [EmployeeTaxStatusController::class, 'store'])->name('payroll.tax-status.store');
        Route::get('/payroll/tax-status/{taxStatus}/edit', [EmployeeTaxStatusController::class, 'edit'])->name('payroll.tax-status.edit');
        Route::put('/payroll/tax-status/{taxStatus}', [EmployeeTaxStatusController::class, 'update'])->name('payroll.tax-status.update');
    });

    Route::middleware('permission:payroll.preview,payroll.run,payroll.calculate')->group(function () {
        Route::get('/payroll/run', [PayrollRunController::class, 'index'])->name('payroll.run.index');
        Route::get('/payroll/run/preview', [PayrollRunController::class, 'preview'])->name('payroll.run.preview');
        Route::post('/payroll/run/execute', [PayrollRunController::class, 'run'])->name('payroll.run.execute');
    });

    // Reimbursement management
    Route::middleware('permission:view_reimbursement,approve_reimbursement')->group(function () {
        Route::get('/reimbursements', [ReimbursementController::class, 'index'])->name('reimbursements.index');
        Route::get('/reimbursements/categories', [ReimbursementController::class, 'categories'])->name('reimbursements.categories');
        Route::post('/reimbursements/categories', [ReimbursementController::class, 'storeCategory'])->name('reimbursements.categories.store');
        Route::put('/reimbursements/categories/{category}', [ReimbursementController::class, 'updateCategory'])->name('reimbursements.categories.update')->where('category', '[0-9]+');
        Route::delete('/reimbursements/categories/{category}', [ReimbursementController::class, 'destroyCategory'])->name('reimbursements.categories.destroy')->where('category', '[0-9]+');
        Route::get('/reimbursements/{claim}', [ReimbursementController::class, 'show'])->name('reimbursements.show')->where('claim', '[0-9]+');
        Route::put('/reimbursements/{claim}/approve', [ReimbursementController::class, 'approve'])->name('reimbursements.approve')->where('claim', '[0-9]+');
        Route::put('/reimbursements/{claim}/reject', [ReimbursementController::class, 'reject'])->name('reimbursements.reject')->where('claim', '[0-9]+');
    });

    // Shift & Overtime management
    Route::middleware('permission:view_shifts,manage_shifts,manage_overtime,approve_overtime')->group(function () {
        Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::get('/shifts/definitions', [ShiftController::class, 'definitions'])->name('shifts.definitions');
        Route::post('/shifts/definitions', [ShiftController::class, 'storeDefinition'])->name('shifts.definitions.store');
        Route::put('/shifts/definitions/{shift}', [ShiftController::class, 'updateDefinition'])->name('shifts.definitions.update')->where('shift', '[0-9]+');
        Route::delete('/shifts/definitions/{shift}', [ShiftController::class, 'destroyDefinition'])->name('shifts.definitions.destroy')->where('shift', '[0-9]+');
        Route::get('/shifts/assign', [ShiftController::class, 'assignForm'])->name('shifts.assign');
        Route::post('/shifts/assign', [ShiftController::class, 'assign'])->name('shifts.assign.store');
        Route::get('/overtime', [ShiftController::class, 'overtimeIndex'])->name('overtime.index');
        Route::get('/overtime/{overtime}', [ShiftController::class, 'overtimeShow'])->name('overtime.show')->where('overtime', '[0-9]+');
        Route::put('/overtime/{overtime}/approve', [ShiftController::class, 'overtimeApprove'])->name('overtime.approve')->where('overtime', '[0-9]+');
        Route::put('/overtime/{overtime}/reject', [ShiftController::class, 'overtimeReject'])->name('overtime.reject')->where('overtime', '[0-9]+');
    });

    // Performance / KPI management
    Route::middleware('permission:view_performance,manage_performance')->group(function () {
        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('/kpis', [PerformanceController::class, 'kpis'])->name('kpis');
            Route::post('/kpis', [PerformanceController::class, 'storeKpi'])->name('kpis.store');
            Route::put('/kpis/{kpi}', [PerformanceController::class, 'updateKpi'])->name('kpis.update')->where('kpi', '[0-9]+');
            Route::delete('/kpis/{kpi}', [PerformanceController::class, 'destroyKpi'])->name('kpis.destroy')->where('kpi', '[0-9]+');
            Route::get('/appraisals', [PerformanceController::class, 'appraisals'])->name('appraisals');
            Route::get('/appraisals/create', [PerformanceController::class, 'createAppraisal'])->name('appraisals.create');
            Route::post('/appraisals', [PerformanceController::class, 'storeAppraisal'])->name('appraisals.store');
            Route::get('/appraisals/{appraisal}', [PerformanceController::class, 'show'])->name('appraisals.show')->where('appraisal', '[0-9]+');
            Route::get('/appraisals/{appraisal}/evaluate', [PerformanceController::class, 'evaluate'])->name('appraisals.evaluate')->where('appraisal', '[0-9]+');
            Route::put('/appraisals/{appraisal}/evaluate', [PerformanceController::class, 'storeEvaluate'])->name('appraisals.evaluate.update')->where('appraisal', '[0-9]+');
            Route::post('/appraisals/{appraisal}/feedback', [PerformanceController::class, 'storeFeedback'])->name('appraisals.feedback')->where('appraisal', '[0-9]+');
        });
    });

    // Reports
    Route::middleware('permission:view_reports')->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/employees', [ReportController::class, 'employeeList'])->name('employees');
            Route::get('/attendance', [ReportController::class, 'attendanceSummary'])->name('attendance');
            Route::get('/leaves', [ReportController::class, 'leaveReport'])->name('leaves');
        });
    });

    // Audit Log
    Route::middleware('permission:view_audit_log')->group(function () {
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
    });

    // Settings
    Route::middleware('permission:manage_settings')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::delete('/settings/logo/{type}', [SettingController::class, 'removeLogo'])->name('settings.logo.remove');
    });

    // Recruitment
    Route::middleware('permission:view_recruitment,manage_recruitment')->group(function () {
        Route::get('/recruitment', [RecruitmentController::class, 'index'])->name('recruitment.index');
        Route::get('/recruitment/create', [RecruitmentController::class, 'create'])->name('recruitment.create');
        Route::post('/recruitment', [RecruitmentController::class, 'store'])->name('recruitment.store');
        Route::get('/recruitment/{id}', [RecruitmentController::class, 'show'])->name('recruitment.show')->whereNumber('id');
        Route::get('/recruitment/{id}/edit', [RecruitmentController::class, 'edit'])->name('recruitment.edit')->whereNumber('id');
        Route::put('/recruitment/{id}', [RecruitmentController::class, 'update'])->name('recruitment.update')->whereNumber('id');
        Route::delete('/recruitment/{id}', [RecruitmentController::class, 'destroy'])->name('recruitment.destroy')->whereNumber('id');

        Route::get('/candidates', [RecruitmentController::class, 'candidates'])->name('recruitment.candidates.index');
        Route::get('/candidates/create', [RecruitmentController::class, 'createCandidate'])->name('recruitment.candidates.create');
        Route::post('/candidates', [RecruitmentController::class, 'storeCandidate'])->name('recruitment.candidates.store');
        Route::get('/candidates/{id}', [RecruitmentController::class, 'showCandidate'])->name('recruitment.candidates.show')->whereNumber('id');
        Route::put('/candidates/{id}', [RecruitmentController::class, 'updateCandidate'])->name('recruitment.candidates.update')->whereNumber('id');

        Route::post('/applications', [RecruitmentController::class, 'apply'])->name('recruitment.apply');
        Route::put('/applications/{id}/status', [RecruitmentController::class, 'updateApplicationStatus'])->name('recruitment.applications.status')->whereNumber('id');

        Route::post('/interviews', [RecruitmentController::class, 'scheduleInterview'])->name('recruitment.interviews.store');
        Route::put('/interviews/{id}', [RecruitmentController::class, 'updateInterview'])->name('recruitment.interviews.update')->whereNumber('id');

        Route::get('/onboarding/{id}', [RecruitmentController::class, 'onboarding'])->name('recruitment.onboarding.show')->whereNumber('id');
        Route::put('/onboarding/{id}', [RecruitmentController::class, 'updateOnboarding'])->name('recruitment.onboarding.update')->whereNumber('id');
    });
});

require __DIR__.'/auth.php';
