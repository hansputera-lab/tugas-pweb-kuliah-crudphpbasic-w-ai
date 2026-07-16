<?php

namespace App\Domains\ActivityLog\Services;

use App\Domains\ActivityLog\Repositories\ActivityLogRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public function __construct(
        protected ActivityLogRepository $activityLogRepo
    ) {}

    public function log(
        ?User $user,
        string $action,
        Model $subject,
        ?array $oldValues = null,
        ?array $newValues = null,
    ) {
        return $this->activityLogRepo->log($user, $action, $subject, $oldValues, $newValues);
    }

    public function getForSubject(Model $subject)
    {
        return $this->activityLogRepo->getForSubject($subject);
    }

    public function getRecent(int $limit = 50)
    {
        return $this->activityLogRepo->getRecent($limit);
    }
}
