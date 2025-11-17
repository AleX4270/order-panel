<?php

namespace App\Dtos\Api\Address;

final readonly class AddressDto {
    public function __construct(
        public string $address,
        public ?string $postalCode,
        public int $cityId,
        public ?string $cityName,
        public int $provinceId,
    ) {}

    public static function fromArray(array $data): self {
        return new AddressDto(
            address: $data['address'],
            postalCode: $data['postalCode'] ?? null,
            cityId: $data['cityId'],
            cityName: $data['cityName'] ?? null,
            provinceId: $data['provinceId'],
        );
    }
}