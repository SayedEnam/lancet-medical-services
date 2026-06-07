<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'user_id' => ['nullable', 'exists:users,id'],
            'specialization' => [
                'required',
                'string',
                'max:255',
                Rule::unique('doctors')->where(fn ($q) => $q->where('name', $this->name)),
            ],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'commission_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'visiting_schedule' => ['nullable', 'array'],
            'is_available' => ['boolean'],
        ];
    }
}

