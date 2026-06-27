<?php

namespace Frooxi\Customer\Repositories;

use Frooxi\Core\Eloquent\Repository;

class CustomerNoteRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return 'Frooxi\Customer\Contracts\CustomerNote';
    }
}
