<?php

namespace Frooxi\Core\Repositories;

use Frooxi\Core\Eloquent\Repository;

class CountryStateRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return 'Frooxi\Core\Contracts\CountryState';
    }
}
