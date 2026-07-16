@php
$employee = $employee ?? null;
@endphp

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    {{-- NIP --}}
    <div>
        <label for="nip" class="block text-sm font-medium text-gray-700">NIP</label>
        <input type="text" name="nip" id="nip" value="{{ old('nip', $employee->nip ?? '') }}"
               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
               required>
        @error('nip') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Full Name --}}
    <div>
        <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
        <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $employee->full_name ?? '') }}"
               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
               required>
        @error('full_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', $employee->user->email ?? '') }}"
               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
               required>
        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Gender --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Gender</label>
        <div class="mt-2 flex gap-4">
            <label class="flex items-center">
                <input type="radio" name="gender" value="L" {{ old('gender', $employee->gender ?? '') === 'L' ? 'checked' : '' }}
                       class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Male</span>
            </label>
            <label class="flex items-center">
                <input type="radio" name="gender" value="P" {{ old('gender', $employee->gender ?? '') === 'P' ? 'checked' : '' }}
                       class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Female</span>
            </label>
        </div>
        @error('gender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Date of Birth --}}
    <div>
        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth ?? '') }}"
               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        @error('date_of_birth') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Phone --}}
    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone ?? '') }}"
               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Address --}}
    <div class="sm:col-span-2">
        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
        <textarea name="address" id="address" rows="3"
                  class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('address', $employee->address ?? '') }}</textarea>
        @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Photo --}}
    <div>
        <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
        <input type="file" name="photo" id="photo" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
        @if($employee->photo ?? null)
            <div class="mt-2">
                <img src="{{ Storage::url($employee->photo) }}" alt="Current photo" class="h-20 w-20 rounded-full object-cover">
            </div>
        @endif
        @error('photo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Join Date --}}
    <div>
        <label for="join_date" class="block text-sm font-medium text-gray-700">Join Date</label>
        <input type="date" name="join_date" id="join_date" value="{{ old('join_date', $employee->join_date ?? '') }}"
               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
               required>
        @error('join_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Status --}}
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" id="status"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            <option value="active" {{ old('status', $employee->status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $employee->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Department --}}
    <div>
        <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
        <select name="department_id" id="department_id"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                required>
            <option value="">Select Department</option>
            @foreach($departments ?? [] as $dept)
                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
        @error('department_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Position --}}
    <div>
        <label for="position_id" class="block text-sm font-medium text-gray-700">Position</label>
        <select name="position_id" id="position_id"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                required>
            <option value="">Select Position</option>
            @foreach($positions ?? [] as $pos)
                <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id ?? '') == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
            @endforeach
        </select>
        @error('position_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Manager --}}
    <div>
        <label for="manager_id" class="block text-sm font-medium text-gray-700">Reports To (Manager)</label>
        <select name="manager_id" id="manager_id"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            <option value="">No Manager</option>
            @foreach($managers ?? [] as $mgr)
                @continue($employee && $employee->id === $mgr->id)
                <option value="{{ $mgr->id }}" {{ old('manager_id', $employee->manager_id ?? '') == $mgr->id ? 'selected' : '' }}>{{ $mgr->full_name }} ({{ $mgr->nip }})</option>
            @endforeach
        </select>
        @error('manager_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
