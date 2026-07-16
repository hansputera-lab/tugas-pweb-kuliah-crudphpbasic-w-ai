<?php

namespace App\Domains\ActivityLog\Repositories;

use App\Domains\ActivityLog\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLogRepository
{
    public function __construct(
        protected ActivityLog $model
    ) {}

    public function log(
        ?User $user,
        string $action,
        Model $subject,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): ActivityLog {
        return $this->model->create([
            'user_id' => $user?->id,
            'action' => $action,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    public function getForSubject(Model $subject): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('subject_type', get_class($subject))
            ->where('subject_id', $subject->getKey())
            ->with('user')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getRecent(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->with('user')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
