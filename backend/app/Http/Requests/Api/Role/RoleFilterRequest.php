<?php

namespace App\Http\Requests\Api\Role;

use App\Dtos\Api\Role\RoleFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class RoleFilterRequest extends FormRequest {
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

    public function toDto(): RoleFilterDto {
        return RoleFilterDto::fromArray($this->validated());
    }
}