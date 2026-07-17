<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Domains\Payroll\Repositories\BpjsSettingRepository;
use App\Domains\Payroll\Models\BpjsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BpjsSettingController extends Controller
{
    public function __construct(
        protected BpjsSettingRepository $bpjsSettingRepo
    ) {}

    public function index()
    {
        Gate::authorize('bpjs.view');

        $settings = BpjsSetting::where('is_active', true)
            ->orderBy('component')
            ->orderByRaw('risk_level IS NULL, risk_level')
            ->get()
            ->groupBy('component');

        return view('payroll.bpjs.index', compact('settings'));
    }

    public function update(Request $request, string $component)
    {
        Gate::authorize('bpjs.configure');

        $validated = $request->validate([
            'rate_employer' => 'required|numeric|min:0|max:100',
            'rate_employee' => 'required|numeric|min:0|max:100',
            'max_wage' => 'nullable|numeric|min:0',
            'min_wage' => 'nullable|numeric|min:0',
            'risk_level' => 'nullable|string|in:low,medium,high,very_high',
        ]);

        $query = ['component' => $component, 'is_active' => true];
        if ($request->risk_level) {
            $query['risk_level'] = $request->risk_level;
        }

        $setting = BpjsSetting::where($query)->firstOrFail();
        $setting->update($validated);

        return redirect()->route('payroll.bpjs.settings')
            ->with('success', 'BPJS setting updated successfully.');
    }
}
