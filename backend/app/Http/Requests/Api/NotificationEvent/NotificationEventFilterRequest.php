<?php

namespace App\Http\Requests\Api\NotificationEvent;

use App\Dtos\Api\NotificationEvent\NotificationEventFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class NotificationEventFilterRequest extends FormRequest {
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

    public function toDto(): NotificationEventFilterDto {
        return NotificationEventFilterDto::fromArray($this->validated());
    }
}
