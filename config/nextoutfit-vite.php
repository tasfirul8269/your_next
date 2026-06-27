<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Next Outfit Vite Configuration
    |--------------------------------------------------------------------------
    |
    | Please add your Vite registry here to seamlessly support the `nextoutfit_assets` function.
    |
    */

    'viters' => [
        'admin' => [
            'hot_file' => 'admin-default-vite.hot',
            'build_directory' => 'themes/admin/default/build',
            'package_assets_directory' => 'src/Resources/assets',
        ],

        'admin-v2' => [
            'hot_file' => 'admin-v2-vite.hot',
            'build_directory' => 'themes/admin/v2/build',
            'package_assets_directory' => 'src/Resources/assets',
        ],

        'shop' => [
            'hot_file' => 'shop-default-vite.hot',
            'build_directory' => 'themes/shop/default/build',
            'package_assets_directory' => 'src/Resources/assets',
        ],

        'installer' => [
            'hot_file' => 'installer-default-vite.hot',
            'build_directory' => 'themes/installer/default/build',
            'package_assets_directory' => 'src/Resources/assets',
        ],
    ],
];
