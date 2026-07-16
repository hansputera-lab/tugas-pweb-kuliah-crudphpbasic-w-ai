@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('reimbursements.index') }}" class="hover:text-gray-700">Reimbursement</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">Categories</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Expense Categories</h1>
            <p class="mt-1 text-sm text-gray-500">Configure expense categories and approval levels (customizable per category).</p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="text-base font-semibold text-gray-900">Add Category</h2>
                <form action="{{ route('reimbursements.categories.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" required placeholder="e.g. Travel"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="2" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Approval Levels</label>
                        <select name="approval_levels" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="1">1 - Single approval</option>
                            <option value="2" selected>2 - Two-level approval</option>
                            <option value="3">3 - Three-level approval</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="requires_receipt" value="1" checked class="h-4 w-4 rounded border-gray-300 text-blue-600"> Requires receipt
                    </label>
                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Add Category</button>
                </form>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 lg:col-span-2">
                <h2 class="text-base font-semibold text-gray-900">Categories</h2>
                <div class="mt-4 space-y-3">
                    @forelse($categories as $category)
                        <form action="{{ route('reimbursements.categories.update', $category) }}" method="POST" class="flex flex-wrap items-end gap-3 rounded-lg border border-gray-100 p-3">
                            @csrf
                            @method('PUT')
                            <div class="w-40">
                                <label class="text-xs font-medium text-gray-500">Name</label>
                                <input type="text" name="name" value="{{ $category->name }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">Levels</label>
                                <select name="approval_levels" class="mt-1 block rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="1" {{ $category->approval_levels == 1 ? 'selected' : '' }}>1</option>
                                    <option value="2" {{ $category->approval_levels == 2 ? 'selected' : '' }}>2</option>
                                    <option value="3" {{ $category->approval_levels == 3 ? 'selected' : '' }}>3</option>
                                </select>
                            </div>
                            <label class="flex items-center gap-1 text-xs text-gray-600">
                                <input type="checkbox" name="requires_receipt" value="1" {{ $category->requires_receipt ? 'checked' : '' }}> Receipt
                            </label>
                            <label class="flex items-center gap-1 text-xs text-gray-600">
                                <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }}> Active
                            </label>
                            <div class="flex gap-1">
                                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-500">Save</button>
                                <form action="{{ route('reimbursements.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-500">Del</button>
                                </form>
                            </div>
                        </form>
                    @empty
                        <p class="py-8 text-center text-sm text-gray-500">No categories yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
