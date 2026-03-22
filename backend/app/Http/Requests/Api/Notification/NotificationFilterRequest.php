<?php

namespace App\Http\Requests\Api\Notification;

use App\Dtos\Api\Notification\NotificationFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class NotificationFilterRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'userId' => ['required', 'integer'],
        ];
    }

    public function toDto(): NotificationFilterDto {
        return NotificationFilterDto::fromArray($this->validated());
    }
}