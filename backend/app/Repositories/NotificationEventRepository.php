<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Dtos\Api\NotificationEvent\NotificationEventFilterDto;
use App\Enums\SortDir;
use App\Models\NotificationEvent;
use Illuminate\Database\Eloquent\Builder;

class NotificationEventRepository {
    public function getAll(NotificationEventFilterDto $dto): Builder {

    }
}
