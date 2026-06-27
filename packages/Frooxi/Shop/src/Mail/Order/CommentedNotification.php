<?php

namespace Frooxi\Shop\Mail\Order;

use Frooxi\Sales\Contracts\OrderComment;
use Frooxi\Shop\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CommentedNotification extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public OrderComment $comment) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [
                new Address($this->comment->order->customer_email, $this->comment->order->customer_full_name),
            ],
            subject: trans('shop::app.emails.orders.commented.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'shop::emails.orders.commented',
        );
    }
}
