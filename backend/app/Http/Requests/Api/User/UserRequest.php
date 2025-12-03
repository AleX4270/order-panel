<?php

namespace App\Http\Requests\Api\User;

use App\Dtos\Api\User\UserDto;
use App\Models\User;
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
            'username' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['sometimes', 'string', 'confirmed:passwordConfirmed'],
            'passwordConfirmed' => ['required_with:password', 'string'],
        ];
    }

    // TODO: Validate the password fields
    public function withValidator($validator): void {
        $validator->after(function ($validator) {
            $this->validateUniqueness($validator);
            $this->validatePassword($validator);
        });
    }

    private function validatePassword($validator): void {

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
