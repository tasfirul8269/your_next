<?php

namespace Frooxi\Sales\Repositories;

use Frooxi\Core\Eloquent\Repository;

class OrderCommentRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return 'Frooxi\Sales\Contracts\OrderComment';
    }
}
