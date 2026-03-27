<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Dtos\NotificationData;
use App\Enums\NotificationChannelType;
use App\Enums\NotificationEventType;
use App\Mail\OrderCompletedMail;
use App\Models\NotificationChannel;
use App\Models\NotificationEvent;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\BroadcastMessage;
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
        $channels = ['database'];

        $eventId = NotificationEvent::where('symbol', NotificationEventType::ORDER_COMPLETED->value)->first()?->id;

        $userChannelIds = $notifiable->notificationSettings
            ->where('notification_event_id', $eventId)
            ->pluck('notification_channel_id');

        $userChannels = NotificationChannel::whereIn('id', $userChannelIds)->pluck('symbol');

        if ($userChannels->contains(NotificationChannelType::BROADCAST->value)) {
            $channels[] = 'broadcast';
        }

        if ($userChannels->contains(NotificationChannelType::MAIL->value)) {
            $channels[] = 'mail';
        }

        return $channels;
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
