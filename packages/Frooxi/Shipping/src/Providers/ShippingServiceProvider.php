<?php

namespace Frooxi\Shipping\Providers;

use Illuminate\Support\ServiceProvider;

class ShippingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/../Http/helpers.php';

        $this->registerConfig();
        $this->registerViews();
        $this->registerTranslations();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/carriers.php', 'carriers'
        );
    }

    /**
     * Register package views.
     *
     * @return void
     */
    protected function registerViews()
    {
        $this->loadViewsFrom(
            dirname(__DIR__).'/Resources/views', 'shipping'
        );
    }

    /**
     * Register package translations.
     *
     * @return void
     */
    protected function registerTranslations()
    {
        $this->loadTranslationsFrom(
            dirname(__DIR__).'/Resources/lang', 'shipping'
        );
    }
}
