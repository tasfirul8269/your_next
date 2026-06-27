<?php

use Frooxi\Shop\Http\Controllers\HomeController;
// REMOVED: BookingProduct package not installed
// use Frooxi\Shop\Http\Controllers\BookingProductController;
use Frooxi\Shop\Http\Controllers\ProductController;
// REMOVED: CMS package not installed
// use Frooxi\Shop\Http\Controllers\PageController;
use Frooxi\Shop\Http\Controllers\ProductsCategoriesProxyController;
use Frooxi\Shop\Http\Controllers\SearchController;
use Frooxi\Shop\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

// REMOVED: CMS routes disabled — CMS package not installed
// Route::get('page/{slug}', [PageController::class, 'view'])->name('shop.cms.page');

/**
 * Fallback route.
 */
Route::fallback(ProductsCategoriesProxyController::class.'@index')
    ->name('shop.product_or_category.index');

/**
 * Store front home.
 */
Route::get('/', [HomeController::class, 'index'])
    ->name('shop.home.index');

Route::get('contact-us', [HomeController::class, 'contactUs'])
    ->name('shop.home.contact_us');

Route::get('all-categories', [HomeController::class, 'allCategories'])
    ->name('shop.all-categories.index');

Route::get('flash-sale', [HomeController::class, 'flashSale'])
    ->name('shop.flash-sale.index');

Route::post('contact-us/send-mail', [HomeController::class, 'sendContactUsMail'])
    ->name('shop.home.contact_us.send_mail');

/**
 * Store front search.
 */
Route::get('search', [SearchController::class, 'index'])
    ->name('shop.search.index');

Route::post('search/upload', [SearchController::class, 'upload'])->name('shop.search.upload');

Route::get('api/search', [SearchController::class, 'suggestions'])->name('shop.search.suggestions');

/**
 * Subscription routes.
 */
Route::controller(SubscriptionController::class)->group(function () {
    Route::post('subscription', 'store')->name('shop.subscription.store');

    Route::get('subscription/{token}', 'destroy')->name('shop.subscription.destroy');
});

/**
 * Product file download
 */
Route::get('product/{id}/{attribute_id}', [ProductController::class, 'download'])->name('shop.product.file.download');

// REMOVED: Booking product routes disabled — BookingProduct package not installed
// Route::get('booking-slots/{id}', [BookingProductController::class, 'index'])->name('shop.booking-product.slots.index');
