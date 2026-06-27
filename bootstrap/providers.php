<?php

use App\Providers\AppServiceProvider;
use Frooxi\Admin\Providers\AdminServiceProvider;
use Frooxi\Attribute\Providers\AttributeServiceProvider;
use Frooxi\Category\Providers\CategoryServiceProvider;
use Frooxi\Checkout\Providers\CheckoutServiceProvider;
use Frooxi\Core\Providers\CoreServiceProvider;
use Frooxi\Core\Providers\EnvValidatorServiceProvider;
use Frooxi\Customer\Providers\CustomerServiceProvider;
use Frooxi\DataGrid\Providers\DataGridServiceProvider;
use Frooxi\Installer\Providers\InstallerServiceProvider;
use Frooxi\Inventory\Providers\InventoryServiceProvider;
use Frooxi\Payment\Providers\PaymentServiceProvider;
use Frooxi\Product\Providers\ProductServiceProvider;
use Frooxi\Sales\Providers\SalesServiceProvider;
use Frooxi\Shipping\Providers\ShippingServiceProvider;
use Frooxi\Shop\Providers\ShopServiceProvider;
use Frooxi\Theme\Providers\ThemeServiceProvider;
use Frooxi\User\Providers\UserServiceProvider;

return [
    /**
     * Application service providers.
     */
    AppServiceProvider::class,

    /**
     * Frooxi's service providers.
     */
    AdminServiceProvider::class,
    AttributeServiceProvider::class,
    CategoryServiceProvider::class,
    CheckoutServiceProvider::class,
    CoreServiceProvider::class,
    EnvValidatorServiceProvider::class,
    CustomerServiceProvider::class,
    DataGridServiceProvider::class,
    InstallerServiceProvider::class,
    InventoryServiceProvider::class,
    PaymentServiceProvider::class,
    ProductServiceProvider::class,
    SalesServiceProvider::class,
    ShippingServiceProvider::class,
    ShopServiceProvider::class,
    ThemeServiceProvider::class,
    UserServiceProvider::class,
];
