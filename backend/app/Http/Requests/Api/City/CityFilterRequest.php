<?php

namespace App\Http\Requests\Api\City;

use App\Dtos\Api\City\CityFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class CityFilterRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'term' => trim($this->term),
        ]);
    }

    public function rules(): array {
        return [
            'page' => ['sometimes', 'integer'],
            'pageSize' => ['sometimes', 'integer'],
            'sortColumn' => ['sometimes', 'string'],
            'sortDir' => ['sometimes', 'string'],
            'provinceId' => ['sometimes', 'integer'],
            'term' => ['sometimes', 'string']
        ];
    }

    public function messages(): array {
        return [];
    }

    public function toDto(): CityFilterDto {
        return CityFilterDto::fromArray($this->validated());
    }
}