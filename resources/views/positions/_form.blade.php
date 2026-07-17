@php
$position = $position ?? null;
@endphp

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    {{-- Code --}}
    <div>
        <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
        <input type="text" name="code" id="code" value="{{ old('code', $position->code ?? '') }}"
               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
               placeholder="e.g. MGR, ENG, ADM"
               required maxlength="20">
        @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Name --}}
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input type="text" name="name" id="name" value="{{ old('name', $position->name ?? '') }}"
               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
               placeholder="e.g. Manager, Engineer"
               required maxlength="100">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Department --}}
    <div>
        <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
        <select name="department_id" id="department_id"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                required>
            <option value="">Select Department</option>
            @foreach($departments ?? [] as $dept)
                <option value="{{ $dept->id }}" {{ old('department_id', $position->department_id ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
        @error('department_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Base Salary --}}
    <div>
        <label for="base_salary" class="block text-sm font-medium text-gray-700">Base Salary (IDR)</label>
        <input type="text" name="base_salary" id="base_salary" value="{{ old('base_salary', $position->base_salary ?? '') }}"
               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
               placeholder="e.g. 5.000.000"
               data-currency
               required>
        @error('base_salary') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Level --}}
    <div>
        <label for="level" class="block text-sm font-medium text-gray-700">Level</label>
        <select name="level" id="level"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                required>
            @for($i = 1; $i <= 10; $i++)
                <option value="{{ $i }}" {{ old('level', $position->level ?? '') == $i ? 'selected' : '' }}>Level {{ $i }}</option>
            @endfor
        </select>
        @error('level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Description --}}
    <div class="sm:col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="description" rows="3"
                  class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                  placeholder="Position description...">{{ old('description', $position->description ?? '') }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
