<?php

use Frooxi\Shipping\Facades\Shipping;

if (! function_exists('shipping')) {
    /**
     * Shipping helper.
     *
     * @return Frooxi\Shipping\Shipping
     */
    function shipping()
    {
        return Shipping::getFacadeRoot();
    }
}
