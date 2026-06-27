<?php

namespace Frooxi\Inventory\Repositories;

use Frooxi\Core\Eloquent\Repository;

class InventorySourceRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Frooxi\Inventory\Contracts\InventorySource';
    }
}
