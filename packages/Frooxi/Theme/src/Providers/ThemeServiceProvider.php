<?php

namespace Frooxi\Theme\Providers;

use Frooxi\Theme\ThemeViewFinder;
use Frooxi\Theme\ViewRenderEventManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/../Http/helpers.php';

        $this->app->singleton('view.finder', function ($app) {
            return new ThemeViewFinder(
                $app['files'],
                $app['config']['view.paths'],
                null
            );
        });

        $this->app->singleton(ViewRenderEventManager::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        Blade::directive('yournextVite', function ($expression) {
            return "<?php echo themes()->setYournextVite({$expression})->toHtml(); ?>";
        });

        Blade::directive('frooxiVite', function ($expression) {
            return "<?php echo themes()->setYournextVite({$expression})->toHtml(); ?>";
        });
    }
}
