<?php

use Frooxi\Payment\Payment\Bkash;
use Frooxi\Payment\Payment\CashOnDelivery;
use Frooxi\Payment\Payment\SSLCommerz;

return [
    'cashondelivery' => [
        'class' => CashOnDelivery::class,
        'code' => 'cashondelivery',
        'title' => 'Cash On Delivery',
        'description' => 'Cash On Delivery',
        'active' => true,
        'generate_invoice' => false,
        'sort' => 6,
    ],

    'sslcommerz' => [
        'class' => SSLCommerz::class,
        'code' => 'sslcommerz',
        'title' => 'SSLCommerz',
        'description' => 'Pay securely with SSLCommerz',
        'active' => true,
        'generate_invoice' => false,
        'sort' => 7,
    ],

    'bkash' => [
        'class' => Bkash::class,
        'code' => 'bkash',
        'title' => 'bKash',
        'description' => 'Pay with bKash Mobile Wallet',
        'active' => true,
        'generate_invoice' => false,
        'sort' => 8,
    ],
];
