<?php

namespace Frooxi\Sales\Repositories;

use Frooxi\Core\Eloquent\Repository;
use Frooxi\Sales\Contracts\OrderTransaction;

class OrderTransactionRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return OrderTransaction::class;
    }
}
