<?php

namespace Frooxi\Core\Providers;

use Elastic\Elasticsearch\Client;
use Frooxi\Core\Console\Commands\ExchangeRateUpdate;
use Frooxi\Core\Console\Commands\InvoiceOverdueCron;
use Frooxi\Core\Console\Commands\NextOutfitVersion;
use Frooxi\Core\Console\Commands\TranslationsChecker;
use Frooxi\Core\Exceptions\Handler;
use Frooxi\Core\Facades\ElasticSearch;
use Frooxi\Core\View\Compilers\BladeCompiler;
use Frooxi\Theme\ViewRenderEventManager;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Console\DownCommand;
use Illuminate\Foundation\Console\UpCommand;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        include __DIR__.'/../Http/helpers.php';

        $this->registerCommands();

        $this->registerOverrides();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'core');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'core');

        Event::listen('frooxi.shop.layout.body.after', static function (ViewRenderEventManager $viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('core::blade.tracer.style');
        });

        Event::listen('frooxi.admin.layout.head', static function (ViewRenderEventManager $viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('core::blade.tracer.style');
        });

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('invoice:cron')->dailyAt('3:00');

            $this->registerExchangeRateSchedule($schedule);
        });

        $this->app->register(EventServiceProvider::class);
        $this->app->register(DynamicSmtpServiceProvider::class);
    }

    /**
     * Register the console commands of this package.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                NextOutfitVersion::class,
                ExchangeRateUpdate::class,
                InvoiceOverdueCron::class,
                TranslationsChecker::class,
            ]);
        }
    }

    /**
     * Register the exchange rate update schedule based on core configuration.
     */
    protected function registerExchangeRateSchedule(Schedule $schedule): void
    {
        try {
            if (! core()->getConfigData('general.exchange_rates.schedule.enabled')) {
                return;
            }

            $frequency = core()->getConfigData('general.exchange_rates.schedule.frequency') ?: 'daily';

            $time = core()->getConfigData('general.exchange_rates.schedule.time') ?: '00:00';

            $command = $schedule->command('exchange-rate:update');

            match ($frequency) {
                'weekly' => $command->weeklyOn(1, $time),
                'monthly' => $command->monthlyOn(1, $time),
                default => $command->dailyAt($time),
            };
        } catch (\Exception) {
            // Silently skip when database is not yet available (e.g., during installation).
        }
    }

    /**
     * Register the overrides.
     */
    protected function registerOverrides(): void
    {
        $this->app->extend(
            UpCommand::class,
            fn () => new \Frooxi\Core\Console\Commands\UpCommand
        );

        $this->app->extend(
            DownCommand::class,
            fn () => new \Frooxi\Core\Console\Commands\DownCommand
        );

        $this->app->bind(
            ExceptionHandler::class,
            Handler::class
        );

        $this->app->bind(
            PreventRequestsDuringMaintenance::class,
            fn ($app) => new \Frooxi\Core\Http\Middleware\PreventRequestsDuringMaintenance($app)
        );

        $this->app->singleton(
            Client::class,
            fn () => ElasticSearch::getFacadeApplication()->connection()
        );

        $this->app->singleton(
            'blade.compiler',
            fn ($app) => new BladeCompiler($app['files'], $app['config']['view.compiled'])
        );
    }
}
