@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('overtime.index') }}" class="hover:text-gray-700">Overtime</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">{{ $request->employee->full_name ?? '' }}</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Overtime Request</h1>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500">Employee</dt><dd class="font-medium text-gray-900">{{ $request->employee->full_name ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Date</dt><dd class="font-medium text-gray-900">{{ $request->date->format('d M Y') }}</dd></div>
                <div><dt class="text-gray-500">Time</dt><dd class="font-medium text-gray-900">{{ substr($request->start_time,0,5) }} - {{ substr($request->end_time,0,5) }}</dd></div>
                <div><dt class="text-gray-500">Hours</dt><dd class="font-medium text-gray-900">{{ number_format($request->hours, 1) }}</dd></div>
                @if($request->reason)
                    <div class="col-span-2"><dt class="text-gray-500">Reason</dt><dd class="font-medium text-gray-900">{{ $request->reason }}</dd></div>
                @endif
                @if($request->rejection_reason)
                    <div class="col-span-2"><dt class="text-gray-500">Rejection Reason</dt><dd class="font-medium text-red-600">{{ $request->rejection_reason }}</dd></div>
                @endif
                @if($request->approval_notes)
                    <div class="col-span-2"><dt class="text-gray-500">Approval Notes</dt><dd class="font-medium text-green-600">{{ $request->approval_notes }}</dd></div>
                @endif
                <div class="col-span-2">
                    <dt class="text-gray-500">Status</dt>
                    <dd>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $request->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($request->status) }}
                        </span>
                    </dd>
                </div>
            </dl>

            @if($request->isPending() && auth()->user()->canManageHR())
                <div class="mt-6 flex gap-3 border-t border-gray-100 pt-4">
                    <form action="{{ route('overtime.approve', $request) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PUT')
                        <input type="text" name="notes" placeholder="Approval note (optional)" class="mb-2 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <button type="submit" class="w-full rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-500">Approve</button>
                    </form>
                    <form action="{{ route('overtime.reject', $request) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PUT')
                        <input type="text" name="rejection_reason" placeholder="Rejection reason" required class="mb-2 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">Reject</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
