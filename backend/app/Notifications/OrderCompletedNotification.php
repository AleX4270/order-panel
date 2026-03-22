<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Dtos\NotificationData;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class OrderCompletedNotification extends Notification {
    use Queueable;

    private NotificationData $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order,
    ) {
        $this->data = new NotificationData(
            title: __('notifications.orderCompletedTitle', ['orderId' => $this->order->id]),
            message: __('notifications.orderCompletedMessage', ['orderId' => $this->order->id]),
            createdAt: Carbon::now()->toISOString(),
        );
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array {
        //! Temporarily disable the mail channel
        // return ['mail', 'broadcast', 'database'];
        return ['broadcast', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage {
        return (new MailMessage)->markdown('mail.order-completed-notification');
    }

    public function toBroadcast(object $notifiable): BroadcastMessage {
        return new BroadcastMessage($this->data->toArray());
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array {
        return $this->data->toArray();
    }
}
