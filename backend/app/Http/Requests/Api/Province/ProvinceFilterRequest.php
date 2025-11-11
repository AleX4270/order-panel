<?php

namespace App\Http\Requests\Api\Province;

use App\Dtos\Api\Province\ProvinceFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class ProvinceFilterRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'page' => ['sometimes', 'integer'],
            'pageSize' => ['sometimes', 'integer'],
            'sortColumn' => ['sometimes', 'string'],
            'sortDir' => ['sometimes', 'string'],
            'countryId' => ['sometimes', 'integer'],
        ];
    }

    public function messages(): array {
        return [];
    }

    public function toDto(): ProvinceFilterDto {
        return ProvinceFilterDto::fromArray($this->validated());
    }
}