<?php

namespace Frooxi\User\Repositories;

use Frooxi\Core\Eloquent\Repository;

class RoleRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Frooxi\User\Contracts\Role';
    }
}
