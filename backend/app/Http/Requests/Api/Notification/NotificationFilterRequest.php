<?php

namespace App\Http\Requests\Api\Notification;

use App\Dtos\Api\Notification\NotificationFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class NotificationFilterRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function prepareForValidation(): void {
        $this->merge([
           'onlyUnread' => filter_var($this->input('onlyUnread'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        ]);
    }

    public function rules(): array {
        return [
            'userId' => ['required', 'integer'],
            'onlyUnread' => ['nullable', 'boolean'],
        ];
    }

    public function toDto(): NotificationFilterDto {
        return NotificationFilterDto::fromArray($this->validated());
    }
}