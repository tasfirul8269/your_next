<?php

namespace Frooxi\Customer\Providers;

use Frooxi\Core\Providers\CoreModuleServiceProvider;
use Frooxi\Customer\Models\CompareItem;
use Frooxi\Customer\Models\Customer;
use Frooxi\Customer\Models\CustomerAddress;
use Frooxi\Customer\Models\CustomerGroup;
use Frooxi\Customer\Models\CustomerNote;
use Frooxi\Customer\Models\Wishlist;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        CompareItem::class,
        Customer::class,
        CustomerAddress::class,
        CustomerGroup::class,
        CustomerNote::class,
        Wishlist::class,
    ];
}
