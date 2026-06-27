<?php

namespace Frooxi\Shipping\Facades;

use Frooxi\Shipping\Shipping as BaseShipping;
use Illuminate\Support\Facades\Facade;

class Shipping extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BaseShipping::class;
    }
}
