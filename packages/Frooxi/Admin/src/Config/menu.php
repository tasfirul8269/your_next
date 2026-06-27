<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Menu Configuration
    |--------------------------------------------------------------------------
    |
    | Matches AdminV2 sidebar navigation structure.
    |
    */

    // Dashboard
    [
        'key' => 'dashboard',
        'name' => 'admin::app.components.layouts.sidebar.dashboard',
        'route' => 'admin.dashboard.index',
        'sort' => 1,
        'icon' => 'icon-dashboard',
    ],

    // Catalog
    [
        'key' => 'catalog',
        'name' => 'admin::app.components.layouts.sidebar.catalog',
        'route' => 'admin.catalog.products.index',
        'sort' => 2,
        'icon' => 'icon-product',
    ],
    [
        'key' => 'catalog.products',
        'name' => 'admin::app.components.layouts.sidebar.products',
        'route' => 'admin.catalog.products.index',
        'sort' => 1,
        'icon' => '',
    ],
    [
        'key' => 'catalog.categories',
        'name' => 'admin::app.components.layouts.sidebar.categories',
        'route' => 'admin.catalog.categories.index',
        'sort' => 2,
        'icon' => '',
    ],

    // Sales
    [
        'key' => 'sales',
        'name' => 'admin::app.components.layouts.sidebar.sales',
        'route' => 'admin.sales.orders.index',
        'sort' => 4,
        'icon' => 'icon-sales',
    ],
    [
        'key' => 'sales.orders',
        'name' => 'admin::app.components.layouts.sidebar.orders',
        'route' => 'admin.sales.orders.index',
        'sort' => 1,
        'icon' => '',
    ],
    [
        'key'   => 'sales.invoices',
        'name'  => 'admin::app.components.layouts.sidebar.invoices',
        'route' => 'admin.sales.invoices.index',
        'sort'  => 2,
        'icon'  => '',
    ],
    [
        'key' => 'sales.payment_methods',
        'name' => 'Payment Methods',
        'route' => 'admin.sales.payment_methods.index',
        'sort' => 3,
        'icon' => '',
    ],

    // Customers
    [
        'key' => 'customers',
        'name' => 'admin::app.components.layouts.sidebar.customers',
        'route' => 'admin.customers.customers.index',
        'sort' => 5,
        'icon' => 'icon-customer-2',
    ],
    [
        'key' => 'customers.customers',
        'name' => 'admin::app.components.layouts.sidebar.customers',
        'route' => 'admin.customers.customers.index',
        'sort' => 1,
        'icon' => '',
    ],
    [
        'key' => 'customers.reviews',
        'name' => 'admin::app.components.layouts.sidebar.reviews',
        'route' => 'admin.customers.customers.review.index',
        'sort' => 2,
        'icon' => '',
    ],

    // Storefront
    [
        'key' => 'storefront',
        'name' => 'Storefront',
        'route' => 'admin.storefront.hero_carousel.index',
        'sort' => 6,
        'icon' => 'icon-store',
    ],
    [
        'key' => 'storefront.hero_carousel',
        'name' => 'Top Banners',
        'route' => 'admin.storefront.hero_carousel.index',
        'sort' => 1,
        'icon' => '',
    ],
    // Flash Sale (Moved to Parent)
    [
        'key' => 'flash_sale',
        'name' => 'Flash Sale',
        'route' => 'admin.storefront.flash_sale.index',
        'sort' => 3,
        'icon' => 'icon-sales',
    ],
    [
        'key' => 'storefront.shipping_methods',
        'name' => 'Shipping Methods',
        'route' => 'admin.shipping_methods.index',
        'sort' => 3,
        'icon' => 'icon-shipping',
    ],

    // Settings (unified — replaces old settings + configuration)
    [
        'key' => 'settings',
        'name' => 'admin::app.components.layouts.sidebar.settings',
        'route' => 'admin.settings.page.index',
        'sort' => 7,
        'icon' => 'icon-settings',
    ],
];
