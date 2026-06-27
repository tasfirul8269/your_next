<?php

namespace Frooxi\DataGrid\Repositories;

use Frooxi\Core\Eloquent\Repository;
use Frooxi\DataGrid\Contracts\SavedFilter;

class SavedFilterRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return SavedFilter::class;
    }
}
