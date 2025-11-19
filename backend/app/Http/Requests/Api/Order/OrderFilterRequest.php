<?php

namespace App\Http\Requests\Api\Order;

use App\Dtos\Api\Order\OrderFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class OrderFilterRequest extends FormRequest {
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

    public function toDto(): OrderFilterDto {
        return OrderFilterDto::fromArray($this->validated());
    }
}