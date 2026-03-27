<?php

namespace App\Http\Requests\Api\NotificationChannel;

use App\Dtos\Api\NotificationChannel\NotificationChannelFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class NotificationChannelFilterRequest extends FormRequest {
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

    public function toDto(): NotificationChannelFilterDto {
        return NotificationChannelFilterDto::fromArray($this->validated());
    }
}
