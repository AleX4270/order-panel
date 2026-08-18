<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'email' => ['required', 'email', 'string', 'lowercase', 'max:255'],
            'password' => ['required', 'string']
        ];
    }

    public function messages(): array {
        return [
            'email.required' => __('auth.emailRequired'),
            'email.email' => __('auth.emailFormatEmail'),
            'email.string' => __('auth.emailString'),
            'email.lowercase' => __('auth.emailLowercase'),
            'email.max' => __('auth.emailMax'),
            'password.required' => __('auth.passwordRequired'),
            'password.string' => __('auth.passwordString'),
        ];
    }
}
