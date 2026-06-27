<?php

namespace Frooxi\Checkout\Repositories;

use Frooxi\Core\Eloquent\Repository;

class CartRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return 'Frooxi\Checkout\Contracts\Cart';
    }
}
