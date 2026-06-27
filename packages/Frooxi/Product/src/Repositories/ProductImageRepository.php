<?php

namespace Frooxi\Product\Repositories;

class ProductImageRepository extends ProductMediaRepository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Frooxi\Product\Contracts\ProductImage';
    }
}
