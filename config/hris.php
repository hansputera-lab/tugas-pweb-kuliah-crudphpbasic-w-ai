<?php

return [
    'work_start_time' => env('HRIS_WORK_START_TIME', '08:00'),
    'work_end_time' => env('HRIS_WORK_END_TIME', '17:00'),
    'grace_period_minutes' => (int) env('HRIS_GRACE_PERIOD_MINUTES', 15),
    'default_annual_leave_days' => (int) env('HRIS_DEFAULT_ANNUAL_LEAVE_DAYS', 12),
    'default_sick_leave_days' => (int) env('HRIS_DEFAULT_SICK_LEAVE_DAYS', 12),

    // Payroll configuration (admin-customizable via settings UI)
    'payroll_working_days' => (int) env('HRIS_PAYROLL_WORKING_DAYS', 22),
    'payroll_late_deduction_rate' => (float) env('HRIS_PAYROLL_LATE_RATE', 0.5),
    'payroll_absent_deduction_rate' => (float) env('HRIS_PAYROLL_ABSENT_RATE', 1.0),
    'payroll_ot_hourly_multiplier' => (float) env('HRIS_PAYROLL_OT_MULTIPLIER', 1.5),

    // KPI / Performance grading (admin-customizable via settings UI)
    'kpi_grade_a_min' => (int) env('HRIS_KPI_GRADE_A', 90),
    'kpi_grade_b_min' => (int) env('HRIS_KPI_GRADE_B', 80),
    'kpi_grade_c_min' => (int) env('HRIS_KPI_GRADE_C', 70),
    'kpi_grade_d_min' => (int) env('HRIS_KPI_GRADE_D', 60),
];
