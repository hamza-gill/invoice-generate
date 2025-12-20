<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportCustomersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt,xlsx|max:10456',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please upload a CSV file.',
            'file.mimes' => 'The file must be a CSV or TXT format.',
            'file.max' => 'The file size must not exceed 10MB.',
        ];
    }
}
