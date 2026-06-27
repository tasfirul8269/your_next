<?php

namespace Frooxi\Checkout\Providers;

use Frooxi\Checkout\Models\Cart;
use Frooxi\Checkout\Models\CartAddress;
use Frooxi\Checkout\Models\CartItem;
use Frooxi\Checkout\Models\CartPayment;
use Frooxi\Checkout\Models\CartShippingRate;
use Frooxi\Core\Providers\CoreModuleServiceProvider;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        Cart::class,
        CartAddress::class,
        CartItem::class,
        CartPayment::class,
        CartShippingRate::class,
    ];
}
