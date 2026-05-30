<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Dtos\Api\OrderRequest\OrderRequestFilterDto;
use App\Enums\SortDir;
use App\Models\Company;
use App\Models\OrderRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class OrderRequestRepository {
    private function getBaseQuery(): Builder {
        return OrderRequest::query()
            ->from('order_requests as orq')
            ->select([
                'orq.id',
                'orq.remarks',
                'orq.ip_address as ipAddress',
                'orq.user_agent as userAgent',
                'orq.consent_given_at as consentGivenAt',
                'orq.created_at as dateCreated',
                'cl.id as clientId',
                'cl.first_name as firstName',
                'cl.last_name as lastName',
                'cl.email',
                'cl.phone_number as phoneNumber',
                'a.address',
                'a.postal_code as postalCode',
                'c.id as cityId',
                'c.name as cityName',
                'p.id as provinceId',
                'p.name as provinceName',
                'co.id as countryId',
                DB::raw("json_build_object(
                    'longitude', ST_X(a.coordinates::geometry),
                    'latitude', ST_Y(a.coordinates::geometry)
                ) as coordinates"),
            ])
            ->selectRaw('ST_Distance(?::geography, a.coordinates::geography) as distance', [Company::current()->address->coordinates])
            ->join('clients as cl', 'cl.id', '=', 'orq.client_id')
            ->join('addresses as a', 'a.id', '=', 'orq.address_id')
            ->join('cities as c', 'c.id', '=', 'a.city_id')
            ->join('provinces as p', 'p.id', '=', 'c.province_id')
            ->join('countries as co', 'co.id', '=', 'p.country_id');
    }

    public function getAll(OrderRequestFilterDto $dto): Builder {
        $query = $this->getBaseQuery()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->whereNull('orq.deleted_at');

        if(!empty($dto->allFields)) {
            $query->where(function($query) use($dto) {
                $term = '%'.$dto->allFields.'%';
                $query->whereLike('a.address', $term)
                    ->orWhereLike('a.postal_code', $term)
                    ->orWhereLike('c.name', $term)
                    ->orWhereLike('cl.email', $term)
                    ->orWhereLike('cl.phone_number', $term)
                    ->orWhereLike('orq.remarks', $term)
                    ->orWhereLike('orq.id', $term)
                    ->orWhereRaw("CONCAT(cl.first_name, ' ', cl.last_name) ILIKE ?", [$term]);
            });
        }

        if(!empty($dto->distanceFromHeadquarters)) {
            $query->whereRaw('ST_DWithin(?::geography, a.coordinates::geography, ?)', [Company::current()->address->coordinates, $dto->distanceFromHeadquarters * 1000]);
        }

        if(!empty($dto->dateCreation)) {
            $query->whereBetween('orq.created_at', [
                Carbon::parse($dto->dateCreation)->startOfDay()->toDateTimeString(),
                Carbon::parse($dto->dateCreation)->endOfDay()->toDateTimeString(),
            ]);
        }

        match($dto->sortColumn) {
            'orderRequestNumber' => $query->orderBy('orq.id', $dto->sortDir->value),
            'remarks' => $query->orderBy('orq.id', $dto->sortDir->value),
            'dateCreated' => $query->orderBy('orq.created_at', $dto->sortDir->value),
            default => $query->orderBy('orq.id', SortDir::DESC->value),
        };

        return $query;
    }
}
