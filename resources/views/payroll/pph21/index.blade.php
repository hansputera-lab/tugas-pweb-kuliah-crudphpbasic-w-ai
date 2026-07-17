@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('payroll.index') }}" class="hover:text-gray-700">Payroll</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">PPh 21 Settings</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">PPh 21 (Income Tax) Settings</h1>
            <p class="mt-1 text-sm text-gray-500">Configure PTKP, progressive tax brackets, and employment cost deductions for PPh 21 calculation.</p>
            <div class="mt-4 rounded-lg bg-blue-50 p-4 text-sm text-blue-700">
                <p class="font-medium">About PPh 21 Calculation</p>
                <p class="mt-1">The system uses the <strong>TER (Tarif Efektif Rata-rata)</strong> method per PMK 168/2023 for monthly calculations, with a Pasal 17 progressive check at year-end. TER categories map to tax status:</p>
                <ul class="mt-1 list-inside list-disc space-y-0.5">
                    <li><strong>Category A</strong> — TK/0, TK/1, K/0</li>
                    <li><strong>Category B</strong> — TK/2, TK/3, K/1, K/2</li>
                    <li><strong>Category C</strong> — K/3</li>
                </ul>
                <p class="mt-1">Net salary = Gross − Biaya Jabatan − BPJS Employee − Pension − PPh 21</p>
            </div>
        </div>

        <form method="POST" action="{{ route('payroll.pph21.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                    <h2 class="mb-4 text-base font-semibold text-gray-900">PTKP (Non-Taxable Income)</h2>
                    <p class="mb-3 text-xs text-gray-500">Annual non-taxable income thresholds based on marital status and dependents. TK = Tidak Kawin, K = Kawin.</p>
                    <div class="space-y-3">
                        @foreach(['ptkp_tk0', 'ptkp_tk1', 'ptkp_tk2', 'ptkp_tk3', 'ptkp_k0', 'ptkp_k1', 'ptkp_k2', 'ptkp_k3'] as $field)
                            <div>
                                <label class="block text-xs font-medium text-gray-500">{{ strtoupper(preg_replace('/(ptkp_|_)/', ' ', $field)) }}</label>
                                <input type="text" name="{{ $field }}" value="{{ old($field, $setting->$field) }}" data-currency
                                       class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                    <h2 class="mb-4 text-base font-semibold text-gray-900">Progressive Tax Brackets (Pasal 17)</h2>
                    <p class="mb-3 text-xs text-gray-500">Progressive rates applied to PKP (Penghasilan Kena Pajak) for year-end true-up calculation.</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Layer 1 Rate (%) – Up to Rp 60M</label>
                            <input type="number" step="0.01" name="tarif_layer1" value="{{ old('tarif_layer1', $setting->tarif_layer1) }}" required min="0" max="100"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Layer 2 Rate (%) – Rp 60M – Rp {{ number_format($setting->tarif_batas2) }}</label>
                            <input type="number" step="0.01" name="tarif_layer2" value="{{ old('tarif_layer2', $setting->tarif_layer2) }}" required min="0" max="100"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Layer 3 Rate (%) – Rp {{ number_format($setting->tarif_batas2) }} – Rp {{ number_format($setting->tarif_batas3) }}</label>
                            <input type="number" step="0.01" name="tarif_layer3" value="{{ old('tarif_layer3', $setting->tarif_layer3) }}" required min="0" max="100"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Layer 4 Rate (%) – Rp {{ number_format($setting->tarif_batas3) }} – Rp {{ number_format($setting->tarif_batas4) }}</label>
                            <input type="number" step="0.01" name="tarif_layer4" value="{{ old('tarif_layer4', $setting->tarif_layer4) }}" required min="0" max="100"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Layer 5 Rate (%) – Above Rp {{ number_format($setting->tarif_batas4) }}</label>
                            <input type="number" step="0.01" name="tarif_layer5" value="{{ old('tarif_layer5', $setting->tarif_layer5) }}" required min="0" max="100"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="mb-4 text-base font-semibold text-gray-900">Deductions & DTP</h2>
                <p class="mb-3 text-xs text-gray-500">Biaya Jabatan = employment cost (5% of gross, capped). DTP = Government-borne tax for salaries ≤ threshold. Non-NPWP = 20% higher rate.</p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Biaya Jabatan (%)</label>
                        <input type="number" step="0.01" name="biaya_jabatan_persen" value="{{ old('biaya_jabatan_persen', $setting->biaya_jabatan_persen) }}" required min="0" max="100"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Biaya Jabatan Max/Month</label>
                        <input type="text" name="biaya_jabatan_max_bulan" value="{{ old('biaya_jabatan_max_bulan', $setting->biaya_jabatan_max_bulan) }}" data-currency
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Biaya Jabatan Max/Year</label>
                        <input type="text" name="biaya_jabatan_max_tahun" value="{{ old('biaya_jabatan_max_tahun', $setting->biaya_jabatan_max_tahun) }}" data-currency
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Non-NPWP Multiplier</label>
                        <input type="number" step="0.01" name="non_npwp_multiplier" value="{{ old('non_npwp_multiplier', $setting->non_npwp_multiplier) }}" required min="1"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">DTP Max Gaji (0=disabled)</label>
                        <input type="text" name="dtp_max_gaji" value="{{ old('dtp_max_gaji', $setting->dtp_max_gaji) }}" data-currency
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Save Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection
