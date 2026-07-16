@extends('layouts.app')

@section('content')
@if(!$leave)
    <div class="py-6">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-12 text-center shadow-sm ring-1 ring-gray-950/5">
                <p class="text-gray-500">Leave request not found.</p>
                <a href="{{ route('my.leaves') }}" class="mt-4 inline-block text-sm font-medium text-blue-600 hover:text-blue-500">&larr; Back to My Leaves</a>
            </div>
        </div>
    </div>
@else
<div class="py-6">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('my.leaves') }}" class="hover:text-gray-700">My Leaves</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900">Leave Detail</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Leave Request Detail</h1>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $leave->leaveType->name ?? '-' }} Leave</h2>
                    <p class="text-sm text-gray-500">Requested by {{ $leave->employee->full_name ?? '-' }}</p>
                </div>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                    {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $leave->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $leave->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst($leave->status) }}
                </span>
            </div>

            {{-- Details --}}
            <div class="mt-6 space-y-4">
                <div class="flex justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">Leave Type</span>
                    <span class="text-sm font-medium text-gray-900">{{ $leave->leaveType->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">Start Date</span>
                    <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">End Date</span>
                    <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">Duration</span>
                    <span class="text-sm font-medium text-gray-900">{{ $leave->total_days }} day(s)</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">Submitted</span>
                    <span class="text-sm font-medium text-gray-900">{{ $leave->created_at ? $leave->created_at->format('d M Y H:i') : '-' }}</span>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Reason</span>
                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-lg p-3">{{ $leave->reason ?? '-' }}</p>
                </div>

                @if($leave->status !== 'pending' && $leave->reviewed_by)
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">{{ $leave->status === 'approved' ? 'Approved' : 'Rejected' }} by</span>
                        <span class="text-sm font-medium text-gray-900">{{ $leave->reviewer->name ?? '-' }}</span>
                    </div>
                @endif

                @if($leave->status !== 'pending' && $leave->reviewed_at)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">{{ $leave->status === 'approved' ? 'Approved' : 'Rejected' }} at</span>
                        <span class="text-sm font-medium text-gray-900">{{ $leave->reviewed_at ? \Carbon\Carbon::parse($leave->reviewed_at)->format('d M Y H:i') : '-' }}</span>
                    </div>
                @endif
            </div>

            {{-- Approve/Reject Form (HR Only for Pending) --}}
            @if(in_array(Auth::user()->role, ['super_admin', 'hr_manager']) && $leave->status === 'pending')
                <div class="mt-8 border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-4">Action</h3>
                    <div class="flex gap-3">
                        <form action="{{ route('leaves.approve', $leave) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="w-full rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                                Approve
                            </button>
                        </form>
                        <form action="{{ route('leaves.reject', $leave) }}" method="POST" class="flex-1" x-data="{ show: false }">
                            @csrf
                            @method('PUT')
                            <button type="button" @click="show = !show" class="w-full rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                                Reject
                            </button>
                            <div x-show="show" x-transition class="mt-3">
                                <textarea name="rejection_reason" rows="2" placeholder="Reason for rejection..."
                                          class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"></textarea>
                                <button type="submit" class="mt-2 w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                                    Confirm Rejection
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-4">
            <a href="{{ route('my.leaves') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">&larr; Back to My Leaves</a>
        </div>
    </div>
</div>
@endif
@endsection
