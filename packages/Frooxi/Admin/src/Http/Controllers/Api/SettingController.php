<?php

namespace Frooxi\Admin\Http\Controllers\Api;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Core\Repositories\ChannelRepository;
use Frooxi\Core\Repositories\CoreConfigRepository;
use Frooxi\Core\Repositories\LocaleRepository;
use Frooxi\User\Repositories\AdminRepository;
use Frooxi\User\Repositories\RoleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(
        protected AdminRepository $adminRepository,
        protected RoleRepository $roleRepository,
        protected ChannelRepository $channelRepository,
        protected LocaleRepository $localeRepository,
        protected CoreConfigRepository $coreConfigRepository
    ) {}

    public function users(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->adminRepository->paginate($request->get('limit', 10)),
        ]);
    }

    public function roles(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->roleRepository->paginate($request->get('limit', 10)),
        ]);
    }

    public function channels(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->channelRepository->paginate($request->get('limit', 10)),
        ]);
    }

    public function locales(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->localeRepository->paginate($request->get('limit', 10)),
        ]);
    }

    /**
     * Get store configuration settings.
     */
    public function getConfig(): JsonResponse
    {
        $settings = [
            'store_name' => core()->getConfigData('general.general.locale_options.name') ?? config('app.name'),
            'store_email' => core()->getConfigData('emails.configure.email_settings.sender_email') ?? '',
            'store_phone' => core()->getConfigData('general.general.locale_options.phone') ?? '',
            'store_address' => core()->getConfigData('general.general.locale_options.address') ?? '',
            'products_per_page' => core()->getConfigData('catalog.products.storefront.products_per_page') ?? 12,
            'default_sort' => core()->getConfigData('catalog.products.storefront.sort_by') ?? 'name-asc',
            'show_out_of_stock' => core()->getConfigData('catalog.products.storefront.out_of_stock_items') ?? true,
            'order_prefix' => core()->getConfigData('sales.order_settings.order_number.order_number_prefix') ?? '',
            'min_order_amount' => core()->getConfigData('sales.order_settings.minimum_order.minimum_order_amount') ?? 0,
        ];

        return response()->json([
            'data' => $settings,
        ]);
    }

    /**
     * Update store configuration settings.
     */
    public function updateConfig(Request $request): JsonResponse
    {
        $request->validate([
            'store_name' => 'string|max:255',
            'store_email' => 'email',
            'products_per_page' => 'integer|min:1|max:100',
            'min_order_amount' => 'numeric|min:0',
        ]);

        try {
            $mappings = [
                'store_name' => 'general.general.locale_options.name',
                'store_email' => 'emails.configure.email_settings.sender_email',
                'store_phone' => 'general.general.locale_options.phone',
                'store_address' => 'general.general.locale_options.address',
                'products_per_page' => 'catalog.products.storefront.products_per_page',
                'default_sort' => 'catalog.products.storefront.sort_by',
                'show_out_of_stock' => 'catalog.products.storefront.out_of_stock_items',
                'order_prefix' => 'sales.order_settings.order_number.order_number_prefix',
                'min_order_amount' => 'sales.order_settings.minimum_order.minimum_order_amount',
            ];

            foreach ($mappings as $inputKey => $configKey) {
                if ($request->has($inputKey)) {
                    $this->coreConfigRepository->updateOrCreate(
                        ['code' => $configKey],
                        ['value' => $request->input($inputKey)]
                    );
                }
            }

            return response()->json([
                'message' => 'Settings updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
