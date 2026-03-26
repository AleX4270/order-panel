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
        return new Content(
            markdown: 'mail.incoming-order-deadline-mail',
            with: [
                'date' => Carbon::parse($this->order->date_deadline)->format('d-m-Y'),
            ]
        );
    }

    public function attachments(): array {
        return [];
    }
}
