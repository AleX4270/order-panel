<?php
declare(strict_types=1);

namespace App\Services\Api\Province;

use App\Dtos\Api\Province\ProvinceFilterDto;
use App\Enums\SortDir;
use App\Models\Province;
use Illuminate\Support\Collection;

class ProvinceService {
    public function index(ProvinceFilterDto $dto): Collection {
        // TODO: This probably can be located in a dedicated repository class
        $query = Province::query()
            ->from('provinces as p')
            ->select([
                'p.id',
                'p.name',
            ]);

        if(!empty($dto->countryId)) {
            $query->where('p.country_id', $dto->countryId);
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
