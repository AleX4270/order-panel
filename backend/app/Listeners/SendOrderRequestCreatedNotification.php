<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Enums\NotificationEventType;
use App\Events\OrderRequestCreated;
use App\Models\NotificationEvent;
use App\Models\User;
use App\Notifications\OrderRequestCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendOrderRequestCreatedNotification implements ShouldQueue {
    public function __construct() {}

    public function handle(OrderRequestCreated $event): void {
        $users = User::whereHas('notificationSettings', function($q) {
            $q->where('notification_event_id', NotificationEvent::where('symbol', NotificationEventType::ORDER_REQUEST_CREATED->value)->first()?->id);
        })
        ->get();

        Notification::send($users, new OrderRequestCreatedNotification($event->orderRequest));
    }
}
