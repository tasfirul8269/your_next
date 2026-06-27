<?php

namespace Frooxi\Shipping\Carriers;

use Frooxi\Checkout\Facades\Cart;
use Frooxi\Checkout\Models\CartShippingRate;
use Frooxi\Shipping\Repositories\ShippingMethodRepository;

class CustomShipping extends AbstractShipping
{
    /**
     * Shipping method carrier code.
     *
     * @var string
     */
    protected $code = 'customshipping';

    /**
     * Create a new instance.
     */
    public function __construct(
        protected ShippingMethodRepository $shippingMethodRepository
    ) {}

    /**
     * Calculate rate for custom shipping.
     *
     * @return array
     */
    public function calculate()
    {
        if (! $this->isAvailable()) {
            return [];
        }

        $rates = [];
        $shippingMethods = $this->shippingMethodRepository->getActiveMethods();

        foreach ($shippingMethods as $method) {
            $rate = $this->createShippingRate($method);
            if ($rate) {
                $rates[] = $rate;
            }
        }

        return $rates;
    }

    /**
     * Create shipping rate from database method.
     */
    protected function createShippingRate($method): ?CartShippingRate
    {
        $cart = Cart::getCart();

        $cartShippingRate = new CartShippingRate;

        $cartShippingRate->carrier = $this->getCode();
        $cartShippingRate->carrier_title = 'Custom Shipping';
        $cartShippingRate->method = 'customshipping_'.$method->id;
        $cartShippingRate->method_title = $method->name;
        $cartShippingRate->method_description = $method->description ?? '';
        $cartShippingRate->price = core()->convertPrice($method->price);
        $cartShippingRate->base_price = $method->price;

        return $cartShippingRate;
    }

    /**
     * Get shipping method code.
     */
    public function getMethod(): string
    {
        return 'customshipping';
    }
}
