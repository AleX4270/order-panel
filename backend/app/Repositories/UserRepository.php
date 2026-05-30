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
                'u.is_internal as isInternal',
            ]);
    }
    
    private function applyRolesQuery(Builder $query): Builder {
        return $query->with([
            'roles:id,name',
            'roles.translation'
        ]);
    }

    private function applyNotificationSettingsQuery(Builder $query): Builder {
        return $query->with([
            'notificationSettings'
        ]);
    }

    public function getAll(UserFilterDto $dto): Builder {
        $query = $this->getBaseQuery()
            ->where('u.is_active', 1);

        $this->applyRolesQuery($query);

        if(!empty($dto->allFields)) {
            $query->where(function($query) use($dto) {
                $term = '%'.$dto->allFields.'%';
                $query->whereLike('u.name', $term)
                    ->orWhereLike('u.email', $term)
                    ->orWhereLike('u.id', $term)
                    ->orWhereRaw("CONCAT(u.first_name, ' ', u.last_name) ILIKE ?", [$term]);
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
            default => $query->orderBy('u.id', SortDir::ASC->value),
        };

        return $query;
    }

    public function getOne(int $userId): Builder {
        $query = $this->getBaseQuery();
        $this->applyRolesQuery($query);
        $this->applyNotificationSettingsQuery($query);

        $query->where('u.id', $userId);

        return $query;
    }
}