<?php

namespace App\Http\Requests\Api\Order;

use App\Dtos\Api\Order\OrderFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class OrderFilterRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function prepareForValidation(): void {
        $this->merge([
           'priorityIds' => !empty($this->input('priorityIds')) ? explode(',', $this->input('priorityIds')) : null,
           'statusIds' => !empty($this->input('statusIds')) ? explode(',', $this->input('statusIds')) : null,
           'allFields' => trim($this->input('allFields')),
        ]);
    }

    public function rules(): array {
        return [
            'page' => ['nullable', 'integer'],
            'pageSize' => ['nullable', 'integer'],
            'sortColumn' => ['nullable', 'string'],
            'sortDir' => ['nullable', 'string'],
            'allFields' => ['nullable', 'string'],
            'priorityIds' => ['nullable', 'array'],
            'priorityIds.*' => ['sometimes', 'integer'],
            'statusIds' => ['nullable', 'array'],
            'statusIds.*' => ['sometimes', 'integer'],
            'dateCreation' => ['nullable', 'string'],
            'dateDeadline' => ['nullable', 'string'],
        ];
    }

    public function toDto(): OrderFilterDto {
        return OrderFilterDto::fromArray($this->validated());
    }
}