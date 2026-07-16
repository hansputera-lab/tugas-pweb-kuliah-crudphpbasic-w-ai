@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('positions.index') }}" class="hover:text-gray-700">Positions</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900">Create Position</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Create Position</h1>
        </div>

        <form action="{{ route('positions.store') }}" method="POST" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
            @csrf
            @include('positions._form')

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                <a href="{{ route('positions.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">Create Position</button>
            </div>
        </form>
    </div>
</div>
@endsection
