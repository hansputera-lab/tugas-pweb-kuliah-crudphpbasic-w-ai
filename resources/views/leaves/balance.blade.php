@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Leave Balances</h1>
            <p class="mt-1 text-sm text-gray-500">View your remaining leave days for the current year</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($balances ?? [] as $balance)
                @php
                    $percentage = $balance->total_days > 0 ? round(($balance->used_days / $balance->total_days) * 100) : 0;
                    $remaining = $balance->total_days - $balance->used_days;
                @endphp
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $balance->leaveType->name }}</h3>
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                            {{ $balance->leaveType->code }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Remaining</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $remaining }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">of {{ $balance->total }} days</p>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Used: {{ $balance->used }} days</span>
                                <span>{{ $percentage }}% used</span>
                            </div>
                            <div class="mt-1 h-3 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full transition-all duration-500
                                    {{ $percentage >= 80 ? 'bg-red-500' : ($percentage >= 50 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="mt-4 space-y-2 border-t border-gray-100 pt-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Total Entitlement</span>
                                <span class="font-medium text-gray-900">{{ $balance->total_days }} days</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Used</span>
                                <span class="font-medium text-gray-900">{{ $balance->used_days }} days</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Available</span>
                                <span class="font-bold text-gray-900">{{ $remaining }} days</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('leaves.create') }}?type={{ $balance->leave_type_id }}" class="block w-full rounded-lg bg-blue-50 px-4 py-2 text-center text-sm font-medium text-blue-700 hover:bg-blue-100">
                            Request {{ $balance->leaveType->name }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 rounded-xl bg-white p-12 text-center shadow-sm ring-1 ring-gray-950/5">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-4 text-sm text-gray-500">No leave balances found</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
