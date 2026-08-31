<?php

namespace App\Http\Requests\Api\Notification;

use Illuminate\Foundation\Http\FormRequest;

class NotificationMarkAsReadRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'id' => ['required', 'uuid'],
        ];
    }
}
