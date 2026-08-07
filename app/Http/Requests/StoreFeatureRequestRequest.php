<?php

namespace App\Http\Requests;

use App\Models\FeatureRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeatureRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'company' => ['nullable', 'string', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'request_type' => ['required', Rule::in(array_keys(FeatureRequest::TYPES))],
            'module_name' => ['nullable', 'string', 'max:190', 'required_if:request_type,new_module'],
            'title' => ['required', 'string', 'max:190'],
            'requirements' => ['required', 'string', 'max:10000'],
            'use_case' => ['nullable', 'string', 'max:10000'],
            'priority' => ['required', Rule::in(array_keys(FeatureRequest::PRIORITIES))],
        ];
    }

    public function messages(): array
    {
        return [
            'module_name.required_if' => 'Please name the module or area you need built.',
            'requirements.required' => 'Please describe what you need and how it should work.',
        ];
    }
}
