<?php
declare(strict_types=1);

namespace App\Services\Api\Status;

use App\Dtos\Api\Status\StatusFilterDto;
use App\Repositories\StatusRepository;
use Illuminate\Support\Collection;

class StatusService {
    public function __construct(
        private readonly StatusRepository $statusRepository,
    ) {}

    public function index(StatusFilterDto $dto): Collection {
        $query = $this->statusRepository->getAll($dto);

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
