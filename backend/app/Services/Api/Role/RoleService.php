<?php
declare(strict_types=1);

namespace App\Services\Api\Role;

use App\Dtos\Api\Role\RoleFilterDto;
use App\Models\Role;
use Illuminate\Support\Collection;

class RoleService {
    public function index(RoleFilterDto $dto): Collection {
        $query = Role::query();

        $totalItems = $query->count();
        if(!empty($dto->page) && !empty($dto->pageSize)) {
            $items = $query->forPage($dto->page, $dto->pageSize)->get();
        }
        else {
            $items = $query->get();
        }

        $items = $items->map(function(Role $item) {
            return [
                'id' => $item->id,
                'symbol' => $item->name,
                'name' => $item->translation->name,
            ];
        });

        return collect([
            'items' => $items ?? [],
            'count' => $totalItems
        ]);
    }
}
