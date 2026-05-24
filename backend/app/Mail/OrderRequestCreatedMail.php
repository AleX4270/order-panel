<?php
declare(strict_types=1);

namespace App\Mail;

use App\Models\OrderRequest;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderRequestCreatedMail extends Mailable {
    use Queueable, SerializesModels;

    public function __construct(
        public OrderRequest $orderRequest,
    ) {}

    public function envelope(): Envelope {
        return new Envelope(
            subject: __('mail.orderCompletedSubject', ['orderRequestId' => $this->orderRequest->id]),
        );
    }

    public function content(): Content {
        $address = $this->orderRequest->address->postal_code . ', ' . $this->orderRequest->address->city->name . ', ' . $this->orderRequest->address->address;

        return new Content(
            markdown: 'mail.order-request-created-mail',
            with: [
                'orderRequestId' => $this->orderRequest->id,
                'address' => $address,
                'dateCreated' => Carbon::parse($this->orderRequest->created_at)->format('d-m-Y H:m'),
                'remarks' => !empty($this->orderRequest->remarks) ? $this->orderRequest->remarks : '-',
                'url' => config('app.frontendUrl'),
            ]
        );
    }

    public function attachments(): array {
        return [];
    }
}
