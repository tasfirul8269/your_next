<?php

use Frooxi\User\Bouncer;
use Frooxi\User\Facades\Bouncer as BouncerFacade;
use Frooxi\User\Facades\TwoFactorAuthentication as TwoFactorAuthenticationFacade;

if (! function_exists('bouncer')) {
    /**
     * Bouncer helper.
     *
     * @return Bouncer
     */
    function bouncer()
    {
        return BouncerFacade::getFacadeRoot();
    }
}

if (! function_exists('two_factor_authentication')) {
    function two_factor_authentication()
    {
        return TwoFactorAuthenticationFacade::getFacadeRoot();
    }
}
