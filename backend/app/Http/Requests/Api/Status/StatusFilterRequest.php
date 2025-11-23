<?php

namespace App\Http\Requests\Api\Status;

use App\Dtos\Api\Status\StatusFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class StatusFilterRequest extends FormRequest {
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

    public function toDto(): StatusFilterDto {
        return StatusFilterDto::fromArray($this->validated());
    }
}