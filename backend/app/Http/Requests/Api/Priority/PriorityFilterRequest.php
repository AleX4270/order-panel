<?php

namespace App\Http\Requests\Api\Priority;

use App\Dtos\Api\Priority\PriorityFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class PriorityFilterRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function prepareForValidation() {
        $this->merge([
            'term' => trim($this->input('term')),
        ]);
    }

    public function rules(): array {
        return [
            'page' => ['sometimes', 'integer'],
            'pageSize' => ['sometimes', 'integer'],
            'sortColumn' => ['sometimes', 'string'],
            'sortDir' => ['sometimes', 'string'],
            'term' => ['sometimes', 'string'],
        ];
    }

    public function messages(): array {
        return [];
    }

    public function toDto(): PriorityFilterDto {
        return PriorityFilterDto::fromArray($this->validated());
    }
}