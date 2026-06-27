<?php

namespace Frooxi\Core\Repositories;

use Frooxi\Core\Eloquent\Repository;

class ExchangeRateRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Frooxi\Core\Contracts\CurrencyExchangeRate';
    }
}
