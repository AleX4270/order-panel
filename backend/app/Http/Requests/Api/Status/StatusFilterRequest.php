<?php

namespace App\Http\Requests\Api\Status;

use App\Dtos\Api\Status\StatusFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class StatusFilterRequest extends FormRequest {
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

    public function toDto(): StatusFilterDto {
        return StatusFilterDto::fromArray($this->validated());
    }
}