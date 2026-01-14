<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OrderCreatedMail extends Mailable
{
    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Beautylatory] Pesanan #'.$this->order->order_number.' Menunggu Pembayaran',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-created',
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
