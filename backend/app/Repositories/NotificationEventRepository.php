<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Dtos\Api\NotificationEvent\NotificationEventFilterDto;
use App\Enums\SortDir;
use App\Models\NotificationEvent;
use Illuminate\Database\Eloquent\Builder;

class NotificationEventRepository {
    public function getAll(NotificationEventFilterDto $dto): Builder {
        $query = NotificationEvent::query()
            ->with(['translations' => function ($q) {
                $q->whereHas('language', fn ($q) => $q->where('symbol', app()->getLocale()));
            }]);

        match($dto->sortColumn) {
            default => $query->orderBy('notification_events.id', SortDir::ASC->value),
        };

        return $query;
    }
}
