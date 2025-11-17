<?php
declare(strict_types=1);

namespace App\Services\Api\Address;

use App\Dtos\Api\Address\AddressDto;
use App\Dtos\Api\City\CityDto;
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

    public function findOrCreate(AddressDto $dto): Address {
        if(!empty($dto->cityId)) {
            $address = Address::whereLike('address', $dto->address)
            ->whereLike('postal_code', $dto->postalCode)
            ->where('city_id', $dto->cityId)
            ->first();

            if(!empty($address)) {
                return $address;
            }
        }

        //Get or make city id
        $cityDto = CityDto::fromArray([
            'cityId' => $dto->cityId,
            'cityName' => $dto->cityName,
            'provinceId' => $dto->provinceId,
        ]);
        $city = $this->cityService->findOrCreate($cityDto);
        
        $newAddressDto = AddressDto::fromArray([
            'address' => $dto->address,
            'postalCode' => $dto->postalCode,
            'cityId' => $city->id,
            'cityName' => $dto->cityName,
            'provinceId' => $dto->provinceId,
        ]);
        return $this->store($newAddressDto);
    }
}
