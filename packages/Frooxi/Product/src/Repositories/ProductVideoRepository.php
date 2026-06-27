<?php

namespace Frooxi\Product\Repositories;

class ProductVideoRepository extends ProductMediaRepository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Frooxi\Product\Contracts\ProductVideo';
    }
}
