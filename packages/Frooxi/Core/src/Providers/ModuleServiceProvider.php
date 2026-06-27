<?php

namespace Frooxi\Core\Providers;

use Frooxi\Core\Models\Channel;
use Frooxi\Core\Models\CoreConfig;
use Frooxi\Core\Models\Country;
use Frooxi\Core\Models\CountryState;
use Frooxi\Core\Models\CountryStateTranslation;
use Frooxi\Core\Models\CountryTranslation;
use Frooxi\Core\Models\Currency;
use Frooxi\Core\Models\CurrencyExchangeRate;
use Frooxi\Core\Models\Locale;
use Frooxi\Core\Models\SubscribersList;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        Channel::class,
        CoreConfig::class,
        Country::class,
        CountryState::class,
        CountryStateTranslation::class,
        CountryTranslation::class,
        Currency::class,
        CurrencyExchangeRate::class,
        Locale::class,
        SubscribersList::class,
    ];
}
