<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderCompletionNotification implements ShouldQueue {
    public function __construct() {}

    public function handle(OrderCompleted $event): void {
        //TODO: Load all users from manager to the top
        //TODO: Dispatch the notification for those users
    }
}
