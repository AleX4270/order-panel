<?php

namespace App\Http\Requests\Api\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'id' => ['sometimes', 'required', 'integer'],
            'orderNumber' => ['required', 'string', 'max:32'],
            'priorityId' => ['required', 'integer'],
            'countryId' => ['required', 'integer'],
            'provinceId' => ['required', 'integer'],
            'cityId' => ['required', 'integer'],
            'address' => ['required', 'string', 'max:255'],
            'phoneNumber' => ['required', 'regex:/^(?:\+?\d{1,3}|\(?\d{2,4}\)?)?[\s-]?\d{3}(?:[\s-]?\d{2,3}){2,3}$/', 'max:32'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array {
        return [];
    }
}
