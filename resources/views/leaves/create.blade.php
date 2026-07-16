@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('my.leaves') }}" class="hover:text-gray-700">My Leaves</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900">Request Leave</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Request Leave</h1>
        </div>

        <form action="{{ route('leaves.store') }}" method="POST" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
            @csrf

            <div class="space-y-6">
                {{-- Leave Type --}}
                <div>
                    <label for="leave_type_id" class="block text-sm font-medium text-gray-700">Leave Type</label>
                    <select name="leave_type_id" id="leave_type_id"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            required>
                        <option value="">Select Leave Type</option>
                        @foreach($leaveTypes ?? [] as $type)
                            <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} (Balance: {{ $type->balance ?? 0 }} days)
                            </option>
                        @endforeach
                    </select>
                    @error('leave_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Current Balance Display --}}
                <div x-data="{ selectedType: '{{ old('leave_type_id') }}' }" class="rounded-lg bg-blue-50 p-4">
                    <p class="text-sm text-blue-800">
                        <span class="font-medium">Leave Balance:</span>
                        <span x-text="selectedType ? balances[selectedType] + ' days remaining' : 'Select a leave type to see balance'">Select a leave type to see balance</span>
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Start Date --}}
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                               min="{{ date('Y-m-d') }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                               required>
                        @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                               min="{{ date('Y-m-d') }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                               required>
                        @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Reason --}}
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                    <textarea name="reason" id="reason" rows="4"
                              class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                              placeholder="Please provide a reason for your leave request..."
                              required>{{ old('reason') }}</textarea>
                    @error('reason') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                <a href="{{ route('my.leaves') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">Submit Request</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const balances = {!! json_encode($leaveBalances ?? []) !!};
        window.balances = balances;

        const leaveTypeSelect = document.getElementById('leave_type_id');
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        leaveTypeSelect?.addEventListener('change', function() {
            this.dispatchEvent(new CustomEvent('typechange', { detail: this.value }));
        });

        startDate?.addEventListener('change', function() {
            if (endDate && !endDate.value) {
                endDate.value = this.value;
            }
            if (endDate && endDate.value < this.value) {
                endDate.value = this.value;
            }
        });
    });
</script>
@endpush
@endsection
