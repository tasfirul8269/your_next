<?php

namespace Frooxi\Payment\Payment;

use Illuminate\Support\Facades\Storage;

class SSLCommerz extends Payment
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'sslcommerz';

    /**
     * Get redirect url.
     *
     * Returning this route triggers the SSLCommerz hosted payment flow.
     * The SSLCommerzController will call the API and redirect to the gateway.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return route('shop.sslcommerz.pay');
    }

    /**
     * Get payment method image.
     *
     * @return array
     */
    public function getImage()
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : yournext_asset('images/sslcommerz.png', 'shop');
    }

    /**
     * Is available.
     *
     * @return bool
     */
    public function isAvailable()
    {
        if (! $this->cart) {
            $this->setCart();
        }

        return $this->getConfigData('active');
    }
}
