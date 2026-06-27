<?php

namespace Frooxi\Core\Facades;

use Frooxi\Core\Menu as BaseMenu;
use Illuminate\Support\Facades\Facade;

class Menu extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BaseMenu::class;
    }
}
