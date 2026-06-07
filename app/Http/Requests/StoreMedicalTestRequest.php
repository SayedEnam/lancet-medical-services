<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalTestRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'test_category_id' => ['required', 'exists:test_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'sample_type' => ['nullable', 'string', 'max:255'],
            'report_delivery_time' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'is_popular' => ['boolean'],
            'home_collection_available' => ['boolean'],
        ];
    }
}
