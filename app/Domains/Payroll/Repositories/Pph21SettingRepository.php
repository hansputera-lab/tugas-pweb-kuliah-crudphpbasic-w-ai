<?php

namespace App\Domains\Payroll\Repositories;

use App\Domains\Payroll\Models\Pph21Setting;
use App\Domains\Payroll\Models\Pph21TerRate;
use Illuminate\Database\Eloquent\Collection;

class Pph21SettingRepository
{
    public function __construct(
        protected Pph21Setting $model
    ) {}

    public function getForYear(int $year): ?Pph21Setting
    {
        return Pph21Setting::getForYear($year);
    }

    public function getCurrent(): ?Pph21Setting
    {
        return $this->model->active()->orderByDesc('tax_year')->first();
    }

    public function save(array $data): Pph21Setting
    {
        return $this->model->updateOrCreate(
            ['tax_year' => $data['tax_year']],
            $data
        );
    }

    public function getTerRates(int $year, string $category): Collection
    {
        return Pph21TerRate::forYear($year)->category($category)
            ->orderBy('min_income')->get();
    }

    public function upsertTerRates(int $year, string $category, array $rates): void
    {
        Pph21TerRate::forYear($year)->category($category)->delete();
        foreach ($rates as $rate) {
            Pph21TerRate::create(array_merge($rate, [
                'tax_year' => $year,
                'category' => $category,
            ]));
        }
    }
}
