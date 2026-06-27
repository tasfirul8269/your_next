<?php

namespace Frooxi\Theme\Providers;

use Frooxi\Core\Providers\CoreModuleServiceProvider;
use Frooxi\Theme\Models\ThemeCustomization;
use Frooxi\Theme\Models\ThemeCustomizationTranslation;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Define the models
     *
     * @var array
     */
    protected $models = [
        ThemeCustomization::class,
        ThemeCustomizationTranslation::class,
    ];
}
