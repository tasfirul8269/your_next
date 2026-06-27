<?php

namespace Frooxi\Customer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslWirelessSmsService implements SmsService
{
    public function sendOtp(string $phone, string $otp): bool
    {
        $message = "Your Next Outfit verification code is - {$otp}.\nIt expires in 5 minutes.\nPlease do not share this code with anyone for your account security.";

        if (config('sslwireless.mock')) {
            Log::info("Mock SMS OTP to {$phone}: {$otp}");

            return true;
        }

        try {
            Log::info('Attempting to send SSL Wireless SMS', [
                'phone' => $phone,
                'url' => config('sslwireless.url'),
                'sid' => config('sslwireless.sid'),
            ]);

            $response = Http::timeout(30)->post(config('sslwireless.url'), [
                'api_token' => config('sslwireless.api_token'),
                'sid' => config('sslwireless.sid'),
                'msisdn' => $phone,
                'sms' => $message,
                'csms_id' => strtoupper(bin2hex(random_bytes(10))),
            ]);

            Log::info('SSL Wireless API Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                Log::info("SSL Wireless SMS sent successfully to {$phone}");

                return true;
            }

            Log::error('SSL Wireless SMS failed', [
                'phone' => $phone,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('SSL Wireless SMS exception', [
                'phone' => $phone,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }
}
