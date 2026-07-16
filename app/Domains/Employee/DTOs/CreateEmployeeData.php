<?php

namespace App\Domains\Employee\DTOs;

readonly class CreateEmployeeData
{
    public function __construct(
        public int $user_id,
        public string $nip,
        public string $full_name,
        public string $gender,
        public string $date_of_birth,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $photo = null,
        public string $join_date,
        public string $status = 'active',
        public int $department_id,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
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
        ];
    }
}
