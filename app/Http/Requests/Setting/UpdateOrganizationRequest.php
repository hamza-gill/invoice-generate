<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled in controller via policy
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name'   => 'required|string|max:255',
            'tax_id'         => 'nullable|string|max:255',
            'country'        => 'nullable|string|max:255',
            'base_currency'  => 'nullable|string|max:10',
            'address'        => 'nullable|string',
            'invoice_notes'  => 'nullable|string',
            'invoice_terms'  => 'nullable|string',
            'contact_email'  => 'nullable|string',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'logo_path'      => 'nullable|image|mimes:jpg,webp,jpeg,png,svg|max:2048',
        ];
    }
}
