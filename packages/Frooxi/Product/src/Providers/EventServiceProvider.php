<?php

namespace Frooxi\Product\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'catalog.product.create.after' => [
            'Frooxi\Product\Listeners\Product@afterCreate',
        ],
        'catalog.product.update.after' => [
            'Frooxi\Product\Listeners\Product@afterUpdate',
        ],
        'catalog.product.delete.before' => [
            'Frooxi\Product\Listeners\Product@beforeDelete',
        ],
        'checkout.order.save.after' => [
            'Frooxi\Product\Listeners\Order@afterCancelOrCreate',
        ],
        'sales.order.cancel.after' => [
            'Frooxi\Product\Listeners\Order@afterCancelOrCreate',
        ],
        'sales.refund.save.after' => [
            'Frooxi\Product\Listeners\Refund@afterCreate',
        ],
    ];
}
