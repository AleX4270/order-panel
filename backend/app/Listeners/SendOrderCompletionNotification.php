<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Enums\RoleType;
use App\Events\OrderCompleted;
use App\Models\User;
use App\Notifications\OrderCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendOrderCompletionNotification implements ShouldQueue {
    public function __construct() {}

    public function handle(OrderCompleted $event): void {
        // This is a temporary solution only. The desired way of doing this is letting the user
        // "check" the type of events it wants to be notified about
        $notifiableRoles = [RoleType::ADMIN->value, RoleType::MANAGER->value];

        $users = User::with('roles')->get()->filter(
            fn($user) => $user->roles()->whereIn('name', $notifiableRoles) 
        );

        Notification::send($users, new OrderCompletedNotification($event->order));
    }
}
