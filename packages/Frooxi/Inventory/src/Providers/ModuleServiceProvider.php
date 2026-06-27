<?php

namespace Frooxi\Inventory\Providers;

use Frooxi\Core\Providers\CoreModuleServiceProvider;
use Frooxi\Inventory\Models\InventorySource;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        InventorySource::class,
    ];
}
