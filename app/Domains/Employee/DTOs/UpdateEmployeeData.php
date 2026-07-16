<?php

namespace App\Domains\Employee\DTOs;

readonly class UpdateEmployeeData
{
    public function __construct(
        public ?string $nip = null,
        public ?string $full_name = null,
        public ?string $gender = null,
        public ?string $date_of_birth = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $photo = null,
        public ?string $join_date = null,
        public ?string $status = null,
        public ?int $department_id = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'nip' => $this->nip,
            'full_name' => $this->full_name,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'phone' => $this->phone,
            'address' => $this->address,
            'photo' => $this->photo,
            'join_date' => $this->join_date,
            'status' => $this->status,
            'department_id' => $this->department_id,
        ], fn ($v) => $v !== null);
    }
}
