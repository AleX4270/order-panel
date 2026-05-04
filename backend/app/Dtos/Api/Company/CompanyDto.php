<?php
declare(strict_types=1);

namespace App\Dtos\Api\Company;

use App\Dtos\Api\Address\AddressDto;

final readonly class CompanyDto {
    public function __construct(
        public ?int $id,
        public string $name,
        public AddressDto $addressDto,
    ) {}

    public static function fromArray(array $data): self {
        return new CompanyDto(
            id: $data['id'] ?? null,
            name: $data['name'],
            addressDto: AddressDto::fromArray([
                'address' => $data['address'],
                'postalCode' => $data['postalCode'] ?? null,
                'cityId' => $data['cityId'],
                'cityName' => $data['cityName'] ?? null,
                'provinceId' => $data['provinceId'],
                'countryId' => $data['countryId'],
            ]),
        );
    }

    public function isConfigured(): bool {
        return !empty($this->id);
    }
}