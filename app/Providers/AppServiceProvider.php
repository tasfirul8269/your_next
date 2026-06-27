<?php

namespace App\Providers;

use Barryvdh\Debugbar\Facades\Debugbar;
use Frooxi\BookingProduct\Models\BookingProduct;
use Frooxi\Product\Models\Product;
use Frooxi\Product\Models\ProductFlat;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $allowedIPs = array_map('trim', explode(',', config('app.debug_allowed_ips', '')));

        $allowedIPs = array_filter($allowedIPs);

        if (empty($allowedIPs)) {
            return;
        }

        if (in_array(Request::ip(), $allowedIPs)) {
            Debugbar::enable();
        } else {
            Debugbar::disable();
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         * Map legacy Webkul morph types to Frooxi classes so any
         * un-migrated rows in order_items / cart_items still resolve.
         */
        Relation::morphMap([
            'Webkul\\Product\\Models\\Product' => Product::class,
            'Webkul\\Product\\Models\\ProductFlat' => ProductFlat::class,
            'Webkul\\BookingProduct\\Models\\BookingProduct' => class_exists(BookingProduct::class)
                ? BookingProduct::class
                : Product::class,
        ]);

        ParallelTesting::setUpTestDatabase(function (string $database, int $token) {
            Artisan::call('db:seed');
        });
    }
}
