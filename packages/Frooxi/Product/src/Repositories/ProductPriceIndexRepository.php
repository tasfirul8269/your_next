<?php

namespace Frooxi\Product\Repositories;

use Frooxi\Core\Eloquent\Repository;

class ProductPriceIndexRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Frooxi\Product\Contracts\ProductPriceIndex';
    }
}
