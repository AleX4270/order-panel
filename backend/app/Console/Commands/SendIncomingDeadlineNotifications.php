<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\RoleType;
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
        // This is a temporary solution only. The desired way of doing this is letting the user
        // "check" the type of events it wants to be notified about
        $notifiableRoles = [RoleType::ADMIN->value, RoleType::MANAGER->value];
        $users = User::with('roles')->get()->filter(
            fn($user) => $user->roles()->whereIn('name', $notifiableRoles) 
        );

        $orders = Order::query()->whereDate('date_deadline', Carbon::now()->addDays(7)->toDateString())->get();

        foreach($orders as $order) {
            Notification::send($users, new IncomingOrderDeadlineNotification($order));
        }

        logger(Carbon::now()->addDays(7)->toDateString());
        logger([$orders]);
    }
}
