<?php

namespace Frooxi\Customer\Repositories;

use Frooxi\Core\Eloquent\Repository;

class CustomerGroupRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Frooxi\Customer\Contracts\CustomerGroup';
    }
}
