<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIntegrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stripe_public_key'  => 'nullable|string|max:255',
            'stripe_secret_key'  => 'nullable|string|max:255',
            'webhook_url'        => 'nullable|url',
            'webhook_secret'     => 'nullable|string|max:255',
            'google_places_key'  => 'nullable|string',
        ];
    }
}
