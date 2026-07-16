<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $positionId = $this->route('position')?->id;

        return [
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', 'unique:positions,code,' . $positionId],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'default_annual_leave_days' => ['nullable', 'integer', 'min:0'],
            'default_sick_leave_days' => ['nullable', 'integer', 'min:0'],
            'level' => ['required', 'integer', 'min:1'],
        ];
    }
}
