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
            'enable_rush_delivery' => 'nullable|boolean',
            'rush_options' => 'nullable|array|min:1',
            'rush_options.*.label' => 'required_with:rush_options|string|max:255',
            'rush_options.*.days' => 'required_with:rush_options',
            'rush_options.*.fee' => 'required_with:rush_options|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'starting_invoice_number.regex' => 'The starting invoice number must follow the format INV-YYYY-NNN (e.g., INV-2025-001).',
            'rush_options.min' => 'At least one rush delivery option is required when rush delivery is enabled.',
            'rush_options.*.label.required_with' => 'Label is required for each rush delivery option.',
            'rush_options.*.days.required_with' => 'Days is required for each rush delivery option.',
            'rush_options.*.fee.required_with' => 'Fee is required for each rush delivery option.',
            'rush_options.*.fee.numeric' => 'Fee must be a valid number.',
            'rush_options.*.fee.min' => 'Fee cannot be negative.',
        ];
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation()
    {
        // Additional validation: If rush delivery is enabled, ensure rush_options exist
        if ($this->has('enable_rush_delivery') && !$this->has('rush_options')) {
            $this->validator->errors()->add(
                'rush_options',
                'At least one rush delivery option is required when rush delivery is enabled.'
            );

            throw new \Illuminate\Validation\ValidationException($this->validator);
        }
    }
}
