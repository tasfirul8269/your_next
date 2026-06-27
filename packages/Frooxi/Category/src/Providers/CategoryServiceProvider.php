<?php

namespace Frooxi\Category\Providers;

use Frooxi\Category\Models\CategoryProxy;
use Frooxi\Category\Observers\CategoryObserver;
use Illuminate\Support\ServiceProvider;

class CategoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        CategoryProxy::observe(CategoryObserver::class);
    }
}
