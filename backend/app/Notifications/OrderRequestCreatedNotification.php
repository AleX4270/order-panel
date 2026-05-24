<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Dtos\NotificationData;
use App\Enums\NotificationChannelType;
use App\Enums\NotificationEventType;
use App\Mail\OrderRequestCreatedMail;
use App\Models\NotificationChannel;
use App\Models\NotificationEvent;
use App\Models\OrderRequest;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class OrderRequestCreatedNotification extends Notification {
    use Queueable;

    private NotificationData $data;

    public function __construct(
        public OrderRequest $orderRequest,
    ) {
        $this->data = new NotificationData(
            title: __('notifications.orderRequestCreatedTitle'),
            message: __('notifications.orderRequestCreatedMessage', ['orderRequestId' => $this->orderRequest->id]),
            createdAt: Carbon::now()->toISOString(),
        );
    }

    public function via(object $notifiable): array {
        $channels = [];

        $eventId = NotificationEvent::where('symbol', NotificationEventType::ORDER_REQUEST_CREATED->value)->first()?->id;

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
        return new OrderRequestCreatedMail($this->orderRequest)->to($notifiable->email);
    }

    public function toBroadcast(object $notifiable): BroadcastMessage {
        return new BroadcastMessage($this->data->toArray());
    }

    public function toArray(object $notifiable): array {
        return $this->data->toArray();
    }
}
