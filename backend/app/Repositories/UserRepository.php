<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Dtos\Api\User\UserFilterDto;
use App\Enums\SortDir;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class UserRepository {
    private function getBaseQuery(): Builder {
        return User::query()
            ->from('users as u')
            ->select([
                'u.id',
                'u.name',
                'u.first_name as firstName',
                'u.last_name as lastName',
                'u.email',
                'u.created_at as dateCreated',
                'u.updated_at as dateUpdated',
            ]);
    }

    public function getAll(UserFilterDto $dto): Builder {
        $query = $this->getBaseQuery();

        if(!empty($dto->allFields)) {
            $query->where(function($query) use($dto) {
                $query->whereLike('u.name', '%'.$dto->allFields.'%')
                    ->orWhereLike('u.first_name', '%'.$dto->allFields.'%')
                    ->orWhereLike('u.last_name', '%'.$dto->allFields.'%')
                    ->orWhereLike('u.email', '%'.$dto->allFields.'%')
                    ->orWhere('u.id', $dto->allFields);
            });
        }

        if(!empty($dto->dateCreated)) {
            $query->whereBetween('u.created_at', [
                Carbon::parse($dto->dateCreated)->startOfDay()->toDateTimeString(),
                Carbon::parse($dto->dateCreated)->endOfDay()->toDateTimeString(),
            ]);
        }

        if(!empty($dto->dateUpdated)) {
            $query->whereBetween('u.updated_at', [
                Carbon::parse($dto->dateUpdated)->startOfDay()->toDateTimeString(),
                Carbon::parse($dto->dateUpdated)->endOfDay()->toDateTimeString(),
            ]);
        }

        match($dto->sortColumn) {
            'userNumber' => $query->orderBy('u.id', $dto->sortDir->value),
            'name' => $query->orderBy('u.name', $dto->sortDir->value)
                ->orderBy('u.first_name', $dto->sortDir->value)
                ->orderBy('u.last_name', $dto->sortDir->value),
            'email' => $query->orderBy('u.email', $dto->sortDir->value),
            'dateCreated' => $query->orderBy('u.created_at', $dto->sortDir->value),
            'dateUpdated' => $query->orderBy('u.updated_at', $dto->sortDir->value),
            default => $query->orderBy('u.id', SortDir::DESC->value),
        };

        return $query;
    }

    // public function getOne(int $orderId): Builder {
    //     $query = $this->getBaseQuery();

    //     $query->where('o.id', $orderId);

    //     return $query;
    // }
}