<?php
declare(strict_types=1);

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class OrderCompletedMail extends Mailable {
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}

    public function envelope(): Envelope {
        return new Envelope(
            subject: __('notifications.orderCompletedTitle', ['orderId' => $this->order->id]),
        );
    }

    public function content(): Content {
        $user = User::findOrFail($this->order->user_modification_id);
        $address = $this->order->client->address->city->name . ', ' . $this->order->client->address->address;

        return new Content(
            markdown: 'mail.order-completed-mail',
            with: [
                'orderId' => $this->order->id,
                'url' => config('app.url'),
                'dateModification' => Carbon::parse($this->order->updated_at)->format('d-m-Y H:m'),
                'username' => $user->name,
                'address' => $address,
            ]
        );
    }

    public function attachments(): array {
        return [];
    }
}
