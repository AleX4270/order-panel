<?php

namespace App\Dtos\Api\City;

final readonly class CityDto {
    public function __construct(
        public int $cityId,
        public ?string $cityName,
        public int $provinceId,
    ) {}

    public static function fromArray(array $data): self {
        return new CityDto(
            cityId: $data['cityId'],
            cityName: $data['cityName'] ?? null,
            provinceId: $data['provinceId'],
        );
    }
}