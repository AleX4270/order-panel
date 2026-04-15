<?php
declare(strict_types=1);

namespace App\Services\Api\Address;

use App\Dtos\Api\Address\AddressDto;
use App\Dtos\Api\Address\AddressResolveDto;
use App\Dtos\Api\City\CityResolveDto;
use App\Models\Address;
use App\Models\Province;
use App\Services\Api\City\CityService;
use App\Services\Nominatim\GeocodingService;

class AddressService {
    public function __construct(
        private readonly CityService $cityService,
        private readonly GeocodingService $geocodingService,
    ) {}

    public function store(AddressDto $dto): Address {
        $coordinates = $this->geocodingService->getCoordinates($dto);

        $result = Address::create([
            'city_id' => $dto->cityId,
            'address' => $dto->address,
            'postal_code' => $dto->postalCode,
            'coordinates' => $coordinates,
        ]);

        return $result;
    }

    public function findOrCreate(AddressResolveDto $dto): Address {
        $city = $this->cityService->findOrCreate(CityResolveDto::fromArray([
            'cityId' => $dto->cityId,
            'cityName' => $dto->cityName,
            'provinceId' => $dto->provinceId,
        ]));

        $address = Address::whereLike('address', $dto->address)
            ->where('city_id', $city->id);

        if(!empty($dto->postalCode)) {
            $address->whereLike('postal_code', $dto->postalCode);
        }

        $address = $address->first();

        if(!empty($address)) {
            return $address;
        }

        return $this->store(AddressDto::fromArray([
            'address' => $dto->address,
            'postalCode' => $dto->postalCode,
            'cityId' => $city->id,
            'cityName' => $dto->cityName,
            'provinceId' => $dto->provinceId,
            'countrySymbol' => Province::findOrFail($dto->provinceId)->country->symbol,
        ]));
    }
}
