<?php

use Frooxi\Core\Http\Middleware\NoCacheMiddleware;
use Illuminate\Support\Facades\Route;

/**
 * Protected admin routes (require authentication).
 */
Route::group(['middleware' => ['admin', NoCacheMiddleware::class], 'prefix' => config('app.admin_url')], function () {
    /**
     * Sales routes.
     */
    require 'sales-routes.php';

    /**
     * Catalog routes.
     */
    require 'catalog-routes.php';

    /**
     * Customers routes.
     */
    require 'customers-routes.php';

    /**
     * Settings routes.
     */
    require 'settings-routes.php';

    /**
     * Storefront routes.
     */
    require 'storefront-routes.php';

    /**
     * Configuration routes.
     */
    require 'configuration-routes.php';

    /**
     * Remaining routes.
     */
    require 'rest-routes.php';
});
