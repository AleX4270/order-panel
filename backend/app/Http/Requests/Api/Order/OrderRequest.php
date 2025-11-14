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

    public function messages(): array {
        return [
            'id.integer' => __('validation.integer'),

            'countryId.required' => __('validation.required'),
            'countryId.integer' => __('validation.integer'),

            'provinceId.required' => __('validation.required'),
            'provinceId.integer' => __('validation.integer'),

            'cityId.required' => __('validation.required'),
            'cityId.integer' => __('validation.integer'),

            'postalCode.string' => __('validation.string'),
            'postalCode.max' => __('validation.max.string'),

            'address.required' => __('validation.required'),
            'address.string' => __('validation.string'),
            'address.max' => __('validation.max.string'),

            'phoneNumber.required' => __('validation.required'),
            'phoneNumber.regex' => __('validation.regex'),
            'phoneNumber.max' => __('validation.max.string'),

            'priorityId.required' => __('validation.required'),
            'priorityId.integer' => __('validation.integer'),

            'statusId.required' => __('validation.required'),
            'statusId.integer' => __('validation.integer'),

            'dateCreation.required' => __('validation.required'),
            'dateCreation.date' => __('validation.date'),

            'dateDeadline.required' => __('validation.required'),
            'dateDeadline.date' => __('validation.date'),
            'dateDeadline.after_or_equal' => __('validation.after_or_equal'),

            'dateCompleted.date' => __('validation.date'),
            'dateCompleted.after_or_equal' => __('validation.after_or_equal'),

            'remarks.string' => __('validation.string'),
            'remarks.max' => __('validation.max.string'),
        ];
    }

    public function toDto(): OrderDto {
        return OrderDto::fromArray($this->validated());
    }
}
