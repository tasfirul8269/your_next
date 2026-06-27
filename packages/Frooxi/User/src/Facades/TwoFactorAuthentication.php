<?php

namespace Frooxi\User\Facades;

use Frooxi\User\TwoFactorAuthentication as BaseTwoFactorAuthentication;
use Illuminate\Support\Facades\Facade;

class TwoFactorAuthentication extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BaseTwoFactorAuthentication::class;
    }
}
