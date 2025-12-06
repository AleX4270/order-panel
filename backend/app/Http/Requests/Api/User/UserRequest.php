<?php

namespace App\Http\Requests\Api\User;

use App\Dtos\Api\User\UserDto;
use App\Enums\PermissionType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest {
    public function authorize(): bool {
        return !empty($this->input('id'))
            ? $this->user()?->can(PermissionType::USERS_UPDATE->value)
            : $this->user()?->can(PermissionType::USERS_CREATE->value);
    }

    public function rules(): array {
        return [
            'id' => ['sometimes', 'integer'],
            'firstName' => ['sometimes', 'string'],
            'lastName' => ['sometimes', 'string'],
            'username' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['sometimes', 'string', 'confirmed:passwordConfirmed', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[#?!@$%^&*\-.,]).{8,}$/',],
            'passwordConfirmed' => ['required_with:password', 'string'],
            'roles' => ['sometimes', 'array']
        ];
    }

    public function messages() {
        return [
            'password.regex' => __('validation.passwordTooWeak'),
        ];
    }

    public function withValidator($validator): void {
        $validator->after(function ($validator) {
            $this->validateUniqueness($validator);
        });
    }

    private function validateUniqueness($validator): void {
        $id = $this->input('id');
        $username = $this->input('username');
        $email = $this->input('email');

        $matchingNameUser = User::where('name', $username);
        $matchingEmailUser = User::where('email', $email);

        if(!empty($id)) {
            $matchingNameUser->where('id', '<>', $id);
            $matchingEmailUser->where('id', '<>', $id);
        }

        if($matchingNameUser->exists()) {
            $validator->errors()->add('name', __('messages.duplicatedName'));
        }

        if($matchingEmailUser->exists()) {
            $validator->errors()->add('email', __('messages.duplicatedEmail'));
        }
    }

    public function toDto(): UserDto {
        return UserDto::fromArray($this->validated());
    }
}
