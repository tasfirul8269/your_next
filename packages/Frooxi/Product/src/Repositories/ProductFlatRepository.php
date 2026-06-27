<?php

namespace Frooxi\Product\Repositories;

use Frooxi\Core\Eloquent\Repository;

class ProductFlatRepository extends Repository
{
    /**
     * Specify model.
     */
    public function model(): string
    {
        return 'Frooxi\Product\Contracts\ProductFlat';
    }
}
