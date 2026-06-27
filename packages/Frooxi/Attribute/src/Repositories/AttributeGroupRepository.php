<?php

namespace Frooxi\Attribute\Repositories;

use Frooxi\Core\Eloquent\Repository;

class AttributeGroupRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return 'Frooxi\Attribute\Contracts\AttributeGroup';
    }
}
