<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Dtos\NotificationData;
use App\Mail\OrderCompletedMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class OrderCompletedNotification extends Notification {
    use Queueable;

    private NotificationData $data;

    public function __construct(
        public Order $order,
    ) {
        $this->data = new NotificationData(
            title: __('notifications.orderCompletedTitle'),
            message: __('notifications.orderCompletedMessage', ['orderId' => $this->order->id]),
            createdAt: Carbon::now()->toISOString(),
        );
    }

    public function via(object $notifiable): array {
        return ['mail', 'broadcast', 'database'];
    }

    public function toMail(object $notifiable): Mailable {
        return new OrderCompletedMail($this->order)->to($notifiable->email);
    }

    public function toBroadcast(object $notifiable): BroadcastMessage {
        return new BroadcastMessage($this->data->toArray());
    }

    public function toArray(object $notifiable): array {
        return $this->data->toArray();
    }
}
