@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('reimbursements.index') }}" class="hover:text-gray-700">Reimbursement</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">{{ $claim->title }}</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Claim Detail</h1>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $claim->title }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $claim->employee->full_name ?? '-' }} &middot; {{ $claim->employee->department->name ?? '' }}</p>
                </div>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                    {{ $claim->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $claim->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $claim->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst($claim->status) }}
                </span>
            </div>

            <dl class="mt-6 grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500">Category</dt><dd class="font-medium text-gray-900">{{ $claim->category->name ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Amount</dt><dd class="font-medium text-gray-900 tabular-nums">{{ currency($claim->amount) }}</dd></div>
                <div><dt class="text-gray-500">Expense Date</dt><dd class="font-medium text-gray-900">{{ $claim->expense_date->format('d M Y') }}</dd></div>
                <div><dt class="text-gray-500">Approval Progress</dt><dd class="font-medium text-gray-900">{{ $claim->current_approval_level }}/{{ $claim->total_approval_levels }}</dd></div>
                @if($claim->description)
                    <div class="col-span-2"><dt class="text-gray-500">Description</dt><dd class="font-medium text-gray-900">{{ $claim->description }}</dd></div>
                @endif
                @if($claim->rejected_reason)
                    <div class="col-span-2"><dt class="text-gray-500">Rejection Reason</dt><dd class="font-medium text-red-600">{{ $claim->rejected_reason }}</dd></div>
                @endif
            </dl>

            @if($claim->receipt_path)
                <div class="mt-6">
                    <p class="text-sm font-medium text-gray-700">Receipt</p>
                    <a href="{{ $claim->receipt_url }}" target="_blank" class="mt-2 inline-block rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-indigo-600 hover:bg-gray-50">View Receipt</a>
                </div>
            @endif

            {{-- Approval history --}}
            <div class="mt-6 border-t border-gray-100 pt-4">
                <h3 class="text-sm font-semibold text-gray-900">Approval History</h3>
                <div class="mt-3 space-y-2">
                    @forelse($claim->approvals as $approval)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 text-sm">
                            <span class="font-medium text-gray-900">Level {{ $approval->level }} &middot; {{ $approval->approver->name ?? 'System' }}</span>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $approval->action === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($approval->action) }}
                            </span>
                        </div>
                        @if($approval->notes)
                            <p class="text-xs text-gray-500">{{ $approval->notes }}</p>
                        @endif
                    @empty
                        <p class="text-sm text-gray-400">No approvals yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- HR actions --}}
            @if($claim->isPending() && auth()->user()->canManageHR())
                <div class="mt-6 flex gap-3 border-t border-gray-100 pt-4">
                    <form action="{{ route('reimbursements.approve', $claim) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PUT')
                        <input type="text" name="notes" placeholder="Approval note (optional)" class="mb-2 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <button type="submit" class="w-full rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-500">
                            Approve Level {{ $claim->current_approval_level }}
                        </button>
                    </form>
                    <form action="{{ route('reimbursements.reject', $claim) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PUT')
                        <input type="text" name="rejected_reason" placeholder="Rejection reason" required class="mb-2 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <input type="text" name="notes" placeholder="Note (optional)" class="mb-2 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">Reject</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
