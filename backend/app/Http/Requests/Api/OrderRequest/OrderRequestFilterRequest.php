<?php

namespace App\Http\Requests\Api\OrderRequest;

use App\Dtos\Api\OrderRequest\OrderRequestFilterDto;
use App\Enums\PermissionType;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequestFilterRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user()?->can(PermissionType::ORDER_REQUESTS_VIEW->value);
    }

    public function prepareForValidation(): void {
        $this->merge([
            'allFields' => trim($this->input('allFields')),
        ]);
    }

    public function rules(): array {
        return [
            'page' => ['nullable', 'integer'],
            'pageSize' => ['nullable', 'integer'],
            'sortColumn' => ['nullable', 'string'],
            'sortDir' => ['nullable', 'string'],
            'distanceFromHeadquarters' => ['nullable', 'numeric'],
            'dateCreation' => ['nullable', 'string'],
            'allFields' => ['nullable', 'string'],
        ];
    }

    public function toDto(): OrderRequestFilterDto {
        return OrderRequestFilterDto::fromArray($this->validated());
    }
}
