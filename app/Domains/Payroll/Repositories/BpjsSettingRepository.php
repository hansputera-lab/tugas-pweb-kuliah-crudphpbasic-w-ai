<?php

namespace App\Domains\Payroll\Repositories;

use App\Domains\Payroll\Models\BpjsSetting;
use Illuminate\Database\Eloquent\Collection;

class BpjsSettingRepository
{
    public function __construct(
        protected BpjsSetting $model
    ) {}

    public function getActive(): Collection
    {
        return $this->model->active()->get();
    }

    public function getByComponent(string $component): ?BpjsSetting
    {
        return BpjsSetting::getEffective($component);
    }

    public function getActiveByComponent(string $component): Collection
    {
        return $this->model->active()->component($component)->get();
    }

    public function updateOrCreate(array $data): BpjsSetting
    {
        $component = $data['component'];
        unset($data['component']);
        return $this->model->updateOrCreate(
            ['component' => $component, 'effective_date' => $data['effective_date'] ?? now()->format('Y-m-d')],
            $data
        );
    }
}
