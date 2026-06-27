<?php

namespace Frooxi\Admin\Providers;

use Frooxi\Core\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load auth routes WITHOUT admin middleware (login, forgot password, etc.)
        Route::middleware(['web', PreventRequestsDuringMaintenance::class])
            ->group(__DIR__.'/../Routes/auth-routes.php');

        // Load protected admin routes WITH admin middleware
        Route::middleware(['web', 'admin', PreventRequestsDuringMaintenance::class])
            ->group(__DIR__.'/../Routes/web-protected.php');

        // Load API routes
        Route::prefix('api/v1/admin')
            ->middleware(['api'])
            ->group(__DIR__.'/../Routes/api.php');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'admin');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'admin');

        Blade::anonymousComponentPath(__DIR__.'/../Resources/views/components', 'admin');

        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/menu.php',
            'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/acl.php',
            'acl'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/system.php',
            'core'
        );
    }
}
