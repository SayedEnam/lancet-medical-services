<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('test_category');

        return [
            'name' => ['sometimes', 'string', 'max:255', "unique:test_categories,name,{$id}"],
            'description' => ['nullable', 'string'],
        ];
    }
}
