<?php
declare(strict_types=1);

namespace App\Services\Api\City;

use App\Dtos\Api\City\CityDto;
use App\Dtos\Api\City\CityFilterDto;
use App\Dtos\Api\City\CityResolveDto;
use App\Enums\SortDir;
use App\Models\City;
use Exception;
use Illuminate\Support\Collection;

class CityService {
    public function index(CityFilterDto $dto): Collection {
        // TODO: This probably can be located in a dedicated repository class
        $query = City::query()
            ->from('cities as c')
            ->select([
                'c.id',
                'c.name',
            ]);

        if(!empty($dto->provinceId)) {
            $query->where('c.province_id', $dto->provinceId);
        }

        if(!empty($dto->term)) {
            $query->whereLike('c.name', '%'. $dto->term .'%');
        }

        match(true) {
            default => $query->orderBy('c.id', SortDir::ASC->value),
        };

        $totalItems = $query->count();
        $items = collect([]);

        if(!empty($dto->page) && !empty($dto->pageSize)) {
            $items = $query->forPage($dto->page, $dto->pageSize)->get();
        }
        else {
            $items = $query->get();
        }

        return collect([
            'items' => $items->map->toCamelCaseKeys(),
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
