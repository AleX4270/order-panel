<?php
declare(strict_types=1);

namespace App\Services\Api\Status;

use App\Dtos\Api\Status\StatusFilterDto;
use App\Enums\SortDir;
use App\Models\OrderStatus;
use Illuminate\Support\Collection;

class StatusService {
    public function index(StatusFilterDto $dto): Collection {
        // TODO: This probably can be located in a dedicated repository class
        $query = OrderStatus::query()
            ->from('order_statuses as os')
            ->select([
                'os.id',
                'os.symbol',
                'ost.name'
            ])
            ->leftJoin('order_status_translations as ost', 'ost.order_status_id', '=', 'os.id')
            ->join('languages as l', function($join) {
                $join->on('l.id', '=', 'ost.language_id')
                    ->where('l.symbol', 'pl');
            })
            ->where('os.is_active', 1);

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
