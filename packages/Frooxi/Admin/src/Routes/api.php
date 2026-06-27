<?php

use Frooxi\Admin\Http\Controllers\Api\CategoryController;
use Frooxi\Admin\Http\Controllers\Api\CustomerController;
use Frooxi\Admin\Http\Controllers\Api\DashboardController;
use Frooxi\Admin\Http\Controllers\Api\FlashSaleController;
use Frooxi\Admin\Http\Controllers\Api\OrderController;
use Frooxi\Admin\Http\Controllers\Api\ProductController;
use Frooxi\Admin\Http\Controllers\Api\SalesController;
use Frooxi\Admin\Http\Controllers\Api\SettingController;
use Frooxi\Admin\Http\Controllers\Api\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api']], function () {
    /**
     * Dashboard routes.
     */
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('dashboard/revenue-trend', [DashboardController::class, 'revenueTrend']);
    Route::get('dashboard/orders-overview', [DashboardController::class, 'ordersOverview']);
    Route::get('dashboard/top-products', [DashboardController::class, 'topProducts']);
    Route::get('dashboard/stock-alert', [DashboardController::class, 'stockAlert']);
    Route::get('dashboard/customer-distribution', [DashboardController::class, 'customerDistribution']);

    /**
     * Attribute options route (placed before products to avoid route conflicts).
     */
    Route::get('attributes/options', [ProductController::class, 'attributeOptions'])->name('admin.api.attributes.options');
    Route::post('attributes/color-options', [ProductController::class, 'createColorOption'])->name('admin.api.attributes.color-options');

    /**
     * Products routes.
     */
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
    Route::post('products/{id}/images', [ProductController::class, 'uploadImages']);
    Route::delete('products/{id}/images/{imageId}', [ProductController::class, 'deleteImage']);
    Route::post('products/{id}/videos', [ProductController::class, 'uploadVideo']);

    /**
     * Categories routes.
     */
    Route::post('categories/reorder', [CategoryController::class, 'reorder']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('categories', [CategoryController::class, 'store']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);
    Route::put('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);

    /**
     * Customers routes.
     */
    Route::get('customers', [CustomerController::class, 'index']);
    Route::post('customers', [CustomerController::class, 'store']);
    Route::get('customers/{id}', [CustomerController::class, 'show']);
    Route::put('customers/{id}', [CustomerController::class, 'update']);
    Route::delete('customers/{id}', [CustomerController::class, 'destroy']);

    /**
     * Orders routes.
     */
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::put('orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::post('orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::delete('orders/{id}', [OrderController::class, 'destroy']);

    /**
     * Sales routes.
     */
    Route::get('invoices', [SalesController::class, 'invoices']);
    Route::post('invoices', [SalesController::class, 'createInvoice']);
    Route::get('invoices/{id}', [SalesController::class, 'showInvoice']);
    Route::get('shipments', [SalesController::class, 'shipments']);
    Route::get('refunds', [SalesController::class, 'refunds']);

    /**
     * Settings routes.
     */
    Route::get('settings/users', [SettingController::class, 'users']);
    Route::get('settings/roles', [SettingController::class, 'roles']);
    Route::get('settings/channels', [SettingController::class, 'channels']);
    Route::get('settings/locales', [SettingController::class, 'locales']);
    Route::get('settings/config', [SettingController::class, 'getConfig']);
    Route::put('settings/config', [SettingController::class, 'updateConfig']);
    // Aliases for frontend convenience
    Route::get('settings', [SettingController::class, 'getConfig']);
    Route::put('settings', [SettingController::class, 'updateConfig']);

    /**
     * Storefront routes.
     */
    Route::get('storefront/hero-carousel', [StorefrontController::class, 'getHeroSlides']);
    Route::post('storefront/hero-carousel', [StorefrontController::class, 'saveHeroSlide']);
    Route::post('storefront/hero-carousel/reorder', [StorefrontController::class, 'reorderHeroSlides']);
    Route::put('storefront/hero-carousel/{id}', [StorefrontController::class, 'updateHeroSlide']);
    Route::delete('storefront/hero-carousel/{id}', [StorefrontController::class, 'deleteHeroSlide']);
    Route::put('storefront/hero-carousel/{id}/toggle', [StorefrontController::class, 'toggleSlideStatus']);

    /**
     * Flash Sale routes.
     */
    Route::get('storefront/flash-sale', [FlashSaleController::class, 'index']);
    Route::post('storefront/flash-sale', [FlashSaleController::class, 'store']);
    Route::put('storefront/flash-sale/{id}', [FlashSaleController::class, 'update']);
    Route::delete('storefront/flash-sale/{id}', [FlashSaleController::class, 'destroy']);
    Route::put('storefront/flash-sale/{id}/toggle', [FlashSaleController::class, 'toggleStatus']);
    Route::post('storefront/flash-sale/reorder', [FlashSaleController::class, 'massUpdate']);
});
