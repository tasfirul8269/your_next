<?php

use Frooxi\Payment\Facades\Payment;

if (! function_exists('payment')) {
    /**
     * Payment helper.
     *
     * @return Frooxi\Payment\Payment
     */
    function payment()
    {
        return Payment::getFacadeRoot();
    }
}
