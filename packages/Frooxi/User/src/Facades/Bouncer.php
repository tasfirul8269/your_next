<?php

namespace Frooxi\User\Facades;

use Frooxi\User\Bouncer as BaseBouncer;
use Illuminate\Support\Facades\Facade;

class Bouncer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BaseBouncer::class;
    }
}
