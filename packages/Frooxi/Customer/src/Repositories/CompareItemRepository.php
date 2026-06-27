<?php

namespace Frooxi\Customer\Repositories;

use Frooxi\Core\Eloquent\Repository;
use Frooxi\Customer\Contracts\CompareItem;

class CompareItemRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return CompareItem::class;
    }
}
