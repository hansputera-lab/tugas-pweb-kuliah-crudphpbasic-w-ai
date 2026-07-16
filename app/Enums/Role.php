<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'super_admin';
    case HRManager = 'hr_manager';
    case Employee = 'employee';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::HRManager => 'HR Manager',
            self::Employee => 'Employee',
        };
    }

    public static function options(): array
    {
        return array_column(self::cases(), 'label', 'value');
    }
}
