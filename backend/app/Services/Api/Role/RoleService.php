<?php
declare(strict_types=1);

namespace App\Services\Api\Role;

use App\Dtos\Api\Role\RoleFilterDto;
use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Support\Collection;

class RoleService {
    public function __construct(
        private readonly RoleRepository $roleRepository,
    ) {}

    public function index(RoleFilterDto $dto): Collection {
        $query = $this->roleRepository->getAll($dto);

        $totalItems = $query->count();
        if(!empty($dto->page) && !empty($dto->pageSize)) {
            $items = $query->forPage($dto->page, $dto->pageSize)->get();
        }
        else {
            $items = $query->get();
        }

        return collect([
            'items' => $items,
            'count' => $totalItems
        ]);
    }
}
