<?php
declare(strict_types=1);

namespace App\Services\Api\Notification;

use App\Dtos\Api\Notification\NotificationFilterDto;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationService {
    public function __construct() {}

    public function index(NotificationFilterDto $dto): Collection {
        $user = User::findOrFail($dto->userId);
        
        if($dto->onlyUnread) {
            return $user->unreadNotifications;
        }
        else {
            return $user->notifications;
        }
    }

    public function markAsRead(string $id): void {
        $notification = DatabaseNotification::findOrFail($id);
        $notification->markAsRead();
    }
}
