<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Dtos\NotificationData;
use App\Enums\NotificationChannelType;
use App\Enums\NotificationEventType;
use App\Mail\IncomingOrderDeadlineMail;
use App\Models\NotificationChannel;
use App\Models\NotificationEvent;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\BroadcastMessage;
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
        $channels = [];

        $eventId = NotificationEvent::where('symbol', NotificationEventType::INCOMING_ORDER_DEADLINE->value)->first()?->id;

        $userChannelIds = $notifiable->notificationSettings
            ->where('notification_event_id', $eventId)
            ->pluck('notification_channel_id');

        $userChannels = NotificationChannel::whereIn('id', $userChannelIds)->pluck('symbol');

        if ($userChannels->contains(NotificationChannelType::BROADCAST->value)) {
            $channels[] = 'broadcast';
            $channels[] = 'database';
        }

        if ($userChannels->contains(NotificationChannelType::MAIL->value)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): Mailable {
        return new IncomingOrderDeadlineMail($this->order)->to($notifiable->email);
    }

    public function toBroadcast(object $notifiable): BroadcastMessage {
        return new BroadcastMessage($this->data->toArray());
    }

    public function toArray(object $notifiable): array {
        return $this->data->toArray();
    }
}
