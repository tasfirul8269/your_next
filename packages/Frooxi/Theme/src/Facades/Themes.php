<?php

namespace Frooxi\Theme\Facades;

use Frooxi\Theme\Themes as BaseThemes;
use Illuminate\Support\Facades\Facade;

class Themes extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BaseThemes::class;
    }
}
