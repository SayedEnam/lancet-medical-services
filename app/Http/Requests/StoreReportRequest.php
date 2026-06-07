<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'test_order_id' => ['required', 'exists:test_orders,id'],
            'type' => ['required', 'in:hematology,biochemistry,serology,radiology,histopathology'],
            'status' => ['nullable', 'in:processing,pending_approval,approved,delivered'],
            'digital_signature' => ['nullable', 'string', 'max:255'],
        ];
    }
}
