<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Letakkan file ini di: app/Mail/OrderConfirmationMail.php
 * Buat juga view: resources/views/emails/order_confirmation.blade.php
 */
class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public array $items;

    public function __construct(Order $order, array $items)
    {
        $this->order = $order;
        $this->items = $items;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Pesanan - ' . $this->order->kode_order . ' | Soto Amih',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_confirmation',
            with: [
                'order' => $this->order,
                'items' => $this->items,
            ],
        );
    }
}