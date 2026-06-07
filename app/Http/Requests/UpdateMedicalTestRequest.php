<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalTestRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'test_category_id' => ['sometimes', 'exists:test_categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'sample_type' => ['nullable', 'string', 'max:255'],
            'report_delivery_time' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'is_popular' => ['boolean'],
            'home_collection_available' => ['boolean'],
        ];
    }
}
