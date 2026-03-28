<?php
declare(strict_types=1);

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class IncomingOrderDeadlineMail extends Mailable {
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}

    public function envelope(): Envelope {
        return new Envelope(
            subject: __('notifications.orderIncomingDeadlineTitle', ['orderId' => $this->order->id]),
        );
    }

    public function content(): Content {
        $address = $this->order->client->address->city->name . ', ' . $this->order->client->address->address;
        $daysLeft = (int) now()->diffInDays(Carbon::parse($this->order->date_deadline), true);

        return new Content(
            markdown: 'mail.incoming-order-deadline-mail',
            with: [
                'orderId' => $this->order->id,
                'dateDeadline' => Carbon::parse($this->order->date_deadline)->format('d-m-Y'),
                'address' => $address,
                'daysLeft' => $daysLeft,
                'url' => config('app.url'),
            ]
        );
    }

    public function attachments(): array {
        return [];
    }
}
