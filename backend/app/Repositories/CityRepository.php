<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Dtos\Api\City\CityFilterDto;
use App\Enums\SortDir;
use App\Models\City;
use Illuminate\Database\Eloquent\Builder;

class CityRepository {
    public function getAll(CityFilterDto $dto): Builder {
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

        match($dto->sortColumn) {
            default => $query->orderBy('c.id', SortDir::ASC->value),
        };

        return $query;
    }
}