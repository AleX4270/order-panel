<?php

namespace App\Http\Requests\Api\User;

use App\Dtos\Api\User\UserFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class UserFilterRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function prepareForValidation(): void {
        $this->merge([
           'allFields' => trim($this->input('allFields')),
        ]);
    }

    public function rules(): array {
        return [
            'page' => ['nullable', 'integer'],
            'pageSize' => ['nullable', 'integer'],
            'sortColumn' => ['nullable', 'string'],
            'sortDir' => ['nullable', 'string'],
            'allFields' => ['nullable', 'string'],
            'dateCreated' => ['nullable', 'string'],
            'dateUpdated' => ['nullable', 'string'],
        ];
    }

    public function toDto(): UserFilterDto {
        return UserFilterDto::fromArray($this->validated());
    }
}