<?php

namespace Frooxi\Admin\Http\Controllers\Settings;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Core\Repositories\CoreConfigRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsPageController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected CoreConfigRepository $coreConfigRepository) {}

    /**
     * Display the unified settings page.
     */
    public function index(): View
    {
        $channel = core()->getRequestedChannel();

        $channelCode = $channel->code;

        $localeCode = core()->getRequestedLocale()->code;

        $settings = [
            // Store Information
            'store_name' => core()->getConfigData('sales.shipping.origin.store_name', $channelCode),
            'store_email' => core()->getConfigData('email.email-settings.contact.contact_email'),
            'store_phone' => core()->getConfigData('sales.shipping.origin.contact_number', $channelCode),
            'store_address' => core()->getConfigData('sales.shipping.origin.street', $channelCode),

            // Catalog Settings
            'products_per_page' => core()->getConfigData('catalog.products.storefront.products_per_page', $channelCode),
            'default_sort' => core()->getConfigData('catalog.products.storefront.sort_by', $channelCode),
            'show_out_of_stock' => core()->getConfigData('catalog.inventory.stock_options.back_orders', $channelCode),

            // Order Settings
            'order_prefix' => core()->getConfigData('sales.order_settings.order_number.order_number_prefix', $channelCode),
            'min_order_amount' => core()->getConfigData('sales.order_settings.minimum_order.minimum_order_amount', $channelCode),
        ];

        return view('admin::settings.index', compact('settings', 'channel', 'channelCode', 'localeCode'));
    }

    /**
     * Store the unified settings.
     */
    public function store(): RedirectResponse
    {
        $channel = request('channel', core()->getDefaultChannel()->code);

        $locale = request('locale', app()->getLocale());

        $data = [
            'locale' => $locale,
            'channel' => $channel,

            'sales' => [
                'shipping' => [
                    'origin' => [
                        'store_name' => request('store_name'),
                        'contact_number' => request('store_phone'),
                        'street' => request('store_address'),
                    ],
                ],
                'order_settings' => [
                    'order_number' => [
                        'order_number_prefix' => request('order_prefix'),
                    ],
                    'minimum_order' => [
                        'minimum_order_amount' => request('min_order_amount', 0),
                    ],
                ],
            ],

            'email' => [
                'email-settings' => [
                    'contact' => [
                        'contact_email' => request('store_email'),
                    ],
                ],
            ],

            'catalog' => [
                'products' => [
                    'storefront' => [
                        'products_per_page' => request('products_per_page', 24),
                        'sort_by' => request('default_sort', 'newest'),
                    ],
                ],
                'inventory' => [
                    'stock_options' => [
                        'back_orders' => request('show_out_of_stock', 0),
                    ],
                ],
            ],
        ];

        $this->coreConfigRepository->create($data);

        session()->flash('success', trans('admin::app.configuration.index.save-message'));

        return redirect()->back();
    }
}
