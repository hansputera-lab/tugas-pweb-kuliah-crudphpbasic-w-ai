@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('payroll.index') }}" class="hover:text-gray-700">Payroll</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">Run Payroll</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Payroll Run</h1>
            <p class="mt-1 text-sm text-gray-500">Select a period, preview calculations, and execute the payroll run.</p>
        </div>

        {{-- Period Selector --}}
        <form method="GET" action="{{ route('payroll.run.preview') }}" class="mb-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
            <div class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Payroll Period</label>
                    <select name="period_id" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @foreach($periods as $p)
                            <option value="{{ $p->id }}" {{ ($currentPeriod->id ?? $period->id ?? null) == $p->id ? 'selected' : '' }}>
                                {{ $p->period_name }} {{ $p->is_active ? '(Active)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Preview</button>
                    @can('payroll.run')
                        <button type="submit" formaction="{{ route('payroll.run.execute') }}" formmethod="POST"
                                onclick="return confirm('Run payroll for the selected period? This will recalculate all BPJS and PPh 21.')"
                                class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500">
                            @csrf
                            Run Payroll
                        </button>
                    @endcan
                </div>
            </div>
        </form>

        @if(isset($results) && count($results))
            {{-- Results Table --}}
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-base font-semibold text-gray-900">Preview: {{ $period->period_name }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-500">Employee</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-500">Gaji Pokok</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-500">BPJS (Emp)</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-500">BPJS (Er)</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-500">PPh 21</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-500">Net Salary</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($results as $r)
                                @php
                                    $bpjs = $r['bpjs'];
                                    $pph21 = $r['pph21'];
                                    $item = $r['payrollItem'];
                                    $gaji = $item->gaji_pokok ?? 0;
                                    $net = $gaji + ($item->total_allowances ?? 0) - ($item->total_deductions ?? 0)
                                        - $bpjs->getTotalEmployee() - ($pph21?->pph21PerBulan ?? 0);
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 text-gray-900">{{ $r['employee']->user?->name }}</td>
                                    <td class="px-3 py-2 text-right text-gray-900">{{ number_format($gaji, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600">{{ number_format($bpjs->getTotalEmployee(), 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600">{{ number_format($bpjs->getTotalEmployer(), 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right text-red-600">{{ number_format($pph21?->pph21PerBulan ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-gray-900">{{ number_format(max(0, $net), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif(isset($runDetails) && $runDetails->isNotEmpty())
            {{-- Already Run --}}
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-base font-semibold text-gray-900">Run Results: {{ $currentPeriod->period_name ?? '' }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-500">Employee</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-500">Gaji Pokok</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-500">Allowances</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-500">Deductions</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-500">BPJS (Emp)</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-500">BPJS (Er)</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-500">PPh 21</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-500">Net</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($runDetails as $d)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 text-gray-900">{{ $d->employee?->user?->name }}</td>
                                    <td class="px-3 py-2 text-right text-gray-900">{{ number_format($d->gaji_pokok, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600">{{ number_format($d->total_allowances, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600">{{ number_format($d->total_deductions, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600">{{ number_format($d->employee_bpjs, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600">{{ number_format($d->employer_bpjs, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right text-red-600">{{ number_format($d->pph21_amount, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-gray-900">{{ number_format($d->net_salary, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if($totals)
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Totals</th>
                                    <th class="px-3 py-2"></th>
                                    <th class="px-3 py-2"></th>
                                    <th class="px-3 py-2"></th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-700">{{ number_format($totals['employee_bpjs'], 0, ',', '.') }}</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-700">{{ number_format($totals['employer_bpjs'], 0, ',', '.') }}</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-red-700">{{ number_format($totals['pph21'], 0, ',', '.') }}</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-900">{{ number_format($totals['net_salary'], 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        @else
            <div class="rounded-xl bg-white p-10 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-center text-sm text-gray-500">Select a period and click Preview to see the calculation results before running payroll.</p>
            </div>
        @endif
    </div>
</div>
@endsection
