<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'test_order_id' => ['sometimes', 'exists:test_orders,id'],
            'type' => ['sometimes', 'in:hematology,biochemistry,serology,radiology,histopathology'],
            'status' => ['sometimes', 'in:processing,pending_approval,approved,delivered'],
            'digital_signature' => ['nullable', 'string', 'max:255'],
        ];
    }
}
