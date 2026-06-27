<?php

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Admin\Http\Controllers\User\ForgetPasswordController;
use Frooxi\Admin\Http\Controllers\User\ResetPasswordController;
use Frooxi\Admin\Http\Controllers\User\SessionController;
use Frooxi\Admin\Http\Controllers\User\TwoFactorController;
use Illuminate\Support\Facades\Route;

/**
 * Auth routes.
 */
Route::group(['prefix' => config('app.admin_url')], function () {
    /**
     * Redirect route.
     */
    Route::get('/', [Controller::class, 'redirectToLogin']);

    Route::controller(SessionController::class)->prefix('login')->group(function () {
        /**
         * Login routes.
         */
        Route::get('', 'create')->name('admin.session.create');

        /**
         * Login post route to admin auth controller.
         */
        Route::post('', 'store')->name('admin.session.store');
    });

    /**
     * Two-factor authentication verification routes.
     */
    Route::controller(TwoFactorController::class)->prefix('two-factor')->group(function () {
        Route::get('verify', 'showVerifyForm')->name('admin.two_factor.verify.form');

        Route::post('verify', 'verifyTwoFactorCode')->name('admin.two_factor.verifyTwoFactorCode');
    });

    /**
     * Forget password routes.
     */
    Route::controller(ForgetPasswordController::class)->prefix('forget-password')->group(function () {
        Route::get('', 'create')->name('admin.forget_password.create');

        Route::post('', 'store')->name('admin.forget_password.store');
    });

    /**
     * Reset password routes.
     */
    Route::controller(ResetPasswordController::class)->prefix('reset-password')->group(function () {
        Route::get('{token}', 'create')->name('admin.reset_password.create');

        Route::post('', 'store')->name('admin.reset_password.store');
    });
});
