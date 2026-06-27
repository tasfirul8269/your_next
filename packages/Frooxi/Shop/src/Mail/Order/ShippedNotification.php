<?php

namespace Frooxi\Shop\Mail\Order;

use Frooxi\Sales\Contracts\Shipment;
use Frooxi\Shop\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ShippedNotification extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public Shipment $shipment) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [
                new Address(
                    $this->shipment->order->customer_email,
                    $this->shipment->order->customer_full_name
                ),
            ],
            subject: trans('shop::app.emails.orders.shipped.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'shop::emails.orders.shipped',
        );
    }
}
