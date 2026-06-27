<?php

use Frooxi\Admin\Http\Controllers\Sales\InvoiceController;
use Frooxi\Admin\Http\Controllers\Sales\OrderController;
use Frooxi\Admin\Http\Controllers\Sales\PaymentMethodController;
use Illuminate\Support\Facades\Route;

/**
 * Sales routes.
 */
Route::prefix('sales')->group(function () {
    /**
     * Orders routes.
     */
    Route::controller(OrderController::class)->prefix('orders')->group(function () {
        Route::get('', 'index')->name('admin.sales.orders.index');
        Route::get('create/{cartId}', 'create')->name('admin.sales.orders.create');
        Route::post('create/{cartId}', 'store')->name('admin.sales.orders.store');
        Route::get('view/{id}', 'view')->name('admin.sales.orders.view');
        Route::post('cancel/{id}', 'cancel')->name('admin.sales.orders.cancel');
        Route::delete('delete/{id}', 'destroy')->name('admin.sales.orders.delete');
        Route::post('mass-delete', 'massDestroy')->name('admin.sales.orders.mass_delete');
        Route::get('reorder/{id}', 'reorder')->name('admin.sales.orders.reorder');
        Route::post('comment/{order_id}', 'comment')->name('admin.sales.orders.comment');
        Route::post('update-status/{id}', 'updateStatus')->name('admin.sales.orders.update_status');
        Route::get('search', 'search')->name('admin.sales.orders.search');
    });

    /**
     * Invoices routes.
     */
    Route::controller(InvoiceController::class)->prefix('invoices')->group(function () {
        Route::get('', 'index')->name('admin.sales.invoices.index');
        Route::get('create/{order_id}', 'create')->name('admin.sales.invoices.create');
        Route::post('create/{order_id}', 'store')->name('admin.sales.invoices.store');
        Route::get('view/{id}', 'view')->name('admin.sales.invoices.view');
        Route::delete('delete/{id}', 'destroy')->name('admin.sales.invoices.delete');
        Route::post('mass-delete', 'massDestroy')->name('admin.sales.invoices.mass_delete');
        Route::post('send-duplicate-email/{id}', 'sendDuplicateEmail')->name('admin.sales.invoices.send_duplicate_email');
        Route::get('print/{id}', 'printInvoice')->name('admin.sales.invoices.print');
        Route::post('mass-update/state', 'massUpdateState')->name('admin.sales.invoices.mass_update.state');
    });

    /**
     * Payment methods routes.
     */
    Route::controller(PaymentMethodController::class)->prefix('payment-methods')->group(function () {
        Route::get('', 'index')->name('admin.sales.payment_methods.index');
        Route::post('', 'store')->name('admin.sales.payment_methods.store');
    });
});
