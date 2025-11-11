<?php
declare(strict_types=1);

namespace App\Services\Api\City;

use App\Dtos\Api\City\CityFilterDto;
use App\Enums\SortDir;
use App\Models\City;
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
            default => $query->orderBy('id', SortDir::ASC->value),
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
}
