<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest {
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'name.required' => __('auth.nameRequired'),
            'name.string' => __('auth.nameString'),
            'name.max' => __('auth.nameMax'),
            'email.required' => __('auth.emailRequired'),
            'email.string' => __('auth.emailString'),
            'email.lowercase' => __('auth.emailLowercase'),
            'email.email' => __('auth.emailFormatEmail'),
            'email.max' => __('auth.emailMax'),
            'email.unique' => __('auth.emailUnique'),
            'password.required' => __('auth.passwordRequired'),
            'password.confirmed' => __('auth.passwordConfirmed'),
            'password.string' => __('auth.passwordString'),
        ];
    }
}
