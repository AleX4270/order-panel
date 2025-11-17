<?php

namespace App\Http\Requests\Api\Order;

use App\Dtos\Api\Order\OrderDto;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'id' => ['sometimes', 'integer'],
            'countryId' => ['required', 'integer'],
            'provinceId' => ['required', 'integer'],
            'cityId' => ['required', 'integer'],
            'cityName' => ['sometimes', 'string'],
            'postalCode' => ['nullable', 'string', 'max:32'],
            'address' => ['required', 'string', 'max:255'],
            'phoneNumber' => ['required', 'regex:/^[0-9]+\/[0-9]{4}$/', 'max:32'],
            'priorityId' => ['required', 'integer'],
            'statusId' => ['required', 'integer'],
            'dateCreation' => ['required', 'date'],
            'dateDeadline' => ['required', 'date', 'after_or_equal:dateCreation'],
            'dateCompleted' => ['sometimes', 'date', 'after_or_equal:dateCreation'],
            'remarks' => ['sometimes', 'string', 'max:2000'],
        ];
    }

    public function toDto(): OrderDto {
        return OrderDto::fromArray($this->validated());
    }
}
