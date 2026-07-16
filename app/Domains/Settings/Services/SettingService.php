<?php

namespace App\Domains\Settings\Services;

use App\Domains\Settings\Repositories\SettingRepository;

class SettingService
{
    public function __construct(
        protected SettingRepository $settingRepo
    ) {}

    public function get(string $key, $default = null)
    {
        return $this->settingRepo->get($key, $default);
    }

    public function update(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $type = is_int($value) ? 'integer' : (is_bool($value) ? 'boolean' : 'string');
            $this->settingRepo->set($key, $value, $type);
        }
    }

    public function getAll()
    {
        return $this->settingRepo->getAll();
    }

    public function getWorkStartTime(): string
    {
        return $this->settingRepo->getWorkStartTime();
    }

    public function getWorkEndTime(): string
    {
        return $this->settingRepo->getWorkEndTime();
    }

    public function getGracePeriodMinutes(): int
    {
        return $this->settingRepo->getGracePeriodMinutes();
    }
}
