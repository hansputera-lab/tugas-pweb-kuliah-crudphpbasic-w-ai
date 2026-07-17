<?php

namespace App\Http\Controllers;

use App\Domains\Settings\Repositories\SettingRepository;
use App\Domains\Settings\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct(
        protected SettingService $settingService,
        protected SettingRepository $settingRepo
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
            'logo_light' => 'nullable|image|mimes:png,svg,jpg,jpeg|max:2048',
            'logo_dark' => 'nullable|image|mimes:png,svg,jpg,jpeg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,svg|max:1024',
        ]);

        foreach (['logo_light', 'logo_dark', 'favicon'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = $field . '.' . $file->extension();
                $path = $file->storeAs('logo', $filename, 'public');
                $validated[$field] = $path;
            }
        }

        $this->settingService->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    public function removeLogo(string $type)
    {
        $path = $this->settingRepo->get("logo_{$type}");
        if ($path) {
            Storage::disk('public')->delete($path);
            $this->settingRepo->set("logo_{$type}", null, 'string');
        }
        return back()->with('success', 'Logo removed.');
    }
}
