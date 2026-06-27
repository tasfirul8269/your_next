<?php

namespace Frooxi\Attribute\Providers;

use Frooxi\Attribute\Models\Attribute;
use Frooxi\Attribute\Models\AttributeFamily;
use Frooxi\Attribute\Models\AttributeGroup;
use Frooxi\Attribute\Models\AttributeOption;
use Frooxi\Attribute\Models\AttributeOptionTranslation;
use Frooxi\Attribute\Models\AttributeTranslation;
use Frooxi\Core\Providers\CoreModuleServiceProvider;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        Attribute::class,
        AttributeFamily::class,
        AttributeGroup::class,
        AttributeOption::class,
        AttributeOptionTranslation::class,
        AttributeTranslation::class,
    ];
}
