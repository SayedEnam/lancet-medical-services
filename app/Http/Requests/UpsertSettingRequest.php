<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertSettingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'group' => ['required', 'string', 'max:100'],
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string'],
        ];
    }
}
