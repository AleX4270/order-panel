<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Dtos\Api\Status\StatusFilterDto;
use App\Enums\SortDir;
use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Builder;

class StatusRepository {
    public function getAll(StatusFilterDto $dto): Builder {
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
                    ->where('l.symbol', app()->getLocale());
            })
            ->where('os.is_active', 1);

        if(!empty($dto->term)) {
            $query->whereLike('ost.name', '%'.$dto->term.'%');
        }

        match($dto->sortColumn) {
            default => $query->orderBy('os.id', SortDir::ASC->value),
        };

        return $query;
    }
}