<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Dtos\Api\Priority\PriorityFilterDto;
use App\Enums\SortDir;
use App\Models\Priority;
use Illuminate\Database\Eloquent\Builder;

class PriorityRepository {
    public function getAll(PriorityFilterDto $dto): Builder {
        $query = Priority::query()
            ->from('priorities as p')
            ->select([
                'p.id',
                'p.symbol',
                'p.is_active',
                'pt.name'
            ])
            ->leftJoin('priority_translations as pt', 'pt.priority_id', '=', 'p.id')
            ->join('languages as l', function($join) {
                $join->on('l.id', '=', 'pt.language_id')
                    ->where('l.symbol', app()->getLocale());
            })
            ->where('p.is_active', 1);

        if(!empty($dto->term)) {
            $query->whereLike('pt.name', '%'.$dto->term.'%');
        }

        match($dto->sortColumn) {
            default => $query->orderBy('p.id', SortDir::ASC->value),
        };

        return $query;
    }
}