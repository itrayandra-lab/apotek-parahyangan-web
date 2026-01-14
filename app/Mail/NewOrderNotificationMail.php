<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewOrderNotificationMail extends Mailable
{
    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Beautylatory] Order Baru #'.$this->order->order_number.' - Rp '.number_format($this->order->total, 0, ',', '.'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-order-notification',
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
