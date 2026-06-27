<?php

use Frooxi\Admin\Http\Controllers\Settings\ShippingMethodController;
use Frooxi\Admin\Http\Controllers\Storefront\FlashSaleController;
use Frooxi\Admin\Http\Controllers\Storefront\HeroCarouselController;
use Illuminate\Support\Facades\Route;

/**
 * Storefront routes.
 */
Route::prefix('storefront')->group(function () {
    /**
     * Hero Carousel routes.
     */
    Route::controller(HeroCarouselController::class)->prefix('hero-carousel')->group(function () {
        Route::get('', 'index')->name('admin.storefront.hero_carousel.index');

        Route::post('store', 'store')->name('admin.storefront.hero_carousel.store');

        Route::put('update/{id}', 'update')->name('admin.storefront.hero_carousel.update');

        Route::delete('destroy/{id}', 'destroy')->name('admin.storefront.hero_carousel.destroy');

        Route::post('mass-update', 'massUpdate')->name('admin.storefront.hero_carousel.mass_update');
    });

    /**
     * Flash Sale routes.
     */
    Route::controller(FlashSaleController::class)->prefix('flash-sale')->group(function () {
        Route::get('', 'index')->name('admin.storefront.flash_sale.index');

        Route::get('create', 'create')->name('admin.storefront.flash_sale.create');

        Route::post('store', 'store')->name('admin.storefront.flash_sale.store');

        Route::get('edit/{id}', 'edit')->name('admin.storefront.flash_sale.edit');

        Route::put('update/{id}', 'update')->name('admin.storefront.flash_sale.update');

        Route::delete('destroy/{id}', 'destroy')->name('admin.storefront.flash_sale.destroy');

        Route::put('toggle/{id}', 'toggleStatus')->name('admin.storefront.flash_sale.toggle');

        Route::post('mass-update', 'massUpdate')->name('admin.storefront.flash_sale.mass_update');
    });

    /**
     * Shipping Methods routes.
     */
    Route::controller(ShippingMethodController::class)->prefix('shipping-methods')->group(function () {
        Route::get('', 'index')->name('admin.shipping_methods.index');

        Route::post('store', 'store')->name('admin.shipping_methods.store');

        Route::put('update/{id}', 'update')->name('admin.shipping_methods.update');

        Route::delete('destroy/{id}', 'destroy')->name('admin.shipping_methods.delete');
    });
});
