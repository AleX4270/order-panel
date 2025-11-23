<?php
declare(strict_types=1);

namespace App\Services\Api\Priority;

use App\Dtos\Api\Priority\PriorityFilterDto;
use App\Enums\SortDir;
use App\Models\Priority;
use Illuminate\Support\Collection;

class PriorityService {
    public function index(PriorityFilterDto $dto): Collection {
        // TODO: This probably can be located in a dedicated repository class
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

        match(true) {
            default => $query->orderBy('id', SortDir::ASC->value),
        };

        $totalItems = $query->count();
        $items = collect([]);

        if(!empty($dto->page) && !empty($dto->pageSize)) {
            $items = $query->forPage($dto->page, $dto->pageSize)->get();
        }
        else {
            $items = $query->get();
        }

        return collect([
            'items' => $items->map->toCamelCaseKeys(),
            'count' => $totalItems
        ]);
    }
}
