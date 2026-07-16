<?php

namespace App\Http\Controllers;

use App\Domains\Settings\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    public function index()
    {
        $settingsCollection = $this->settingService->getAll();
        $settings = $settingsCollection->pluck('value', 'key')->toArray();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i|after:work_start_time',
            'grace_period_minutes' => 'required|integer|min:0|max:60',
            'company_name' => 'required|string|max:255',
            'default_annual_leave_days' => 'required|integer|min:0|max:30',
            'default_sick_leave_days' => 'required|integer|min:0|max:30',
            'payroll_working_days' => 'required|integer|min:1|max:31',
            'payroll_late_deduction_rate' => 'required|numeric|min:0|max:1',
            'payroll_absent_deduction_rate' => 'required|numeric|min:0|max:2',
            'payroll_ot_hourly_multiplier' => 'required|numeric|min:1|max:5',
            'kpi_grade_a_min' => 'required|integer|min:0|max:100',
            'kpi_grade_b_min' => 'required|integer|min:0|max:100',
            'kpi_grade_c_min' => 'required|integer|min:0|max:100',
            'kpi_grade_d_min' => 'required|integer|min:0|max:100',
        ]);

        $this->settingService->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
