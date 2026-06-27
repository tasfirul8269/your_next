<?php

namespace Frooxi\Category\Providers;

use Frooxi\Category\Models\Category;
use Frooxi\Category\Models\CategoryTranslation;
use Frooxi\Core\Providers\CoreModuleServiceProvider;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        Category::class,
        CategoryTranslation::class,
    ];
}
