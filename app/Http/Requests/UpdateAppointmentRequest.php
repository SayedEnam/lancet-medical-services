<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'patient_id' => ['sometimes', 'exists:patients,id'],
            'doctor_id' => ['sometimes', 'exists:doctors,id'],
            'appointment_date' => ['sometimes', 'date'],
            'appointment_time' => ['sometimes'],
            'status' => ['nullable', 'in:scheduled,checked_in,completed,cancelled'],
            'is_walk_in' => ['boolean'],
            'queue_no' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
