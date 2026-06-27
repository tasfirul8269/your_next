<?php

namespace Frooxi\Shop\Http\Controllers;

use Frooxi\Checkout\Facades\Cart;
use Frooxi\Sales\Models\Order;
use Frooxi\Sales\Repositories\OrderRepository;
use Frooxi\Sales\Transformers\OrderResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashController extends Controller
{
    /**
     * bKash sandbox base URL.
     */
    const SANDBOX_BASE_URL = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta';

    /**
     * bKash live base URL.
     */
    const LIVE_BASE_URL = 'https://tokenized.pay.bka.sh/v1.2.0-beta';

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected OrderRepository $orderRepository
    ) {}

    /**
     * Initiate the bKash tokenized payment.
     *
     * Creates the order immediately with 'pending_payment' status,
     * stores the paymentID in order_payment.additional, then redirects
     * to bKash payment URL.
     *
     * @return never|RedirectResponse
     */
    public function pay()
    {
        $cart = Cart::getCart();

        if (! $cart || ! $cart->payment || $cart->payment->method !== 'bkash') {
            return redirect()->route('shop.checkout.cart.index')
                ->with('error', trans('shop::app.checkout.cart.specify-payment-method'));
        }

        Cart::collectTotals();
        $cart = Cart::getCart();

        // Step 1: Get grant token
        $token = $this->getGrantToken();

        if (! $token) {
            Log::error('bKash: failed to obtain grant token');

            return redirect()->route('shop.checkout.cart.index')
                ->with('error', trans('shop::app.checkout.cart.bkash.payment-error'));
        }

        // Step 2: Create payment on bKash — get paymentID
        $amount = number_format((float) $cart->grand_total, 2, '.', '');
        $invoice = 'BK_'.strtoupper(uniqid()).'_'.time();

        $createResult = $this->createPayment($token, $amount, $invoice);

        if (! $createResult || empty($createResult['paymentID']) || empty($createResult['bkashURL'])) {
            Log::error('bKash: failed to create payment', ['result' => $createResult]);

            return redirect()->route('shop.checkout.cart.index')
                ->with('error', trans('shop::app.checkout.cart.bkash.payment-error'));
        }

        $paymentId = $createResult['paymentID'];
        $bkashUrl = $createResult['bkashURL'];

        // Step 3: Create order immediately with pending_payment status
        $data = (new OrderResource($cart))->jsonSerialize();
        $data['payment']['additional'] = [
            'bkash_payment_id' => $paymentId,
            'bkash_invoice' => $invoice,
        ];

        try {
            $order = $this->orderRepository->create($data);
        } catch (\Exception $e) {
            return redirect()->route('shop.checkout.cart.index')
                ->with('error', $e->getMessage());
        }

        $order->status = Order::STATUS_PENDING_PAYMENT;
        $order->save();

        Cart::deActivateCart();

        // Step 4: Redirect customer to bKash hosted payment page
        return redirect()->away($bkashUrl);
    }

    /**
     * Handle successful callback from bKash.
     *
     * bKash redirects here with ?paymentID=...&status=success after the customer pays.
     * We execute the payment server-side to confirm and capture it.
     *
     * @return RedirectResponse
     */
    public function callback(Request $request)
    {
        $paymentId = $request->query('paymentID');
        $status = $request->query('status');

        if (! $paymentId || $status !== 'success') {
            Log::warning('bKash callback: invalid status or missing paymentID', [
                'paymentID' => $paymentId,
                'status' => $status,
            ]);

            return redirect()->route('shop.checkout.cart.index')
                ->with('error', trans('shop::app.checkout.cart.bkash.payment-error'));
        }

        $order = $this->findOrderByPaymentId($paymentId);

        if (! $order) {
            Log::error('bKash callback: order not found', ['paymentID' => $paymentId]);

            return redirect()->route('shop.checkout.cart.index')
                ->with('error', trans('shop::app.checkout.cart.bkash.payment-error'));
        }

        // Execute the payment — this actually captures the money
        $token = $this->getGrantToken();

        if (! $token) {
            Log::error('bKash callback: failed to get token for execute', ['paymentID' => $paymentId]);

            $order->status = Order::STATUS_CANCELED;
            $order->save();

            return redirect()->route('shop.checkout.cart.index')
                ->with('error', trans('shop::app.checkout.cart.bkash.payment-error'));
        }

        $executeResult = $this->executePayment($token, $paymentId);

        if (
            ! $executeResult
            || ! in_array($executeResult['statusCode'] ?? '', ['0000', '200'])
            || ($executeResult['transactionStatus'] ?? '') !== 'Completed'
        ) {
            Log::error('bKash callback: execute failed', [
                'paymentID' => $paymentId,
                'result' => $executeResult,
            ]);

            $order->status = Order::STATUS_CANCELED;
            $order->save();

            return redirect()->route('shop.checkout.cart.index')
                ->with('error', trans('shop::app.checkout.cart.bkash.payment-failed'));
        }

        // Store bKash transaction ID in additional
        $additional = $order->payment->additional ?? [];
        $additional['bkash_trx_id'] = $executeResult['trxID'] ?? null;
        $order->payment->additional = $additional;
        $order->payment->save();

        // Update order to processing
        $this->orderRepository->updateOrderStatus($order, Order::STATUS_PROCESSING);

        Log::info('bKash payment confirmed', [
            'paymentID' => $paymentId,
            'trxID' => $executeResult['trxID'] ?? null,
            'order_id' => $order->id,
        ]);

        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
     * Handle cancellation from bKash (customer pressed back/cancel).
     *
     * @return RedirectResponse
     */
    public function cancel(Request $request)
    {
        $paymentId = $request->query('paymentID');

        if ($paymentId && $order = $this->findOrderByPaymentId($paymentId)) {
            $order->status = Order::STATUS_CANCELED;
            $order->save();

            Log::info('bKash payment cancelled', ['paymentID' => $paymentId, 'order_id' => $order->id]);
        }

        return redirect()->route('shop.checkout.cart.index')
            ->with('warning', trans('shop::app.checkout.cart.bkash.payment-cancelled'));
    }

    /**
     * Handle failure redirect from bKash.
     *
     * @return RedirectResponse
     */
    public function failure(Request $request)
    {
        $paymentId = $request->query('paymentID');

        if ($paymentId && $order = $this->findOrderByPaymentId($paymentId)) {
            $order->status = Order::STATUS_CANCELED;
            $order->save();

            Log::info('bKash payment failed — order marked canceled', ['paymentID' => $paymentId, 'order_id' => $order->id]);
        }

        return redirect()->route('shop.checkout.cart.index')
            ->with('error', trans('shop::app.checkout.cart.bkash.payment-failed'));
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Obtain a bKash grant token.
     */
    private function getGrantToken(): ?string
    {
        $credentials = $this->getCredentials();

        $response = Http::withHeaders([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($credentials['base_url'].'/tokenized/checkout/token/grant', [
            'app_key' => $credentials['app_key'],
            'app_secret' => $credentials['app_secret'],
        ]);

        if (! $response->successful()) {
            Log::error('bKash grant token failed', ['status' => $response->status(), 'body' => $response->body()]);

            return null;
        }

        $data = $response->json();

        if (($data['statusCode'] ?? null) !== '0000') {
            Log::error('bKash grant token error', ['response' => $data]);

            return null;
        }

        $token = $data['id_token'] ?? null;
        Log::info('bKash grant token obtained', ['token_prefix' => $token ? substr($token, 0, 20).'...' : null]);

        return $token;
    }

    /**
     * Create a bKash payment and return the response array.
     */
    private function createPayment(string $token, string $amount, string $invoice): ?array
    {
        $credentials = $this->getCredentials();
        $callbackUrl = rtrim(config('app.url'), '/').'/checkout/bkash/callback';

        Log::info('bKash create payment request', [
            'url' => $credentials['base_url'].'/tokenized/checkout/create',
            'callbackURL' => $callbackUrl,
            'amount' => $amount,
            'invoice' => $invoice,
            'token_ok' => str_starts_with($token, 'ey'),
        ]);

        $response = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key' => $credentials['app_key'],
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($credentials['base_url'].'/tokenized/checkout/create', [
            'mode' => '0011',
            'payerReference' => '01770618575',
            'callbackURL' => $callbackUrl,
            'amount' => $amount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $invoice,
        ]);

        if (! $response->successful()) {
            Log::error('bKash create payment HTTP failed', ['status' => $response->status(), 'body' => $response->body()]);

            return null;
        }

        return $response->json();
    }

    /**
     * Execute a bKash payment to capture the funds.
     */
    private function executePayment(string $token, string $paymentId): ?array
    {
        $credentials = $this->getCredentials();

        $response = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key' => $credentials['app_key'],
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($credentials['base_url'].'/tokenized/checkout/execute', [
            'paymentID' => $paymentId,
        ]);

        if (! $response->successful()) {
            Log::error('bKash execute payment HTTP failed', ['status' => $response->status(), 'body' => $response->body()]);

            return null;
        }

        return $response->json();
    }

    /**
     * Find an order by the bKash paymentID stored in order_payment.additional.
     */
    private function findOrderByPaymentId(string $paymentId): ?Order
    {
        return Order::whereHas('payment', function ($query) use ($paymentId) {
            $query->where('method', 'bkash')
                ->where('additional', 'LIKE', '%'.$paymentId.'%');
        })->first();
    }

    /**
     * Build credentials array from admin DB config with .env fallback.
     */
    private function getCredentials(): array
    {
        $isSandbox = $this->isSandbox();

        return [
            'base_url' => $isSandbox ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL,
            'app_key' => $this->configValue('app_key', 'bkash.app_key'),
            'app_secret' => $this->configValue('app_secret', 'bkash.app_secret'),
            'username' => $this->configValue('username', 'bkash.username'),
            'password' => $this->configValue('password', 'bkash.password'),
        ];
    }

    /**
     * Check whether sandbox mode is active.
     */
    private function isSandbox(): bool
    {
        $v = core()->getConfigData('sales.payment_methods.bkash.sandbox');

        return ($v !== null && $v !== '') ? (bool) $v : (bool) config('bkash.sandbox', true);
    }

    /**
     * Read a config value from DB, falling back to a config key.
     */
    private function configValue(string $field, string $fallbackKey): ?string
    {
        $v = core()->getConfigData('sales.payment_methods.bkash.'.$field);

        return ($v !== null && $v !== '') ? $v : config($fallbackKey);
    }
}
