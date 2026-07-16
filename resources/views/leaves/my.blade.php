@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Leave Requests</h1>
                <p class="mt-1 text-sm text-gray-500">View and manage your leave requests</p>
            </div>
            <a href="{{ route('leaves.create') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Request Leave
            </a>
        </div>

        {{-- Summary --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-sm text-gray-500">Total Requests</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalRequests ?? 0 }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="mt-1 text-2xl font-bold text-yellow-600">{{ $pendingCount ?? 0 }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-sm text-gray-500">Approved</p>
                <p class="mt-1 text-2xl font-bold text-green-600">{{ $approvedCount ?? 0 }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-sm text-gray-500">Rejected</p>
                <p class="mt-1 text-2xl font-bold text-red-600">{{ $rejectedCount ?? 0 }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Start Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">End Date</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Days</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($leaveRequests ?? [] as $leave)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $leave->leaveType->name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-gray-500">{{ $leave->total_days }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $leave->reason ?? '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $leave->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $leave->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($leave->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('leaves.show', $leave) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                        @if($leave->status === 'pending')
                                            <form action="{{ route('leaves.destroy', $leave) }}" method="POST" onsubmit="return confirm('Cancel this leave request?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No leave requests found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($leaveRequests) && $leaveRequests instanceof \Illuminate\Pagination\LengthAwarePaginator && $leaveRequests->hasPages())
                <div class="border-t border-gray-200 bg-white px-6 py-3">
                    {{ $leaveRequests->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
