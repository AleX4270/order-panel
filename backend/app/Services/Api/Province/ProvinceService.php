<?php
declare(strict_types=1);

namespace App\Services\Api\Province;

use App\Dtos\Api\Province\ProvinceFilterDto;
use App\Repositories\ProvinceRepository;
use Illuminate\Support\Collection;

class ProvinceService {
    public function __construct(
        private readonly ProvinceRepository $provinceRepository,
    ) {}

    public function index(ProvinceFilterDto $dto): Collection {
        $query = $this->provinceRepository->getAll($dto);

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
