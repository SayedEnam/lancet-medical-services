<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'exists:users,id'],
            'designation' => ['sometimes', 'string', 'max:255'],
            'salary' => ['sometimes', 'numeric', 'min:0'],
            'joining_date' => ['nullable', 'date'],
            'employment_status' => ['sometimes', 'in:active,inactive,on_leave'],
        ];
    }
}
