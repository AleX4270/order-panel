<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Dtos\Api\Order\OrderFilterDto;
use App\Enums\SortDir;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OrderRepository {
    public function getAll(OrderFilterDto $dto): Builder {
        $query = Order::query()
            ->from('orders as o')
            ->select([
                'o.id',
                'a.address',
                'a.postal_code',
                'c.name as cityName',
                'p.name as provinceName',
                'pr.symbol as prioritySymbol',
                'prt.name as priorityName',
                'os.symbol as statusSymbol',
                'o.created_at as dateCreated',
                'o.date_deadline',
                'ot.remarks',
            ])
            ->join('clients as cl', 'cl.id', '=', 'o.client_id')
            ->join('addresses as a', 'a.id', '=', 'cl.address_id')
            ->join('cities as c', 'c.id', '=', 'a.city_id')
            ->join('provinces as p', 'p.id', '=', 'c.province_id')
            ->join('priorities as pr', 'pr.id', '=', 'o.priority_id')
            ->join('priority_translations as prt', 'prt.priority_id', '=', 'pr.id')
            ->join('languages as prl', function($prlJoin) {
                $prlJoin->on('prl.id', '=', 'prt.language_id')
                    ->where('prl.symbol', app()->getLocale());
            })
            ->join('order_statuses as os', 'os.id', '=', 'o.status_id')
            ->join('order_translations as ot', 'ot.order_id', '=', 'o.id')
            ->join('languages as otl', function($prlJoin) {
                $prlJoin->on('otl.id', '=', 'ot.language_id')
                    ->where('otl.symbol', app()->getLocale());
            });

        if(!empty($dto->allFields)) {
            $query->whereLike('a.address', '%'.$dto->allFields.'%')
                ->orWhereLike('a.postal_code', '%'.$dto->allFields.'%')
                ->orWhereLike('c.name', '%'.$dto->allFields.'%')
                ->orWhereLike('cl.first_name', '%'.$dto->allFields.'%')
                ->orWhereLike('cl.last_name', '%'.$dto->allFields.'%')
                ->orWhereLike('cl.email', '%'.$dto->allFields.'%')
                ->orWhereLike('cl.phone_number', '%'.$dto->allFields.'%')
                ->orWhereLike('ot.remarks', '%'.$dto->allFields.'%')
                ->orWhere('o.id', $dto->allFields);
        }

        if(!empty($dto->priorityIds)) {
            $query->whereIn('o.priority_id', $dto->priorityIds);
        }

        if(!empty($dto->statusIds)) {
            $query->whereIn('o.status_id', $dto->statusIds);
        }

        if(!empty($dto->dateCreation)) {
            $query->whereBetween('o.created_at', [
                Carbon::parse($dto->dateCreation)->startOfDay()->toDateTimeString(),
                Carbon::parse($dto->dateCreation)->endOfDay()->toDateTimeString(),
            ]);
        }

        if(!empty($dto->dateDeadline)) {
            $query->whereBetween('o.date_deadline', [
                Carbon::parse($dto->dateDeadline)->startOfDay()->toDateTimeString(),
                Carbon::parse($dto->dateDeadline)->endOfDay()->toDateTimeString(),
            ]);
        }

        match($dto->sortColumn) {
            'orderNumber' => $query->orderBy('o.id', $dto->sortDir->value),
            'address' => $query->orderBy('a.address', $dto->sortDir->value)
                ->orderBy('a.postal_code', $dto->sortDir->value)
                ->orderBy('c.name', $dto->sortDir->value),
            'priority' => $query->orderBy('pr.id', $dto->sortDir->value),
            'dateCreated' => $query->orderBy('o.created_at', $dto->sortDir->value),
            'dateDeadline' => $query->orderBy('o.date_deadline', $dto->sortDir->value),
            'remarks' => $query->orderBy('ot.remarks', $dto->sortDir->value),
            default => $query->orderBy('o.id', SortDir::DESC->value),
        };

        return $query;
    }
}