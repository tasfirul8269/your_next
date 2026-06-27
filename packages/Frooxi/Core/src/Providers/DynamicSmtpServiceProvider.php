<?php

namespace Frooxi\Core\Providers;

use Frooxi\Core\Mail\Transport\DynamicSmtpTransport;
use Illuminate\Support\ServiceProvider;

class DynamicSmtpServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        $this->app['mail.manager']->extend('frooxi-dynamic-smtp', function () {
            return new DynamicSmtpTransport;
        });
    }
}
