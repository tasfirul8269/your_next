<?php

namespace Frooxi\Sales\Repositories;

use Frooxi\Core\Eloquent\Repository;

/**
 * Order Address Repository
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Frooxi Software Pvt Ltd (http://www.webkul.com)
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
