<?php

namespace Frooxi\Core\Repositories;

use Frooxi\Core\Eloquent\Repository;

class CountryRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return 'Frooxi\Core\Contracts\Country';
    }
}
