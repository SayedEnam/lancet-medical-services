<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'user_id' => ['nullable', 'exists:users,id'],
            'patient_id' => ['required', 'string', 'max:50', 'unique:patients,patient_id'],
            'photo_path' => ['nullable', 'string', 'max:255'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'medical_history' => ['nullable', 'string'],
        ];
    }
}

