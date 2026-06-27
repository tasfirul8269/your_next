<?php

namespace Frooxi\DataGrid\Providers;

use Frooxi\Core\Providers\CoreModuleServiceProvider;
use Frooxi\DataGrid\Models\SavedFilter;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        SavedFilter::class,
    ];
}
