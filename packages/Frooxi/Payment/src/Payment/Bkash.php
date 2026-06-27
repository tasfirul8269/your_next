<?php

namespace Frooxi\Payment\Payment;

use Illuminate\Support\Facades\Storage;

class Bkash extends Payment
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'bkash';

    /**
     * Get redirect url.
     *
     * Returns the bKash payment initiation route.
     * The BkashController will call the tokenized API and redirect to bKash.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return route('shop.bkash.pay');
    }

    /**
     * Get payment method image.
     *
     * @return array
     */
    public function getImage()
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : yournext_asset('images/bkash.png', 'shop');
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
