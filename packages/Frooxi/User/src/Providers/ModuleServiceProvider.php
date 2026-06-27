<?php

namespace Frooxi\User\Providers;

use Frooxi\Core\Providers\CoreModuleServiceProvider;
use Frooxi\User\Models\Admin;
use Frooxi\User\Models\Role;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        Admin::class,
        Role::class,
    ];
}
