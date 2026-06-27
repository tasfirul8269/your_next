<?php

namespace Frooxi\Customer\Services;

use Frooxi\Customer\Contracts\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function __construct(
        protected SmsService $smsService,
    ) {}

    /**
     * Generate OTP without saving or sending.
     * Returns array with 'plain', 'hashed', and 'expires_at'.
     */
    public function generateOtp(string $phone): ?array
    {
        // Mock mode: always use 123456, otherwise random 6-digit
        $otp = config('sslwireless.mock') ? '123456' : str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        Log::info("OTP generated for phone {$phone}", ['mock' => config('sslwireless.mock')]);

        return [
            'plain' => $otp,
            'hashed' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
        ];
    }

    /**
     * Send OTP via SMS only.
     */
    public function sendOtp(string $phone, string $otp): bool
    {
        return $this->smsService->sendOtp($phone, $otp);
    }

    /**
     * Generate OTP and send it to the phone number.
     */
    public function generateAndSend(string $phone): bool
    {
        $customer = app(Customer::class)::where('phone', $phone)->first();

        if (! $customer) {
            return false;
        }

        $otpData = $this->generateOtp($phone);

        if (!$otpData) {
            return false;
        }

        $customer->update([
            'otp_code' => $otpData['hashed'],
            'otp_expires_at' => $otpData['expires_at'],
        ]);

        return $this->sendOtp($phone, $otpData['plain']);
    }

    /**
     * Verify the OTP for the given phone number.
     */
    public function verify(string $phone, string $otp): bool
    {
        $customer = app(Customer::class)::where('phone', $phone)->first();

        if (! $customer || ! $customer->otp_code) {
            return false;
        }

        if ($customer->otp_expires_at && $customer->otp_expires_at->isPast()) {
            return false;
        }

        if (! Hash::check($otp, $customer->otp_code)) {
            return false;
        }

        // Clear OTP after successful verification
        $customer->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return true;
    }

    /**
     * Check if OTP can be resent (60-second cooldown).
     */
    public function canResend(string $phone): bool
    {
        $customer = app(Customer::class)::where('phone', $phone)->first();

        if (! $customer || ! $customer->otp_expires_at) {
            return true;
        }

        // otp_expires_at = sent_at + 5 minutes, so sent_at = otp_expires_at - 5 minutes
        $sentAt = $customer->otp_expires_at->copy()->subMinutes(5);

        // Enforce 60-second cooldown from the time OTP was sent
        return $sentAt->copy()->addSeconds(60)->isPast();
    }
}
