<?php

namespace Frooxi\Customer\Repositories;

use Frooxi\Core\Eloquent\Repository;
use Frooxi\Customer\Contracts\Wishlist;

class WishlistRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return Wishlist::class;
    }
}
