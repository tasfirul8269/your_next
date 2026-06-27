<?php

use Frooxi\Admin\Providers\ModuleServiceProvider;
use Frooxi\Core\CoreConvention;

return [

    /**
     * The path of the convention file.
     */
    'convention' => CoreConvention::class,

    /**
     * Example:
     *
     * VendorA\ModuleX\Providers\ModuleServiceProvider::class,
     * VendorB\ModuleY\Providers\ModuleServiceProvider::class,
     */
    'modules' => [
        ModuleServiceProvider::class,
        Frooxi\Attribute\Providers\ModuleServiceProvider::class,
        Frooxi\Category\Providers\ModuleServiceProvider::class,
        Frooxi\Checkout\Providers\ModuleServiceProvider::class,
        Frooxi\Core\Providers\ModuleServiceProvider::class,
        Frooxi\Customer\Providers\ModuleServiceProvider::class,
        Frooxi\DataGrid\Providers\ModuleServiceProvider::class,
        Frooxi\Inventory\Providers\ModuleServiceProvider::class,
        Frooxi\Payment\Providers\ModuleServiceProvider::class,
        Frooxi\Product\Providers\ModuleServiceProvider::class,
        Frooxi\Sales\Providers\ModuleServiceProvider::class,
        Frooxi\Shipping\Providers\ModuleServiceProvider::class,
        Frooxi\Shop\Providers\ModuleServiceProvider::class,
        Frooxi\Theme\Providers\ModuleServiceProvider::class,
        Frooxi\User\Providers\ModuleServiceProvider::class,
    ],
];
