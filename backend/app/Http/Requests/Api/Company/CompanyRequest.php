<?php

namespace App\Http\Requests\Api\Company;

use App\Dtos\Api\Company\CompanyDto;
use App\Enums\PermissionType;
use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user()?->can(PermissionType::COMPANY_MANAGE->value);
    }

    public function rules(): array {
        return [
            'id' => ['sometimes', 'integer'],
            'name' => ['required', 'string'],
            'countryId' => ['required', 'integer'],
            'provinceId' => ['required', 'integer'],
            'cityId' => ['required', 'integer'],
            'cityName' => ['sometimes', 'string'],
            'postalCode' => ['sometimes', 'string', 'max:32'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }

    public function toDto(): CompanyDto {
        return CompanyDto::fromArray($this->validated());
    }
}
