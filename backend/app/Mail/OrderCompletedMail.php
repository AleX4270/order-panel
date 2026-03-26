<?php
declare(strict_types=1);

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

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
        return new Content(
            markdown: 'mail.order-completed-mail',
        );
    }

    public function attachments(): array {
        return [];
    }
}
