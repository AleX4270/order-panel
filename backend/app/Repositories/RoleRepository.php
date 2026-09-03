<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Dtos\Api\Role\RoleFilterDto;
use App\Enums\SortDir;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;

class RoleRepository {
    public function getAll(RoleFilterDto $dto): Builder {
        $query = Role::query()
            ->whereHas('translations.language', fn($q) => $q->where('symbol', app()->getLocale()))
            ->with(['translations' => function($q) {
                $q->whereHas('language', fn($q) => $q->where('symbol', app()->getLocale()));
            }]);

        match($dto->sortColumn) {
            default => $query->orderBy('id', SortDir::ASC->value),
        };

        return $query;
    }
}