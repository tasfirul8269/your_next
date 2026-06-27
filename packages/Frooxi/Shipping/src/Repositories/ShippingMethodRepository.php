<?php

namespace Frooxi\Shipping\Repositories;

use Frooxi\Core\Eloquent\Repository;

class ShippingMethodRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return 'Frooxi\Shipping\Models\ShippingMethod';
    }

    /**
     * Get active shipping methods.
     */
    public function getActiveMethods()
    {
        return $this->model
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get();
    }
}
