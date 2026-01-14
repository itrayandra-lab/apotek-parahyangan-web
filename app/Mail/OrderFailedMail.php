<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OrderFailedMail extends Mailable
{
    public function __construct(
        public Order $order,
        public string $reason = 'expired'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->reason === 'expired'
            ? '[Beautylatory] Pembayaran Kedaluwarsa - Pesanan #'.$this->order->order_number
            : '[Beautylatory] Pembayaran Gagal - Pesanan #'.$this->order->order_number;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-failed',
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
