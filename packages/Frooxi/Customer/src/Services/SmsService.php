<?php

namespace Frooxi\Customer\Services;

interface SmsService
{
    /**
     * Send an OTP SMS to the given phone number.
     */
    public function sendOtp(string $phone, string $otp): bool;
}
