<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Domains\Payroll\Repositories\Pph21SettingRepository;
use App\Domains\Payroll\Models\Pph21Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class Pph21SettingController extends Controller
{
    public function __construct(
        protected Pph21SettingRepository $pph21SettingRepo
    ) {}

    public function index()
    {
        Gate::authorize('pph21.view');

        $setting = Pph21Setting::where('is_active', true)
            ->orderByDesc('tax_year')
            ->where('is_active', true)
            ->orderByDesc('tax_year')
            ->first();

        if (!$setting) {
            $setting = new Pph21Setting();
        }

        return view('payroll.pph21.index', compact('setting'));
    }

    public function update(Request $request)
    {
        Gate::authorize('pph21.configure');

        $validated = $request->validate([
            'ptkp_tk0' => 'required|numeric|min:0',
            'ptkp_tk1' => 'required|numeric|min:0',
            'ptkp_tk2' => 'required|numeric|min:0',
            'ptkp_tk3' => 'required|numeric|min:0',
            'ptkp_k0' => 'required|numeric|min:0',
            'ptkp_k1' => 'required|numeric|min:0',
            'ptkp_k2' => 'required|numeric|min:0',
            'ptkp_k3' => 'required|numeric|min:0',
            'tarif_layer1' => 'required|numeric|min:0|max:100',
            'tarif_layer2' => 'required|numeric|min:0|max:100',
            'tarif_layer3' => 'required|numeric|min:0|max:100',
            'tarif_layer4' => 'required|numeric|min:0|max:100',
            'tarif_layer5' => 'required|numeric|min:0|max:100',
            'biaya_jabatan_persen' => 'required|numeric|min:0|max:100',
            'biaya_jabatan_max_bulan' => 'required|numeric|min:0',
            'biaya_jabatan_max_tahun' => 'required|numeric|min:0',
            'non_npwp_multiplier' => 'required|numeric|min:1',
            'dtp_max_gaji' => 'required|numeric|min:0',
        ]);

        $setting = Pph21Setting::where('is_active', true)
            ->orderByDesc('tax_year')
            ->first();

        if (!$setting) {
            $setting = Pph21Setting::create([
                'tax_year' => now()->year,
                'is_active' => true,
                'effective_date' => now()->startOfYear(),
            ]);
        }

        $setting->update($validated);

        return redirect()->route('payroll.pph21.settings')
            ->with('success', 'PPh 21 settings updated.');
    }
}
