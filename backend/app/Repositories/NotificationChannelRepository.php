<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Dtos\Api\NotificationChannel\NotificationChannelFilterDto;
use App\Enums\SortDir;
use App\Models\NotificationChannel;
use Illuminate\Database\Eloquent\Builder;

class NotificationChannelRepository {
    public function getAll(NotificationChannelFilterDto $dto): Builder {
        
    }
}
