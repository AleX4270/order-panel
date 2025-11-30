<?php

namespace App\Http\Requests\Api\Country;

use App\Dtos\Api\Country\CountryFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class CountryFilterRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'page' => ['sometimes', 'integer'],
            'pageSize' => ['sometimes', 'integer'],
            'sortColumn' => ['sometimes', 'string'],
            'sortDir' => ['sometimes', 'string'],
        ];
    }

    public function messages(): array {
        return [];
    }

    public function toDto(): CountryFilterDto {
        return CountryFilterDto::fromArray($this->validated());
    }
}