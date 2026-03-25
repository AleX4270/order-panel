<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Dtos\NotificationData;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncomingOrderDeadlineNotification extends Notification implements ShouldQueue {
    use Queueable;

    private NotificationData $data;

    public function __construct(
        public Order $order,
    ) {
        $this->data = new NotificationData(
            title: __('notifications.orderIncomingDeadlineTitle'),
            message: __('notifications.orderIncomingDeadlineMessage', ['orderId' => $this->order->id, 'date' => Carbon::parse($this->order->date_deadline)->format('d-m-Y')]),
            createdAt: Carbon::now()->toISOString(),
        );
    }

    public function via(object $notifiable): array {
        //! Temporarily disable the mail channel
        // return ['mail', 'broadcast', 'database'];
        return ['broadcast', 'database'];
    }

    public function toMail(object $notifiable): MailMessage {
        return (new MailMessage)->markdown('mail.incoming-order-deadline-notification');
    }

    public function toBroadcast(object $notifiable): BroadcastMessage {
        return new BroadcastMessage($this->data->toArray());
    }

    public function toArray(object $notifiable): array {
        return $this->data->toArray();
    }
}
