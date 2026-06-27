<?php

namespace Frooxi\Customer\Repositories;

use Frooxi\Core\Eloquent\Repository;
use Frooxi\Customer\Contracts\CustomerAddress;

class CustomerAddressRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return CustomerAddress::class;
    }
}
