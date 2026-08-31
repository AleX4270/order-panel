<?php
declare(strict_types=1);

namespace App\Services\Api\Notification;

use App\Dtos\Api\Notification\NotificationFilterDto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class NotificationService {
    public function __construct() {}

    public function index(NotificationFilterDto $dto): Collection {
        $user = Auth::user();

        if($dto->onlyUnread) {
            return $user->unreadNotifications;
        }
        else {
            return $user->notifications;
        }
    }

    public function markAsRead(string $id): void {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
    }
}
