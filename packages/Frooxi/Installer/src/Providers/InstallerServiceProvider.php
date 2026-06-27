<?php

namespace Frooxi\Installer\Providers;

use Frooxi\Installer\Console\Commands\Installer as InstallerCommand;
use Frooxi\Installer\Http\Middleware\CanInstall;
use Frooxi\Installer\Http\Middleware\Locale;
use Frooxi\Installer\Http\Middleware\UseFileSession;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class InstallerServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $router->middlewareGroup('install', [CanInstall::class]);

        $router->aliasMiddleware('installer_locale', Locale::class);

        $router->aliasMiddleware('installer_file_session', UseFileSession::class);

        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'installer');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'installer');

        Event::listen('frooxi.installed', 'Frooxi\Installer\Listeners\Installer@installed');
    }

    /**
     * Register the Installer Commands of this package.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallerCommand::class,
            ]);
        }
    }
}
