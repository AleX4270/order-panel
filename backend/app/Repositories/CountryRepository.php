<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Dtos\Api\Country\CountryFilterDto;
use App\Enums\SortDir;
use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;

class CountryRepository {
    public function getAll(CountryFilterDto $dto): Builder {
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
                    ->where('l.symbol', app()->getLocale());
            });

        match($dto->sortColumn) {
            default => $query->orderBy('c.id', SortDir::ASC->value),
        };

        return $query;
    }
}