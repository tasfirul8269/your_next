<?php

use Frooxi\Checkout\Facades\Cart;

if (! function_exists('cart')) {
    /**
     * Cart helper.
     *
     * @return Frooxi\Checkout\Cart
     */
    function cart()
    {
        return Cart::getFacadeRoot();
    }
}
