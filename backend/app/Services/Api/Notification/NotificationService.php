<?php
declare(strict_types=1);

namespace App\Services\Api\Notification;

use App\Dtos\Api\Notification\NotificationFilterDto;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService {
    public function __construct() {}

    public function index(NotificationFilterDto $dto): Collection {
        $user = User::findOrFail($dto->userId);
        return $user->notifications;
    }
}
