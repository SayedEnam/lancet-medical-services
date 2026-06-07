<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('invoice');

        return [
            'patient_id' => ['sometimes', 'exists:patients,id'],
            'invoice_no' => ['sometimes', 'string', 'max:255', "unique:invoices,invoice_no,{$id}"],
            'sub_total' => ['sometimes', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'vat' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
