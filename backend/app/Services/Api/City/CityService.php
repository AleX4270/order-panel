<?php
declare(strict_types=1);

namespace App\Services\Api\City;

use App\Dtos\Api\City\CityDto;
use App\Dtos\Api\City\CityFilterDto;
use App\Dtos\Api\City\CityResolveDto;
use App\Models\City;
use App\Repositories\CityRepository;
use Exception;
use Illuminate\Support\Collection;

class CityService {
    public function __construct(
        private readonly CityRepository $cityRepository,
    ) {}

    public function index(CityFilterDto $dto): Collection {
        $query = $this->cityRepository->getAll($dto);

        $totalItems = $query->count();
        if(!empty($dto->page) && !empty($dto->pageSize)) {
            $items = $query->forPage($dto->page, $dto->pageSize)->get();
        }
        else {
            $items = $query->get();
        }

        return collect([
            'items' => $items->map->toCamelCaseKeys() ?? [],
            'count' => $totalItems
        ]);
    }

    public function store(CityDto $dto): City {
        $result = City::create([
            'province_id' => $dto->provinceId,
            'name' => $dto->cityName,
        ]);

        return $result;
    }

    public function findOrCreate(CityResolveDto $dto): City {
        if(!empty($dto->cityId)) {
            return City::where('id', $dto->cityId)->firstOrFail();
        }

        if(empty($dto->cityId) && !empty($dto->cityName) && !empty($dto->provinceId)) {
            $existingCity = City::whereRaw('LOWER(name) = LOWER(?)', [$dto->cityName])
                ->where('province_id', $dto->provinceId)
                ->first();

            if(!empty($existingCity)) {
                return $existingCity;
            }
            
            return $this->store(CityDto::fromArray([
                'cityName' => $dto->cityName,
                'provinceId' => $dto->provinceId,
            ]));
        }

        throw new Exception('Could not find or create a city by provided params');
    }
}
