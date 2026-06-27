<?php

namespace Frooxi\Core\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Prettus\Repository\Events\RepositoryEntityCreated' => [
            'Frooxi\Core\Listeners\CleanCacheRepository',
        ],
        'Prettus\Repository\Events\RepositoryEntityUpdated' => [
            'Frooxi\Core\Listeners\CleanCacheRepository',
        ],
        'Prettus\Repository\Events\RepositoryEntityDeleted' => [
            'Frooxi\Core\Listeners\CleanCacheRepository',
        ],
    ];
}
