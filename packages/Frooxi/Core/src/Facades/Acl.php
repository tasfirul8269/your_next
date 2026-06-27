<?php

namespace Frooxi\Core\Facades;

use Frooxi\Core\Acl as BaseAcl;
use Illuminate\Support\Facades\Facade;

class Acl extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BaseAcl::class;
    }
}
