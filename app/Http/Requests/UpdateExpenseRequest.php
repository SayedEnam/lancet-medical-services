<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'expense_date' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
