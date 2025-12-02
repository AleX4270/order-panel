<?php

namespace App\Http\Requests\Api\User;

use App\Dtos\Api\User\UserDto;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'id' => ['sometimes', 'integer'],
            'firstName' => ['sometimes', 'string'],
            'lastName' => ['sometimes', 'string'],
            'username' => ['required', 'string', 'unique:users,name'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['sometimes', 'string', 'confirmed:passwordConfirmed'],
            'passwordConfirmed' => ['required_with:password', 'string'],
        ];
    }

    // TODO: Validate the password fields

    public function toDto(): UserDto {
        return UserDto::fromArray($this->validated());
    }
}
