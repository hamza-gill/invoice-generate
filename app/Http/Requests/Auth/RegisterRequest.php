<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules()
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',      // At least one uppercase letter
                'regex:/[0-9]/',      // At least one digit
                'regex:/[@$!%*#?&]/', // At least one special character
                'confirmed',
            ],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'company_name' => ['required', 'string', 'max:100'],
            'phone' => ['required'],
            'terms' => ['accepted'],
            'privacy' => ['accepted'],
            'sharing' => ['accepted'],
        ];
    }

    public function messages()
    {
        return [
            'country_code.regex' => 'Country code must start with "+" followed by 1–4 digits.',
            'password.regex' => 'Password must include at least one uppercase letter, one digit, and one special character.',
            'terms.accepted' => 'You must accept the Terms of Service.',
            'privacy.accepted' => 'You must accept the Privacy Policy.',
            'sharing.accepted' => 'You must accept the data sharing agreement.',
        ];
    }

}
