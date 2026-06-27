<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | Your SSLCommerz Store ID and Store Password. Obtain sandbox credentials
    | from https://developer.sslcommerz.com/registration/ and live credentials
    | from your SSLCommerz merchant panel.
    |
    | NOTE: These are the fallback .env values. When the admin panel has
    | store_id / store_password configured, the SSLCommerzController reads
    | directly from the Yournext config DB (getConfigData), which takes
    | precedence over these .env values.
    |
    */

    'apiCredentials' => [
        'store_id' => env('SSLCZ_STORE_ID', ''),
        'store_password' => env('SSLCZ_STORE_PASSWORD', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Domain
    |--------------------------------------------------------------------------
    |
    | Sandbox : https://sandbox.sslcommerz.com
    | Live    : https://securepay.sslcommerz.com
    |
    | Controlled by SSLCZ_TESTMODE in .env (true = sandbox, false = live).
    | The admin panel "Sandbox Mode" toggle overrides this at runtime via
    | the Yournext config DB.
    |
    */

    'apiDomain' => env('SSLCZ_TESTMODE', true)
        ? 'https://sandbox.sslcommerz.com'
        : 'https://securepay.sslcommerz.com',

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    */

    'apiUrl' => [
        'make_payment' => '/gwprocess/v4/api.php',
        'order_validate' => '/validator/api/validationserverAPI.php',
    ],

    /*
    |--------------------------------------------------------------------------
    | Localhost / Sandbox SSL Bypass
    |--------------------------------------------------------------------------
    |
    | When true, cURL will skip SSL certificate verification. Always true
    | in sandbox mode; set to false in production for full SSL enforcement.
    |
    */

    'connect_from_localhost' => env('SSLCZ_TESTMODE', true),

    /*
    |--------------------------------------------------------------------------
    | Callback URLs
    |--------------------------------------------------------------------------
    |
    | These paths are appended to APP_URL by the SSLCommerzController when
    | building the payment request. They must match the routes defined in
    | checkout-routes.php.
    |
    */

    'success_url' => '/checkout/sslcommerz/success',
    'failed_url' => '/checkout/sslcommerz/fail',
    'cancel_url' => '/checkout/sslcommerz/cancel',
    'ipn_url' => '/checkout/sslcommerz/ipn',

];
