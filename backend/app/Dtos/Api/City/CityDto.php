<?php

namespace App\Dtos\Api\City;

final readonly class CityDto {
    public function __construct(
        public ?int $id,
        public ?string $cityName,
        public int $provinceId,
    ) {}

    public static function fromArray(array $data): self {
        return new CityDto(
            id: $data['cityId'] ?? null,
            cityName: $data['cityName'] ?? null,
            provinceId: $data['provinceId'],
        );
    }
}