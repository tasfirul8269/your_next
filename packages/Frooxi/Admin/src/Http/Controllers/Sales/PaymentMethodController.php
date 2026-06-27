<?php

namespace Frooxi\Admin\Http\Controllers\Sales;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Core\Repositories\CoreConfigRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    /**
     * Payment methods managed on this page.
     */
    const METHODS = ['sslcommerz', 'bkash'];

    /**
     * Create a new controller instance.
     */
    public function __construct(protected CoreConfigRepository $coreConfigRepository) {}

    /**
     * Display the payment methods configuration page.
     */
    public function index(): View
    {
        $channel = core()->getRequestedChannel();
        $channelCode = $channel->code;

        $methods = [];

        foreach (self::METHODS as $code) {
            $methods[$code] = [
                'active' => (bool) core()->getConfigData("sales.payment_methods.{$code}.active", $channelCode),
                'title' => core()->getConfigData("sales.payment_methods.{$code}.title", $channelCode) ?? '',
                'sandbox' => (bool) core()->getConfigData("sales.payment_methods.{$code}.sandbox", $channelCode),
                // SSLCommerz specific
                'store_id' => core()->getConfigData("sales.payment_methods.{$code}.store_id", $channelCode) ?? '',
                'store_password' => core()->getConfigData("sales.payment_methods.{$code}.store_password", $channelCode) ?? '',
                // bKash specific
                'app_key' => core()->getConfigData("sales.payment_methods.{$code}.app_key", $channelCode) ?? '',
                'app_secret' => core()->getConfigData("sales.payment_methods.{$code}.app_secret", $channelCode) ?? '',
                'username' => core()->getConfigData("sales.payment_methods.{$code}.username", $channelCode) ?? '',
                'password' => core()->getConfigData("sales.payment_methods.{$code}.password", $channelCode) ?? '',
            ];
        }

        return view('admin::sales.payment-methods.index', compact('methods', 'channel', 'channelCode'));
    }

    /**
     * Save payment method configuration.
     */
    public function store(): RedirectResponse
    {
        $channel = request('channel', core()->getDefaultChannel()->code);
        $locale = request('locale', app()->getLocale());

        $data = [
            'locale' => $locale,
            'channel' => $channel,
            'sales' => ['payment_methods' => []],
        ];

        foreach (self::METHODS as $code) {
            $method = request($code, []);

            $entry = [
                'active' => isset($method['active']) ? 1 : 0,
                'title' => $method['title'] ?? '',
                'sandbox' => isset($method['sandbox']) ? 1 : 0,
            ];

            if ($code === 'sslcommerz') {
                $entry['store_id'] = $method['store_id'] ?? '';
                $entry['store_password'] = $method['store_password'] ?? '';
            }

            if ($code === 'bkash') {
                $entry['app_key'] = $method['app_key'] ?? '';
                $entry['app_secret'] = $method['app_secret'] ?? '';
                $entry['username'] = $method['username'] ?? '';
                $entry['password'] = $method['password'] ?? '';
            }

            $data['sales']['payment_methods'][$code] = $entry;
        }

        $this->coreConfigRepository->create($data);

        session()->flash('success', 'Payment method settings saved successfully.');

        return redirect()->back();
    }
}
