<?php

namespace App\Domains\Settings\Repositories;

use App\Domains\Settings\Models\Setting;

class SettingRepository
{
    public function __construct(
        protected Setting $model
    ) {}

    public function get(string $key, $default = null)
    {
        $setting = $this->model->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public function set(string $key, $value, string $type = 'string'): Setting
    {
        return $this->model->updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'type' => $type]
        );
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->orderBy('key')->get();
    }

    public function getWorkStartTime(): string
    {
        return (string) $this->get('work_start_time', config('hris.work_start_time', '08:00'));
    }

    public function getWorkEndTime(): string
    {
        return (string) $this->get('work_end_time', config('hris.work_end_time', '17:00'));
    }

    public function getGracePeriodMinutes(): int
    {
        return (int) $this->get('grace_period_minutes', config('hris.grace_period_minutes', 15));
    }
}
