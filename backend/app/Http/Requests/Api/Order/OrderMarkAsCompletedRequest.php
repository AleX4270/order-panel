<?php

namespace App\Http\Requests\Api\Order;

use App\Enums\PermissionType;
use Illuminate\Foundation\Http\FormRequest;

class OrderMarkAsCompletedRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user()?->can(PermissionType::ORDERS_MARK_AS_COMPLETED->value);
    }

    public function rules(): array {
        return [
            'id' => ['required', 'integer', 'min:1'],
        ];
    }

    public function getOrderId(): int {
        return (int)$this->validated('id');
    }
}
