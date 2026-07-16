@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('payroll.index') }}" class="hover:text-gray-700">Payroll</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('payroll.show', $item->payroll_period_id) }}" class="hover:text-gray-700">{{ $item->period->label }}</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">Documents — {{ $item->employee->full_name }}</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Payroll Documents</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $item->employee->full_name }} · {{ $item->period->label }}</p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Upload form --}}
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="text-base font-semibold text-gray-900">Upload Document</h2>
                <form action="{{ route('payroll.documents.upload', $item) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Document Name</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="file_type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="file_type" id="file_type" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="payslip">Payslip</option>
                            <option value="tax_form">Tax Form</option>
                            <option value="supporting_doc">Supporting Document</option>
                            <option value="contract">Contract</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700">File (max 10MB)</label>
                        <input type="file" name="file" id="file"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100"
                               required>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                        Upload
                    </button>
                </form>
            </div>

            {{-- Document list --}}
            <div class="lg:col-span-2">
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h2 class="text-base font-semibold text-gray-900">{{ $documents->count() }} Documents</h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($documents as $doc)
                            <div class="flex items-center justify-between px-5 py-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <svg class="h-8 w-8 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                    </svg>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $doc->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ ucfirst(str_replace('_', ' ', $doc->file_type)) }}
                                            @if($doc->file_size)
                                                · {{ $doc->file_size }}
                                            @endif
                                            · {{ $doc->created_at->format('d M Y') }}
                                            @if($doc->uploadedBy)
                                                · by {{ $doc->uploadedBy->name }}
                                            @endif
                                        </p>
                                        @if($doc->notes)
                                            <p class="mt-1 text-xs text-gray-400">{{ $doc->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                                    <a href="{{ $doc->download_url }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                        Download
                                    </a>
                                    <form action="{{ route('payroll.documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Delete this document?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-10 text-center text-sm text-gray-500">
                                No documents uploaded yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
