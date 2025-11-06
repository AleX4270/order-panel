<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'email' => ['required', 'email', 'string', 'lowercase', 'max:255'],
            'password' => ['required', 'string', Password::defaults()]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
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
