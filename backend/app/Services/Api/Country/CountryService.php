<?php
declare(strict_types=1);

namespace App\Services\Api\Country;

use App\Dtos\Api\Country\CountryFilterDto;
use App\Repositories\CountryRepository;
use Illuminate\Support\Collection;

class CountryService {
    public function __construct(
        private readonly CountryRepository $countryRepository,
    ) {}

    public function index(CountryFilterDto $dto): Collection {
        $query = $this->countryRepository->getAll($dto);

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
}
