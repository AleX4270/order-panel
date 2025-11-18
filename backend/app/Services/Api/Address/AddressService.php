<?php
declare(strict_types=1);

namespace App\Services\Api\Address;

use App\Dtos\Api\Address\AddressDto;
use App\Dtos\Api\Address\AddressResolveDto;
use App\Dtos\Api\City\CityDto;
use App\Dtos\Api\City\CityResolveDto;
use App\Models\Address;
use App\Services\Api\City\CityService;

class AddressService {
    public function __construct(
        private readonly CityService $cityService,
    ) {}

    public function store(AddressDto $dto): Address {
        $result = Address::create([
            'city_id' => $dto->cityId,
            'address' => $dto->address,
            'postal_code' => $dto->postalCode,
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
        ->whereLike('postal_code', $dto->postalCode)
        ->where('city_id', $city->id)
        ->first();

        if(!empty($address)) {
            return $address;
        }

        return $this->store(AddressDto::fromArray([
            'address' => $dto->address,
            'postalCode' => $dto->postalCode,
            'cityId' => $city->id,
            'cityName' => $dto->cityName,
            'provinceId' => $dto->provinceId,
        ]));
    }
}
