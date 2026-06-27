<?php

namespace Frooxi\Shop\Providers;

use Frooxi\Shop\Listeners\Customer;
use Frooxi\Shop\Listeners\Invoice;
use Frooxi\Shop\Listeners\Order;
use Frooxi\Shop\Listeners\Refund;
use Frooxi\Shop\Listeners\Shipment;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        /**
         * Customer related events.
         */
        'customer.registration.after' => [
            [Customer::class, 'afterCreated'],
        ],

        'customer.password.update.after' => [
            [Customer::class, 'afterPasswordUpdated'],
        ],

        'customer.subscription.after' => [
            [Customer::class, 'afterSubscribed'],
        ],

        'customer.note.create.after' => [
            [Customer::class, 'afterNoteCreated'],
        ],

        /**
         * Sales related events.
         */
        'checkout.order.save.after' => [
            [Order::class, 'afterCreated'],
        ],

        'sales.order.cancel.after' => [
            [Order::class, 'afterCanceled'],
        ],

        'sales.order.comment.create.after' => [
            [Order::class, 'afterCommented'],
        ],

        'sales.invoice.save.after' => [
            [Invoice::class, 'afterCreated'],
        ],

        'sales.invoice.send_duplicate_email' => [
            [Invoice::class, 'afterCreated'],
        ],

        'sales.shipment.save.after' => [
            [Shipment::class, 'afterCreated'],
        ],

        'sales.refund.save.after' => [
            [Refund::class, 'afterCreated'],
        ],
    ];
}
