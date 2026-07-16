<?php

namespace App\Domains\Shift\Services;

use App\Domains\Employee\Models\Employee;
use App\Domains\Shift\Models\OvertimeRequest;
use App\Domains\Shift\Repositories\OvertimeRequestRepository;
use App\Models\User;
use Carbon\Carbon;

class OvertimeService
{
    public function __construct(
        protected OvertimeRequestRepository $otRepo
    ) {}

    public function submitRequest(Employee $employee, array $data): OvertimeRequest
    {
        $start = Carbon::parse($data['start_time']);
        $end = Carbon::parse($data['end_time']);
        $hours = round(abs($end->diffInMinutes($start)) / 60, 2);

        if ($hours <= 0) {
            throw new \InvalidArgumentException('Overtime end time must be after start time.');
        }

        $data['hours'] = $hours;

        return $this->otRepo->create($employee, $data);
    }

    public function approve(OvertimeRequest $request, User $approver, ?string $notes): OvertimeRequest
    {
        if (!$request->isPending()) {
            throw new \InvalidArgumentException('Only pending overtime requests can be approved.');
        }
        return $this->otRepo->approve($request, $approver, $notes);
    }

    public function reject(OvertimeRequest $request, User $approver, string $reason): OvertimeRequest
    {
        if (!$request->isPending()) {
            throw new \InvalidArgumentException('Only pending overtime requests can be rejected.');
        }
        return $this->otRepo->reject($request, $approver, $reason);
    }

    public function getPending()
    {
        return $this->otRepo->getPending();
    }

    public function getAll()
    {
        return $this->otRepo->getAll();
    }

    public function getById(int $id): ?OvertimeRequest
    {
        return $this->otRepo->findById($id);
    }

    public function getByEmployee(int $employeeId)
    {
        return $this->otRepo->getByEmployee($employeeId);
    }
}
