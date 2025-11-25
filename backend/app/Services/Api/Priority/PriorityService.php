<?php
declare(strict_types=1);

namespace App\Services\Api\Priority;

use App\Dtos\Api\Priority\PriorityFilterDto;
use App\Repositories\PriorityRepository;
use Illuminate\Support\Collection;

class PriorityService {
    public function __construct(
        private readonly PriorityRepository $priorityRepository,
    ) {}

    public function index(PriorityFilterDto $dto): Collection {
        $query = $this->priorityRepository->getAll($dto);

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
