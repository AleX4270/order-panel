<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Dtos\Api\Province\ProvinceFilterDto;
use App\Enums\SortDir;
use App\Models\Province;
use Illuminate\Database\Eloquent\Builder;

class ProvinceRepository {
    public function getAll(ProvinceFilterDto $dto): Builder {
        $query = Province::query()
            ->from('provinces as p')
            ->select([
                'p.id',
                'p.name',
            ]);

        if(!empty($dto->countryId)) {
            $query->where('p.country_id', $dto->countryId);
        }

        match($dto->sortColumn) {
            default => $query->orderBy('p.id', SortDir::ASC->value),
        };

        return $query;
    }
}