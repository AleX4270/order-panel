<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\NotificationEventType;
use App\Models\NotificationEvent;
use App\Models\Order;
use App\Models\User;
use App\Notifications\IncomingOrderDeadlineNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendIncomingDeadlineNotifications extends Command {
    protected $signature = 'order:send-incoming-deadline-notifications';
    protected $description = 'Detects incoming deadlines for all orders and sends proper notifications';

    public function handle() {
        $users = User::whereHas('notificationSettings', function($q) {
            $q->where('notification_event_id', NotificationEvent::where('symbol', NotificationEventType::INCOMING_ORDER_DEADLINE->value)->first()?->id);
        })
        ->get();

        $orders = Order::query()->whereDate('date_deadline', Carbon::now()->addDays(7)->toDateString())->get();

        foreach($orders as $order) {
            Notification::send($users, new IncomingOrderDeadlineNotification($order));
        }
    }
}
