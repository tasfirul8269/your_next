<?php

namespace Frooxi\Sales\Repositories;

use Frooxi\Core\Eloquent\Repository;

/**
 * Order Address Repository
 *
 * @author    Frooxi <hello@frooxi.com>
 * @copyright Frooxi (https://frooxi.com)
 */
class OrderAddressRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return 'Frooxi\Sales\Contracts\OrderAddress';
    }
}
