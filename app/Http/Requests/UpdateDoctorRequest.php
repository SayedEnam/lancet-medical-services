<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('doctor');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'user_id' => ['nullable', 'exists:users,id'],
            'specialization' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('doctors')
                    ->ignore($id)
                    ->where(fn ($q) => $q->where('name', $this->input('name'))),
            ],
            'consultation_fee' => ['sometimes', 'numeric', 'min:0'],
            'commission_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'visiting_schedule' => ['nullable', 'array'],
            'is_available' => ['boolean'],
        ];
    }
}

