@php
$user = auth()->user();
$can = fn($perm) => $user->hasPermissionTo($perm);
$hasAny = fn($perms) => $user->hasAnyPermission((array) $perms);
$isActive = fn($matches) => request()->routeIs(...$matches) ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900';
$cls = 'flex items-center px-3 py-2 text-sm font-medium rounded-lg';

$mainItems = [
    ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => ['dashboard'],
     'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
];

$hasAnyHR = $hasAny([
    'view_employees', 'manage_employees',
    'view_departments', 'manage_departments',
    'view_positions', 'manage_positions',
    'view_attendance', 'manage_attendance',
    'view_leave', 'manage_leave', 'approve_leave',
    'view_payroll', 'manage_payroll',
    'view_reimbursement', 'manage_reimbursement', 'approve_reimbursement',
    'view_shifts', 'manage_shifts', 'manage_overtime', 'approve_overtime',
    'view_performance', 'manage_performance',
    'view_recruitment', 'manage_recruitment', 'manage_candidates',
    'view_settings', 'manage_settings',
    'view_users', 'manage_users',
    'view_reports',
]);
@endphp

<nav class="space-y-1">
    @foreach($mainItems as $item)
        <a href="{{ route($item['route']) }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive($item['match']) }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="{{ $item['icon'] }}"/></svg>
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>

@if($hasAnyHR)
    <div>
        <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">HR Management</p>
        <nav class="mt-2 space-y-1">
            @if($hasAny(['view_employees', 'manage_employees']))
                <a href="{{ route('employees.index') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['employees.*']) }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Employees
                </a>
            @endif
            @if($hasAny(['view_departments', 'manage_departments']))
                <a href="{{ route('departments.index') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['departments.*']) }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Departments
                </a>
            @endif
            @if($hasAny(['view_positions', 'manage_positions']))
                <a href="{{ route('positions.index') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['positions.*']) }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    Positions
                </a>
            @endif
            @if($hasAny(['view_attendance', 'manage_attendance']))
                <a href="{{ route('attendance.index') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['attendance.*']) }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Attendance
                </a>
            @endif
            @if($hasAny(['view_leave', 'approve_leave']))
                <a href="{{ route('leaves.index') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['leaves.*']) }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Leave
                </a>
            @endif
            @if($hasAny(['view_payroll', 'manage_payroll']))
                <a href="{{ route('payroll.index') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['payroll.*']) }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Payroll
                </a>
            @endif
            @if($hasAny(['view_reimbursement', 'approve_reimbursement']))
                <a href="{{ route('reimbursements.index') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['reimbursements.*']) }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Reimbursement
                </a>
            @endif
            @if($hasAny(['view_shifts', 'manage_shifts', 'manage_overtime', 'approve_overtime']))
                <a href="{{ route('shifts.index') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['shifts.*', 'overtime.*']) }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Shifts
                </a>
            @endif
            @if($hasAny(['view_performance', 'manage_performance']))
                <a href="{{ route('performance.appraisals') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['performance.*']) }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Performance
                </a>
            @endif
            @if($hasAny(['view_recruitment', 'manage_recruitment']))
                <a href="{{ route('recruitment.index') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['recruitment.*', 'candidates.*']) }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 13.255A23.931 23.931 0 0112 15c-4.183 0-8.037-1.069-10.944-2.94M21 13.255A23.931 23.931 0 0112 15c-4.183 0-8.037-1.069-10.944-2.94M13 9l3 3m0 0l-3 3m3-3H3"/></svg>
                    Recruitment
                </a>
            @endif
            @if($hasAny(['view_reports']))
                <a href="{{ route('reports.employees') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['reports.*']) }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055zM19.194 11A7.001 7.001 0 0013 4.806V11h6.194z"/></svg>
                    Reports
                </a>
            @endif
            @if($hasAny(['view_settings', 'manage_settings']))
                <a href="{{ route('settings.index') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['settings.*']) }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Settings
                </a>
            @endif
        </nav>
    </div>
@endif

@if($user->employee)
<div>
    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">My Portal</p>
    <nav class="mt-2 space-y-1">
        @if($can('view_own_attendance') || $hasAnyHR)
            <a href="{{ route('my.profile') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['my.profile']) }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                My Profile
            </a>
        @endif
        @if($can('view_own_attendance') || $hasAnyHR)
            <a href="{{ route('my.attendance') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['my.attendance']) }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                My Attendance
            </a>
        @endif
        @if($can('request_leave') || $hasAnyHR)
            <a href="{{ route('my.leaves') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['my.leaves']) }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                My Leave
            </a>
        @endif
        @if($can('view_own_payslip') || $hasAnyHR)
            <a href="{{ route('my.payslips') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['my.payslips']) }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                My Payslips
            </a>
        @endif
        @if($can('submit_claim') || $hasAnyHR)
            <a href="{{ route('my.claims') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['my.claims.*']) }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                My Claims
            </a>
        @endif
        @if($can('view_own_schedule') || $hasAnyHR)
            <a href="{{ route('my.schedule') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['my.schedule']) }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M8 7V3m8 4V3m-1 8h.01M12 11h.01M16 11h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                My Schedule
            </a>
        @endif
        @if($can('request_overtime') || $hasAnyHR)
            <a href="{{ route('my.overtime') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['my.overtime*']) }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                My Overtime
            </a>
        @endif
        @if($can('view_own_appraisal') || $hasAnyHR)
            <a href="{{ route('my.appraisals') }}" @click="sidebarOpen = false" class="{{ $cls }} {{ $isActive(['my.appraisals*', 'my.appraisal*']) }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                My Appraisals
            </a>
        @endif
    </nav>
</div>
@endif
