<?php

namespace Database\Seeders;

use App\Domains\Auth\Models\Permission;
use App\Domains\Auth\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'view_employees', 'label' => 'View Employees', 'module' => 'employees'],
            ['name' => 'view_employee_sensitive', 'label' => 'View Sensitive Employee Data', 'module' => 'employees'],
            ['name' => 'manage_employees', 'label' => 'Manage Employees (CRUD)', 'module' => 'employees'],
            ['name' => 'suspend_employees', 'label' => 'Suspend/Unsuspend Employees', 'module' => 'employees'],
            ['name' => 'view_departments', 'label' => 'View Departments', 'module' => 'departments'],
            ['name' => 'manage_departments', 'label' => 'Manage Departments', 'module' => 'departments'],
            ['name' => 'view_positions', 'label' => 'View Positions', 'module' => 'positions'],
            ['name' => 'manage_positions', 'label' => 'Manage Positions', 'module' => 'positions'],
            ['name' => 'view_attendance', 'label' => 'View All Attendance', 'module' => 'attendance'],
            ['name' => 'manage_attendance', 'label' => 'Manage Attendance', 'module' => 'attendance'],
            ['name' => 'view_own_attendance', 'label' => 'View Own Attendance', 'module' => 'attendance'],
            ['name' => 'view_leave', 'label' => 'View All Leave Requests', 'module' => 'leave'],
            ['name' => 'manage_leave', 'label' => 'Manage Leave Types/Balances', 'module' => 'leave'],
            ['name' => 'approve_leave', 'label' => 'Approve/Reject Leave', 'module' => 'leave'],
            ['name' => 'view_own_leave', 'label' => 'View Own Leave Requests', 'module' => 'leave'],
            ['name' => 'request_leave', 'label' => 'Submit Leave Request', 'module' => 'leave'],
            ['name' => 'view_payroll', 'label' => 'View Payroll Data', 'module' => 'payroll'],
            ['name' => 'manage_payroll', 'label' => 'Manage Payroll (CRUD)', 'module' => 'payroll'],
            ['name' => 'view_own_payslip', 'label' => 'View Own Payslip', 'module' => 'payroll'],
            ['name' => 'view_reimbursement', 'label' => 'View All Reimbursement Claims', 'module' => 'reimbursement'],
            ['name' => 'manage_reimbursement', 'label' => 'Manage Reimbursement Settings', 'module' => 'reimbursement'],
            ['name' => 'approve_reimbursement', 'label' => 'Approve/Reject Reimbursement', 'module' => 'reimbursement'],
            ['name' => 'view_own_claims', 'label' => 'View Own Reimbursement Claims', 'module' => 'reimbursement'],
            ['name' => 'submit_claim', 'label' => 'Submit Reimbursement Claim', 'module' => 'reimbursement'],
            ['name' => 'view_shifts', 'label' => 'View Shift Configurations', 'module' => 'shifts'],
            ['name' => 'manage_shifts', 'label' => 'Manage Shift Configurations', 'module' => 'shifts'],
            ['name' => 'view_own_schedule', 'label' => 'View Own Shift Schedule', 'module' => 'shifts'],
            ['name' => 'manage_overtime', 'label' => 'Manage Overtime Requests', 'module' => 'shifts'],
            ['name' => 'approve_overtime', 'label' => 'Approve/Reject Overtime', 'module' => 'shifts'],
            ['name' => 'view_own_overtime', 'label' => 'View Own Overtime Requests', 'module' => 'shifts'],
            ['name' => 'request_overtime', 'label' => 'Submit Overtime Request', 'module' => 'shifts'],
            ['name' => 'view_performance', 'label' => 'View All Performance Data', 'module' => 'performance'],
            ['name' => 'manage_performance', 'label' => 'Manage KPIs and Appraisals', 'module' => 'performance'],
            ['name' => 'view_own_appraisal', 'label' => 'View Own Appraisal', 'module' => 'performance'],
            ['name' => 'view_recruitment', 'label' => 'View Recruitment Data', 'module' => 'recruitment'],
            ['name' => 'manage_recruitment', 'label' => 'Manage Job Postings', 'module' => 'recruitment'],
            ['name' => 'manage_candidates', 'label' => 'Manage Candidates & Applications', 'module' => 'recruitment'],
            ['name' => 'view_settings', 'label' => 'View Settings', 'module' => 'settings'],
            ['name' => 'manage_settings', 'label' => 'Manage Settings', 'module' => 'settings'],
            ['name' => 'view_users', 'label' => 'View Users', 'module' => 'users'],
            ['name' => 'manage_users', 'label' => 'Manage Users', 'module' => 'users'],
            ['name' => 'manage_roles', 'label' => 'Manage Roles & Permissions', 'module' => 'users'],
            ['name' => 'view_reports', 'label' => 'View Reports', 'module' => 'reports'],
            ['name' => 'view_dashboard_admin', 'label' => 'View Admin Dashboard', 'module' => 'dashboard'],
            ['name' => 'view_audit_log', 'label' => 'View Audit Log', 'module' => 'system'],
            ['name' => 'bpjs.view', 'label' => 'View BPJS Data', 'module' => 'bpjs'],
            ['name' => 'bpjs.manage', 'label' => 'Manage BPJS Rates', 'module' => 'bpjs'],
            ['name' => 'bpjs.configure', 'label' => 'Configure BPJS Settings', 'module' => 'bpjs'],
            ['name' => 'pph21.view', 'label' => 'View PPh 21 Data', 'module' => 'pph21'],
            ['name' => 'pph21.manage', 'label' => 'Manage PPh 21 Calculation', 'module' => 'pph21'],
            ['name' => 'pph21.configure', 'label' => 'Configure PPh 21 Settings', 'module' => 'pph21'],
            ['name' => 'payroll.run', 'label' => 'Execute Payroll Run', 'module' => 'payroll'],
            ['name' => 'payroll.preview', 'label' => 'Preview Payroll', 'module' => 'payroll'],
            ['name' => 'payroll.calculate', 'label' => 'Trigger Payroll Calc', 'module' => 'payroll'],
            ['name' => 'tax.reports', 'label' => 'View Tax Reports', 'module' => 'reports'],
            ['name' => 'bpjs.reports', 'label' => 'View BPJS Reports', 'module' => 'reports'],
        ];

        $permByName = collect();
        foreach ($permissions as $perm) {
            $permByName->put($perm['name'], Permission::updateOrCreate(
                ['name' => $perm['name']],
                $perm
            )->id);
        }

        $roleDefinitions = [
            'super_admin' => [
                'label' => 'Super Admin',
                'names' => array_column($permissions, 'name'),
            ],
            'hr_manager' => [
                'label' => 'HR Manager',
                'names' => ['view_employees', 'view_employee_sensitive', 'manage_employees', 'suspend_employees', 'view_departments', 'manage_departments', 'view_positions', 'manage_positions', 'view_attendance', 'manage_attendance', 'view_own_attendance', 'view_leave', 'manage_leave', 'approve_leave', 'view_own_leave', 'request_leave', 'view_payroll', 'manage_payroll', 'view_own_payslip', 'view_reimbursement', 'manage_reimbursement', 'approve_reimbursement', 'view_own_claims', 'submit_claim', 'view_shifts', 'manage_shifts', 'view_own_schedule', 'manage_overtime', 'approve_overtime', 'view_own_overtime', 'request_overtime', 'view_performance', 'manage_performance', 'view_own_appraisal', 'view_recruitment', 'manage_recruitment', 'manage_candidates', 'view_settings', 'manage_settings', 'view_reports', 'view_dashboard_admin', 'view_audit_log', 'bpjs.view', 'bpjs.manage', 'bpjs.configure', 'pph21.view', 'pph21.manage', 'pph21.configure', 'payroll.run', 'payroll.preview', 'payroll.calculate', 'tax.reports', 'bpjs.reports'],
            ],
            'hr_staff' => [
                'label' => 'HR Staff',
                'names' => ['view_employees', 'manage_attendance', 'view_leave', 'approve_leave', 'view_reimbursement', 'view_own_attendance', 'view_own_leave', 'request_leave', 'view_own_payslip', 'view_own_claims', 'submit_claim', 'view_own_schedule', 'view_own_overtime', 'request_overtime', 'view_own_appraisal'],
            ],
            'manager' => [
                'label' => 'Manager / Supervisor',
                'names' => ['view_employees', 'view_employee_sensitive', 'view_attendance', 'view_leave', 'approve_leave', 'view_performance', 'approve_overtime', 'view_own_attendance', 'view_own_leave', 'request_leave', 'view_own_payslip', 'view_own_claims', 'submit_claim', 'view_own_schedule', 'view_own_overtime', 'request_overtime', 'view_own_appraisal', 'view_reports', 'view_dashboard_admin'],
            ],
            'payroll_specialist' => [
                'label' => 'Payroll Specialist',
                'names' => ['view_payroll', 'manage_payroll', 'view_own_payslip', 'view_settings', 'view_employees', 'view_reports', 'bpjs.view', 'bpjs.manage', 'pph21.view', 'pph21.manage', 'payroll.run', 'payroll.preview', 'payroll.calculate', 'tax.reports', 'bpjs.reports'],
            ],
            'executive' => [
                'label' => 'Executive',
                'names' => ['view_reports', 'view_dashboard_admin', 'view_employees', 'view_payroll', 'view_attendance', 'view_leave', 'view_performance', 'view_recruitment', 'tax.reports', 'bpjs.reports'],
            ],
            'recruiter' => [
                'label' => 'Recruiter',
                'names' => ['view_recruitment', 'manage_recruitment', 'manage_candidates', 'view_employees', 'view_departments', 'view_positions', 'view_own_attendance', 'view_own_leave', 'request_leave', 'view_own_payslip', 'view_own_claims', 'submit_claim', 'view_own_schedule', 'view_own_overtime', 'request_overtime', 'view_own_appraisal'],
            ],
            'it_admin' => [
                'label' => 'IT Admin',
                'names' => ['view_users', 'manage_users', 'manage_roles', 'view_settings', 'manage_settings', 'view_employees', 'view_departments', 'view_positions', 'view_own_attendance', 'view_own_leave', 'request_leave', 'view_own_payslip', 'view_own_claims', 'submit_claim', 'view_own_schedule', 'view_own_overtime', 'request_overtime', 'view_own_appraisal', 'view_audit_log'],
            ],
            'employee' => [
                'label' => 'Employee',
                'names' => ['view_own_attendance', 'view_own_leave', 'request_leave', 'view_own_payslip', 'view_own_claims', 'submit_claim', 'view_own_schedule', 'view_own_overtime', 'request_overtime', 'view_own_appraisal'],
            ],
        ];

        foreach ($roleDefinitions as $name => $config) {
            $role = Role::updateOrCreate(
                ['name' => $name],
                ['label' => $config['label'], 'is_system' => true]
            );
            $role->permissions()->sync(
                $permByName->only($config['names'])->values()->toArray()
            );
        }
    }
}
