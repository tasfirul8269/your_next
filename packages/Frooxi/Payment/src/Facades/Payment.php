<?php

namespace Frooxi\Payment\Facades;

use Frooxi\Payment\Payment as BasePayment;
use Illuminate\Support\Facades\Facade;

class Payment extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BasePayment::class;
    }
}
