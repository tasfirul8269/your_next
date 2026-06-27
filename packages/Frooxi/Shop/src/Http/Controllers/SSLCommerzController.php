<?php

namespace Frooxi\Shop\Http\Controllers;

use Frooxi\Checkout\Facades\Cart;
use Frooxi\Payment\Library\SSLCommerz\SslCommerzNotification;
use Frooxi\Sales\Models\Order;
use Frooxi\Sales\Repositories\OrderRepository;
use Frooxi\Sales\Transformers\OrderResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SSLCommerzController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected OrderRepository $orderRepository
    ) {}

    /**
     * Initiate the SSLCommerz payment.
     *
     * Creates the order immediately with 'pending_payment' status,
     * stores the tran_id in order_payment.additional, then redirects
     * the customer to the SSLCommerz gateway.
     *
     * @return never|RedirectResponse
     */
    public function pay()
    {
        $cart = Cart::getCart();

        if (! $cart || ! $cart->payment || $cart->payment->method !== 'sslcommerz') {
            return redirect()->route('shop.checkout.cart.index')
                ->with('error', trans('shop::app.checkout.cart.specify-payment-method'));
        }

        Cart::collectTotals();
        $cart = Cart::getCart();

        // Generate unique transaction ID
        $tranId = 'SSLCZ_'.strtoupper(uniqid()).'_'.time();

        // Create order immediately with pending_payment status
        $data = (new OrderResource($cart))->jsonSerialize();

        // Store tran_id in payment additional data for later lookup
        $data['payment']['additional'] = [
            'sslcommerz_tran_id' => $tranId,
        ];

        try {
            $order = $this->orderRepository->create($data);
        } catch (\Exception $e) {
            return redirect()->route('shop.checkout.cart.index')
                ->with('error', $e->getMessage());
        }

        // Set order status to pending_payment after creation
        $order->status = Order::STATUS_PENDING_PAYMENT;
        $order->save();

        // Deactivate cart — order is placed
        Cart::deActivateCart();

        $this->applyRuntimeConfig();

        $billing = $cart->billing_address;
        $shipping = $cart->shipping_address ?? $billing;

        $baseUrl = rtrim(config('app.url'), '/');

        $productNames = mb_substr(
            $cart->items->map(fn ($item) => $item->name)->implode(', '),
            0,
            100
        );

        $customerName = trim(($billing->first_name ?? '').' '.($billing->last_name ?? '')) ?: 'Customer';
        $customerEmail = $billing->email ?? (auth()->guard('customer')->user()?->email ?? 'noreply@example.com');
        $customerPhone = $billing->phone ?? '01700000000';

        $postData = [
            // Required
            'total_amount' => number_format((float) $cart->grand_total, 2, '.', ''),
            'currency' => 'BDT',
            'tran_id' => $tranId,
            'success_url' => $baseUrl.config('sslcommerz.success_url'),
            'fail_url' => $baseUrl.config('sslcommerz.failed_url'),
            'cancel_url' => $baseUrl.config('sslcommerz.cancel_url'),
            'ipn_url' => $baseUrl.config('sslcommerz.ipn_url'),

            // Customer info
            'cus_name' => $customerName,
            'cus_email' => $customerEmail,
            'cus_add1' => $billing->address1 ?? 'N/A',
            'cus_add2' => $billing->address2 ?? '',
            'cus_city' => $billing->city ?? 'Dhaka',
            'cus_state' => $billing->state ?? '',
            'cus_postcode' => $billing->postcode ?? '1000',
            'cus_country' => $billing->country ?? 'Bangladesh',
            'cus_phone' => $customerPhone,

            // Shipping info
            'ship_name' => trim(($shipping->first_name ?? '').' '.($shipping->last_name ?? '')) ?: $customerName,
            'ship_add1' => $shipping->address1 ?? $billing->address1 ?? 'N/A',
            'ship_add2' => $shipping->address2 ?? '',
            'ship_city' => $shipping->city ?? $billing->city ?? 'Dhaka',
            'ship_state' => $shipping->state ?? $billing->state ?? '',
            'ship_postcode' => $shipping->postcode ?? $billing->postcode ?? '1000',
            'ship_country' => $shipping->country ?? $billing->country ?? 'Bangladesh',
            'shipping_method' => $cart->selected_shipping_rate ? 'YES' : 'NO',

            // Product info
            'product_name' => $productNames ?: 'Order',
            'product_category' => 'General',
            'product_profile' => 'general',
            'num_of_item' => $cart->items->count(),
        ];

        $sslcommerz = new SslCommerzNotification;

        // 'hosted' redirects the browser to the SSLCommerz gateway page
        $sslcommerz->makePayment($postData, 'hosted');

        // Should not reach here
        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
     * Handle successful payment callback from SSLCommerz.
     *
     * Looks up the order by tran_id, validates the payment with SSLCommerz,
     * then updates the order status to processing.
     *
     * @return RedirectResponse
     */
    public function success(Request $request)
    {
        $postData = $request->all();
        $tranId = $postData['tran_id'] ?? null;

        if (! $tranId) {
            return redirect()->route('shop.checkout.cart.index')
                ->with('error', trans('shop::app.checkout.cart.sslcommerz.payment-error'));
        }

        // Find the order by tran_id stored in order_payment.additional
        $order = $this->findOrderByTranId($tranId);

        if (! $order) {
            Log::error('SSLCommerz success: order not found for tran_id', ['tran_id' => $tranId]);

            return redirect()->route('shop.checkout.cart.index')
                ->with('error', trans('shop::app.checkout.cart.sslcommerz.payment-error'));
        }

        $this->applyRuntimeConfig();

        $sslcommerz = new SslCommerzNotification;

        // In sandbox mode, skip hash verification as the test gateway
        // generates unreliable signatures. Always rely on server-side orderValidate().
        $isSandbox = (bool) config('sslcommerz.connect_from_localhost');

        if (! $isSandbox && ! $sslcommerz->hashVerify($postData)) {
            Log::warning('SSLCommerz success: hash mismatch', ['tran_id' => $tranId]);

            return redirect()->route('shop.checkout.cart.index')
                ->with('error', trans('shop::app.checkout.cart.sslcommerz.payment-error'));
        }

        // Validate transaction server-side with SSLCommerz API
        if (! $sslcommerz->orderValidate($postData, $tranId, $order->grand_total, 'BDT')) {
            Log::warning('SSLCommerz success: order validation failed', [
                'tran_id' => $tranId,
                'error' => $sslcommerz->getError(),
            ]);

            return redirect()->route('shop.checkout.cart.index')
                ->with('error', trans('shop::app.checkout.cart.sslcommerz.payment-error'));
        }

        // Payment confirmed — update order status to processing
        $this->orderRepository->updateOrderStatus($order, Order::STATUS_PROCESSING);

        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
     * Handle failed payment callback from SSLCommerz.
     *
     * @return RedirectResponse
     */
    public function fail(Request $request)
    {
        $tranId = $request->input('tran_id');

        if ($tranId && $order = $this->findOrderByTranId($tranId)) {
            $order->status = Order::STATUS_CANCELED;
            $order->save();
            Log::info('SSLCommerz payment failed — order marked canceled', ['tran_id' => $tranId, 'order_id' => $order->id]);
        }

        return redirect()->route('shop.checkout.cart.index')
            ->with('error', trans('shop::app.checkout.cart.sslcommerz.payment-failed'));
    }

    /**
     * Handle cancelled payment callback from SSLCommerz.
     *
     * @return RedirectResponse
     */
    public function cancel(Request $request)
    {
        $tranId = $request->input('tran_id');

        if ($tranId && $order = $this->findOrderByTranId($tranId)) {
            $order->status = Order::STATUS_CANCELED;
            $order->save();
            Log::info('SSLCommerz payment cancelled — order marked canceled', ['tran_id' => $tranId, 'order_id' => $order->id]);
        }

        return redirect()->route('shop.checkout.cart.index')
            ->with('warning', trans('shop::app.checkout.cart.sslcommerz.payment-cancelled'));
    }

    /**
     * Handle Instant Payment Notification (IPN) from SSLCommerz.
     *
     * Server-to-server POST — updates order status independently of the browser.
     *
     * @return Response
     */
    public function ipn(Request $request)
    {
        $postData = $request->all();
        $tranId = $postData['tran_id'] ?? null;
        $status = $postData['status'] ?? null;

        $this->applyRuntimeConfig();

        $sslcommerz = new SslCommerzNotification;

        $isSandbox = (bool) config('sslcommerz.connect_from_localhost');

        if (! $isSandbox && ! $sslcommerz->hashVerify($postData)) {
            Log::warning('SSLCommerz IPN: hash mismatch', ['tran_id' => $tranId]);

            return response('Hash mismatch', 400);
        }

        Log::info('SSLCommerz IPN received', ['tran_id' => $tranId, 'status' => $status]);

        if ($tranId && in_array($status, ['VALID', 'VALIDATED'])) {
            $order = $this->findOrderByTranId($tranId);

            if ($order && $order->status === Order::STATUS_PENDING_PAYMENT) {
                $this->orderRepository->updateOrderStatus($order, Order::STATUS_PROCESSING);
                Log::info('SSLCommerz IPN: order status updated to processing', ['order_id' => $order->id]);
            }
        }

        return response('IPN received', 200);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Find an order by the SSLCommerz tran_id stored in order_payment.additional.
     */
    private function findOrderByTranId(string $tranId): ?Order
    {
        return Order::whereHas('payment', function ($query) use ($tranId) {
            $query->where('method', 'sslcommerz')
                ->where('additional', 'LIKE', '%'.$tranId.'%');
        })->first();
    }

    /**
     * Apply runtime SSLCommerz config from admin DB settings.
     */
    private function applyRuntimeConfig(): void
    {
        $storeId = core()->getConfigData('sales.payment_methods.sslcommerz.store_id')
            ?: config('sslcommerz.apiCredentials.store_id');

        $storePassword = core()->getConfigData('sales.payment_methods.sslcommerz.store_password')
            ?: config('sslcommerz.apiCredentials.store_password');

        $sandboxConfig = core()->getConfigData('sales.payment_methods.sslcommerz.sandbox');
        $isSandbox = ($sandboxConfig !== null && $sandboxConfig !== '')
            ? (bool) $sandboxConfig
            : (bool) config('sslcommerz.connect_from_localhost');

        $apiDomain = $isSandbox
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';

        config([
            'sslcommerz.apiCredentials.store_id' => $storeId,
            'sslcommerz.apiCredentials.store_password' => $storePassword,
            'sslcommerz.apiDomain' => $apiDomain,
            'sslcommerz.connect_from_localhost' => $isSandbox,
        ]);
    }
}
