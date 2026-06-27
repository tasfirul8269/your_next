<?php

namespace Frooxi\Payment\Library\SSLCommerz;

abstract class AbstractSslCommerz
{
    protected $apiUrl;

    protected $storeId;

    protected $storePassword;

    protected function setStoreId($storeID)
    {
        $this->storeId = $storeID;
    }

    protected function getStoreId()
    {
        return $this->storeId;
    }

    protected function setStorePassword($storePassword)
    {
        $this->storePassword = $storePassword;
    }

    protected function getStorePassword()
    {
        return $this->storePassword;
    }

    protected function setApiUrl($url)
    {
        $this->apiUrl = $url;
    }

    protected function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Make a cURL POST call to the SSLCommerz API.
     *
     * @param  array  $data
     * @param  array  $header
     * @param  bool  $setLocalhost
     * @return bool|string
     */
    public function callToApi($data, $header = [], $setLocalhost = false)
    {
        $curl = curl_init();

        if (! $setLocalhost) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        }

        curl_setopt($curl, CURLOPT_URL, $this->getApiUrl());
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($curl);
        $curlErrorNo = curl_errno($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($code == 200 && ! $curlErrorNo) {
            return $response;
        }

        return 'FAILED TO CONNECT WITH SSLCOMMERZ API';
    }

    /**
     * Format the API response for checkout or hosted payment.
     *
     * @param  string  $response
     * @param  string  $type
     * @param  string  $pattern
     * @return false|mixed|string
     */
    public function formatResponse($response, $type = 'checkout', $pattern = 'json')
    {
        $sslcz = json_decode($response, true);

        if ($type !== 'checkout') {
            return $sslcz;
        }

        if (! empty($sslcz['GatewayPageURL'])) {
            $response = json_encode([
                'status' => 'success',
                'data' => $sslcz['GatewayPageURL'],
                'logo' => $sslcz['storeLogo'] ?? '',
            ]);
        } else {
            $failedReason = $sslcz['failedreason'] ?? 'Unknown error';

            if (strpos($failedReason, 'Store Credential') !== false) {
                $message = 'Check the SSLCZ_TESTMODE and SSLCZ_STORE_PASSWORD value in your .env; DO NOT USE MERCHANT PANEL PASSWORD HERE.';
            } else {
                $message = $failedReason;
            }

            $response = json_encode(['status' => 'fail', 'data' => null, 'message' => $message]);
        }

        if ($pattern === 'json') {
            return $response;
        }

        echo $response;
    }

    /**
     * Perform an HTTP redirect.
     *
     * @param  string  $url
     * @param  bool  $permanent
     */
    public function redirect($url, $permanent = false)
    {
        header('Location: '.$url, true, $permanent ? 301 : 302);

        exit();
    }
}
