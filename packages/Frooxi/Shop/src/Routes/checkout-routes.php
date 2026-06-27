<?php

use Frooxi\Shop\Http\Controllers\BkashController;
use Frooxi\Shop\Http\Controllers\CartController;
use Frooxi\Shop\Http\Controllers\OnepageController;
use Frooxi\Shop\Http\Controllers\SSLCommerzController;
use Illuminate\Support\Facades\Route;

/**
 * Cart routes.
 */
Route::controller(CartController::class)->prefix('checkout/cart')->group(function () {
    Route::get('', 'index')->name('shop.checkout.cart.index');
});

Route::controller(OnepageController::class)->prefix('checkout/onepage')->group(function () {
    Route::get('', 'index')->name('shop.checkout.onepage.index');

    Route::get('success', 'success')->name('shop.checkout.onepage.success');
});

/**
 * bKash tokenized payment routes.
 *
 * The callback route receives a GET redirect from bKash after payment.
 */
Route::controller(BkashController::class)
    ->prefix('checkout/bkash')
    ->group(function () {
        Route::get('pay', 'pay')->name('shop.bkash.pay');
        Route::get('callback', 'callback')->name('shop.bkash.callback');
        Route::get('cancel', 'cancel')->name('shop.bkash.cancel');
        Route::get('failure', 'failure')->name('shop.bkash.failure');
    });

/**
 * SSLCommerz payment routes.
 *
 * The success/fail/cancel/ipn routes receive POST callbacks from SSLCommerz
 * without CSRF tokens — those are excluded in bootstrap/app.php.
 */
Route::controller(SSLCommerzController::class)
    ->prefix('checkout/sslcommerz')
    ->group(function () {
        Route::get('pay', 'pay')->name('shop.sslcommerz.pay');
        Route::post('success', 'success')->name('shop.sslcommerz.success');
        Route::post('fail', 'fail')->name('shop.sslcommerz.fail');
        Route::post('cancel', 'cancel')->name('shop.sslcommerz.cancel');
        Route::post('ipn', 'ipn')->name('shop.sslcommerz.ipn');
    });
