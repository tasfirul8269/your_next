<?php

use Frooxi\Core\Http\Middleware\NoCacheMiddleware;
use Frooxi\Shop\Http\Controllers\Customer\Account\AddressController;
use Frooxi\Shop\Http\Controllers\Customer\Account\OrderController;
use Frooxi\Shop\Http\Controllers\Customer\Account\WishlistController;
use Frooxi\Shop\Http\Controllers\Customer\CustomerController;
use Frooxi\Shop\Http\Controllers\Customer\ForgotPasswordController;
use Frooxi\Shop\Http\Controllers\Customer\RegistrationController;
use Frooxi\Shop\Http\Controllers\Customer\ResetPasswordController;
use Frooxi\Shop\Http\Controllers\Customer\SessionController;
use Frooxi\Shop\Http\Controllers\DataGridController;
use Illuminate\Support\Facades\Route;

Route::prefix('customer')->group(function () {
    Route::controller(ForgotPasswordController::class)->prefix('forgot-password')->group(function () {
        Route::get('', 'create')->name('shop.customers.forgot_password.create');
        Route::post('', 'store')->name('shop.customers.forgot_password.store');
    });

    Route::controller(ResetPasswordController::class)->prefix('reset-password')->group(function () {
        Route::get('{token}', 'create')->name('shop.customers.reset_password.create');
        Route::post('', 'store')->name('shop.customers.reset_password.store');
    });

    Route::controller(SessionController::class)->prefix('login')->group(function () {
        Route::get('', 'index')->name('shop.customer.session.index');
        Route::post('', 'store')->name('shop.customer.session.create');
    });

    Route::controller(RegistrationController::class)->group(function () {
        Route::prefix('register')->group(function () {
            Route::get('', 'index')->name('shop.customers.register.index');
            Route::post('', 'store')->name('shop.customers.register.store');
        });

        Route::get('verify-otp', 'showOtpForm')->name('shop.customers.verify-otp');
        Route::post('verify-otp', 'verifyOtp')->name('shop.customers.verify-otp.store');
        Route::post('resend-otp', 'resendOtp')->name('shop.customers.resend-otp');
    });

    Route::group(['middleware' => ['customer', NoCacheMiddleware::class]], function () {
        Route::get('datagrid/look-up', [DataGridController::class, 'lookUp'])->name('shop.customer.datagrid.look_up');
        Route::delete('logout', [SessionController::class, 'destroy'])->name('shop.customer.session.destroy');

        Route::prefix('account')->group(function () {
            Route::get('', [CustomerController::class, 'account'])->name('shop.customers.account.index');

            Route::get('wishlist', [WishlistController::class, 'index'])->name('shop.customers.account.wishlist.index');

            Route::controller(CustomerController::class)->group(function () {
                Route::prefix('profile')->group(function () {
                    Route::get('', 'index')->name('shop.customers.account.profile.index');
                    Route::get('edit', 'edit')->name('shop.customers.account.profile.edit');
                    Route::post('edit', 'update')->name('shop.customers.account.profile.update');
                    Route::post('destroy', 'destroy')->name('shop.customers.account.profile.destroy');
                });

                Route::get('reviews', 'reviews')->name('shop.customers.account.reviews.index');
            });

            Route::controller(AddressController::class)->prefix('addresses')->group(function () {
                Route::get('', 'index')->name('shop.customers.account.addresses.index');
                Route::get('create', 'create')->name('shop.customers.account.addresses.create');
                Route::post('create', 'store')->name('shop.customers.account.addresses.store');
                Route::get('edit/{id}', 'edit')->name('shop.customers.account.addresses.edit');
                Route::put('edit/{id}', 'update')->name('shop.customers.account.addresses.update');
                Route::patch('edit/{id}', 'makeDefault')->name('shop.customers.account.addresses.update.default');
                Route::delete('delete/{id}', 'destroy')->name('shop.customers.account.addresses.delete');
            });

            Route::controller(OrderController::class)->prefix('orders')->group(function () {
                Route::get('', 'index')->name('shop.customers.account.orders.index');
                Route::get('view/{id}', 'view')->name('shop.customers.account.orders.view');
                Route::get('reorder/{id}', 'reorder')->name('shop.customers.account.orders.reorder');
                Route::post('cancel/{id}', 'cancel')->name('shop.customers.account.orders.cancel');
                Route::get('print/Invoice/{id}', 'printInvoice')->name('shop.customers.account.orders.print-invoice');
            });
        });
    });
});
