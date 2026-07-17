<?php

namespace App\Domains\Payroll\DTOs;

class BpjsResult
{
    public function __construct(
        public readonly array $employerContributions = [],
        public readonly array $employeeContributions = [],
        public readonly float $totalEmployer = 0,
        public readonly float $totalEmployee = 0,
    ) {}

    public function getTotalEmployee(): float
    {
        return $this->totalEmployee;
    }

    public function getTotalEmployer(): float
    {
        return $this->totalEmployer;
    }

    public function toArray(): array
    {
        return [
            'employer_contributions' => $this->employerContributions,
            'employee_contributions' => $this->employeeContributions,
            'total_employer' => $this->totalEmployer,
            'total_employee' => $this->totalEmployee,
        ];
    }
}
