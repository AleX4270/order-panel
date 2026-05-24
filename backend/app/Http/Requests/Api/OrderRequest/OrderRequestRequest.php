<?php

namespace App\Http\Requests\Api\OrderRequest;

use App\Dtos\Api\OrderRequest\OrderRequestDto;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequestRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function prepareForValidation(): void {
        $this->merge([
            'email' => strtolower($this->email),
        ]);
    }

    public function rules(): array {
        return [
            'firstName' => ['required', 'string', 'max:128'],
            'lastName' => ['required', 'string', 'max:128'],
            'alias' => ['nullable', 'string', 'max:128'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phoneNumber' => ['required', 'string', 'max:32', 'regex:/^[0-9\s()+-]{6,20}$/'],
            'countryId' => ['required', 'integer', 'min:1'],
            'provinceId' => ['required', 'integer', 'min:1'],
            'city' => ['required', 'string', 'max:128'],
            'postalCode' => ['required', 'string', 'max:32'],
            'address' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'isConsentGiven' => ['required', 'accepted'],
            'citizenship' => ['prohibited']
        ];
    }

    public function toDto(): OrderRequestDto {
        return OrderRequestDto::fromArray(
            array_merge(
                $this->validated(),
                [
                    'ipAddress' => $this->ip(),
                    'userAgent' => $this->userAgent(),
                ],
            )
        );
    }
}
