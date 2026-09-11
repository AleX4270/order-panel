<?php

namespace App\Http\Requests\Api\OrderRequest;

use App\Enums\PermissionType;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequestDeleteRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user()?->can(PermissionType::ORDER_REQUESTS_MANAGE->value);
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

    public function getOrderRequestId(): int {
        return (int)$this->validated('id');
    }
}
