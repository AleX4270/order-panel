<?php

namespace App\Http\Requests\Api\Order;

use App\Enums\PermissionType;
use Illuminate\Foundation\Http\FormRequest;

class OrderShowRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user()?->can(PermissionType::ORDERS_SHOW->value);
    }

    public function prepareForValidation(): void {
        $this->merge([
            'id' => $this->route('id'),
        ]);
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
