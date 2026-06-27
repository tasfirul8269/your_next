<?php

namespace Frooxi\Core\Facades;

use Frooxi\Core\Core as BaseCore;
use Illuminate\Support\Facades\Facade;

class Core extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BaseCore::class;
    }
}
