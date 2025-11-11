<?php
declare(strict_types=1);

namespace App\Services\Api\Country;

use App\Dtos\Api\Country\CountryFilterDto;
use App\Enums\SortDir;
use App\Models\Country;
use Illuminate\Support\Collection;

class CountryService {
    public function index(CountryFilterDto $dto): Collection {
        // TODO: This probably can be located in a dedicated repository class
        $query = Country::query()
            ->from('countries as c')
            ->select([
                'c.id',
                'c.symbol',
                'ct.name'
            ])
            ->leftJoin('country_translations as ct', 'ct.country_id', '=', 'c.id')
            ->join('languages as l', function($join) {
                $join->on('l.id', '=', 'ct.language_id')
                    ->where('l.symbol', 'pl');
            });

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
