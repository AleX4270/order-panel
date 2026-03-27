<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Enums\NotificationEventType;
use App\Events\OrderCompleted;
use App\Models\NotificationEvent;
use App\Models\User;
use App\Notifications\OrderCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendOrderCompletionNotification implements ShouldQueue {
    public function __construct() {}

    public function handle(OrderCompleted $event): void {
        $users = User::whereHas('notificationSettings', function($q) {
            $q->where('notification_event_id', NotificationEvent::where('symbol', NotificationEventType::ORDER_COMPLETED->value)->first()?->id);
        })
        ->where('id', '<>', $event->order->user_modification_id)
        ->get();

        Notification::send($users, new OrderCompletedNotification($event->order));
    }
}
