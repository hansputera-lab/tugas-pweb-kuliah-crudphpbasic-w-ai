<?php

return [
    'work_start_time' => env('HRIS_WORK_START_TIME', '08:00'),
    'work_end_time' => env('HRIS_WORK_END_TIME', '17:00'),
    'grace_period_minutes' => env('HRIS_GRACE_PERIOD_MINUTES', 15),
    'default_annual_leave_days' => env('HRIS_DEFAULT_ANNUAL_LEAVE_DAYS', 12),
    'default_sick_leave_days' => env('HRIS_DEFAULT_SICK_LEAVE_DAYS', 12),
];