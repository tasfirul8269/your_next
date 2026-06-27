<?php

namespace Frooxi\Customer\Providers;

use Frooxi\Customer\Facades\Captcha;
use Frooxi\Customer\Services\OtpService;
use Frooxi\Customer\Services\SmsService;
use Frooxi\Customer\Services\SslWirelessSmsService;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class CustomerServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsService::class, SslWirelessSmsService::class);

        $this->app->singleton(OtpService::class);
    }

    /**
     * Bootstrap application services.
     *
     * @param  Router  $router
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'customer');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'customer');

        $this->app['validator']->extend('captcha', function ($attribute, $value, $parameters) {
            return Captcha::getFacadeRoot()->validateResponse($value);
        });
    }
}
