<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tax_id_invoice'           => 'nullable|string|max:255',
            'enable_tax_id'            => 'nullable|boolean',
            'enable_terms'             => 'nullable|boolean',
            'enable_invoice_notes'     => 'nullable|boolean',
            'enable_tax'               => 'nullable|boolean',
            'enable_due_date'          => 'nullable|boolean',
            'starting_invoice_number'  => [
                'required',
                'string',
                'regex:/^INV-\d{4}-\d{3,}$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'starting_invoice_number.regex' => 'The starting invoice number must follow the format INV-YYYY-NNN (e.g., INV-2025-001).',
        ];
    }
}
