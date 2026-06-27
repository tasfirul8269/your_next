<?php

namespace Frooxi\Payment\Library\SSLCommerz;

class SslCommerzNotification extends AbstractSslCommerz
{
    /**
     * Payment data array.
     *
     * @var array
     */
    protected $data = [];

    /**
     * SSLCommerz config.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Last error message.
     *
     * @var string
     */
    private $error;

    /**
     * Last validated response data.
     *
     * @var object|null
     */
    public $sslc_data;

    /**
     * SslCommerzNotification constructor.
     */
    public function __construct()
    {
        $this->config = config('sslcommerz');

        $this->setStoreId($this->config['apiCredentials']['store_id']);
        $this->setStorePassword($this->config['apiCredentials']['store_password']);
    }

    /**
     * Initiate a payment request and redirect or return gateway URL.
     *
     * @param  string  $type  'hosted' = server-side redirect | 'checkout' = return JSON
     * @param  string  $pattern
     * @return false|mixed|string
     */
    public function makePayment(array $requestData, $type = 'checkout', $pattern = 'json')
    {
        if (empty($requestData)) {
            return 'Please provide a valid information list about the transaction.';
        }

        $this->setApiUrl($this->config['apiDomain'].$this->config['apiUrl']['make_payment']);

        $this->setParams($requestData);

        $this->setAuthenticationInfo();

        $response = $this->callToApi($this->data, [], $this->config['connect_from_localhost']);

        // Always format as 'checkout' to get a consistent JSON string back,
        // then handle the 'hosted' redirect ourselves.
        $formattedResponse = $this->formatResponse($response, 'checkout', 'json');

        if ($type === 'hosted') {
            $decoded = json_decode($formattedResponse, true);

            if (! empty($decoded['data'])) {
                $this->redirect($decoded['data']);
            } else {
                return $decoded['message'] ?? 'Failed to connect with SSLCommerz.';
            }
        }

        return $formattedResponse;
    }

    /**
     * Validate an SSLCommerz transaction after payment callback.
     *
     * @param  array  $postData
     * @param  string  $tranId
     * @param  float  $amount
     * @param  string  $currency
     * @return bool
     */
    public function orderValidate($postData, $tranId = '', $amount = 0, $currency = 'BDT')
    {
        if (empty($postData) || empty($tranId) || ! is_array($postData)) {
            $this->error = 'Please provide valid transaction ID and post request data.';

            return false;
        }

        return $this->validate($tranId, $amount, $currency, $postData);
    }

    /**
     * Verify the hash signature on the IPN/callback post.
     *
     * @param  array  $postData
     * @return bool
     */
    public function hashVerify($postData)
    {
        return $this->SSLCOMMERZ_hash_verify($postData, $this->getStorePassword());
    }

    /**
     * Get last error message.
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    // ─── Private/Protected helpers ───────────────────────────────────────────

    /**
     * Call the SSLCommerz validation API.
     *
     * @param  string  $merchantTransId
     * @param  float  $merchantTransAmount
     * @param  string  $merchantTransCurrency
     * @param  array  $postData
     * @return bool
     */
    protected function validate($merchantTransId, $merchantTransAmount, $merchantTransCurrency, $postData)
    {
        if (empty($merchantTransId) || empty($merchantTransAmount)) {
            $this->error = 'Invalid data';

            return false;
        }

        $postData['store_id'] = $this->getStoreId();
        $postData['store_pass'] = $this->getStorePassword();

        $valId = urlencode($postData['val_id']);
        $storeId = urlencode($this->getStoreId());
        $storePasswd = urlencode($this->getStorePassword());

        $requestedUrl = $this->config['apiDomain']
            .$this->config['apiUrl']['order_validate']
            ."?val_id={$valId}&store_id={$storeId}&store_passwd={$storePasswd}&v=1&format=json";

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $requestedUrl);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        if ($this->config['connect_from_localhost']) {
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
        } else {
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 2);
        }

        $result = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        if ($code !== 200 || ! $result) {
            $this->error = 'Failed to connect with SSLCOMMERZ.';

            return false;
        }

        $result = json_decode($result);
        $this->sslc_data = $result;
        $status = $result->status ?? '';

        if (! in_array($status, ['VALID', 'VALIDATED'])) {
            $this->error = 'Failed Transaction';

            return false;
        }

        // Amount / currency cross-check to prevent data tampering
        if ($merchantTransCurrency === 'BDT') {
            $valid = trim($merchantTransId) === trim($result->tran_id)
                && abs($merchantTransAmount - $result->amount) < 1
                && trim($merchantTransCurrency) === trim('BDT');
        } else {
            $valid = trim($merchantTransId) === trim($result->tran_id)
                && abs($merchantTransAmount - $result->currency_amount) < 1
                && trim($merchantTransCurrency) === trim($result->currency_type);
        }

        if (! $valid) {
            $this->error = 'Data has been tampered.';

            return false;
        }

        return true;
    }

    /**
     * Verify SSLCOMMERZ hash signature.
     *
     * @param  array  $postData
     * @param  string  $storePasswd
     * @return bool
     */
    protected function SSLCOMMERZ_hash_verify($postData, $storePasswd = '')
    {
        if (
            ! isset($postData['verify_sign'])
            || ! isset($postData['verify_key'])
        ) {
            $this->error = 'Required data missing: verify_key, verify_sign';

            return false;
        }

        $preDefineKey = explode(',', $postData['verify_key']);
        $newData = [];

        foreach ($preDefineKey as $value) {
            $newData[$value] = $postData[$value] ?? '';
        }

        $newData['store_passwd'] = md5($storePasswd);
        ksort($newData);

        $hashString = '';
        foreach ($newData as $key => $value) {
            $hashString .= $key.'='.$value.'&';
        }
        $hashString = rtrim($hashString, '&');

        if (md5($hashString) === $postData['verify_sign']) {
            return true;
        }

        $this->error = 'Verification signature not matched.';

        return false;
    }

    /**
     * Populate all parameter groups.
     *
     * @param  array  $requestData
     */
    protected function setParams($requestData)
    {
        $this->setRequiredInfo($requestData);
        $this->setCustomerInfo($requestData);
        $this->setShipmentInfo($requestData);
        $this->setProductInfo($requestData);
        $this->setAdditionalInfo($requestData);
    }

    /**
     * Set authentication credentials on the data payload.
     */
    protected function setAuthenticationInfo()
    {
        $this->data['store_id'] = $this->getStoreId();
        $this->data['store_passwd'] = $this->getStorePassword();
    }

    /**
     * Required integration parameters.
     *
     * @param  array  $requestData
     */
    protected function setRequiredInfo($requestData)
    {
        $requiredKeys = [
            'total_amount', 'currency', 'tran_id',
            'success_url', 'fail_url', 'cancel_url',
        ];

        foreach ($requiredKeys as $key) {
            if (isset($requestData[$key])) {
                $this->data[$key] = $requestData[$key];
            }
        }
    }

    /**
     * Customer information parameters.
     *
     * @param  array  $requestData
     */
    protected function setCustomerInfo($requestData)
    {
        $customerKeys = [
            'cus_name', 'cus_email', 'cus_add1', 'cus_add2',
            'cus_city', 'cus_state', 'cus_postcode', 'cus_country',
            'cus_phone', 'cus_fax',
        ];

        foreach ($customerKeys as $key) {
            if (isset($requestData[$key])) {
                $this->data[$key] = $requestData[$key];
            }
        }
    }

    /**
     * Shipment information parameters.
     *
     * @param  array  $requestData
     */
    protected function setShipmentInfo($requestData)
    {
        $shipmentKeys = [
            'ship_name', 'ship_add1', 'ship_add2',
            'ship_city', 'ship_state', 'ship_postcode',
            'ship_country', 'shipping_method',
        ];

        foreach ($shipmentKeys as $key) {
            if (isset($requestData[$key])) {
                $this->data[$key] = $requestData[$key];
            }
        }
    }

    /**
     * Product information parameters.
     *
     * @param  array  $requestData
     */
    protected function setProductInfo($requestData)
    {
        $productKeys = [
            'product_name', 'product_category', 'product_profile',
            'product_type', 'num_of_item',
        ];

        foreach ($productKeys as $key) {
            if (isset($requestData[$key])) {
                $this->data[$key] = $requestData[$key];
            }
        }
    }

    /**
     * Additional / optional parameters.
     *
     * @param  array  $requestData
     */
    protected function setAdditionalInfo($requestData)
    {
        $additionalKeys = [
            'value_a', 'value_b', 'value_c', 'value_d', 'ipn_url',
            'card_brand', 'cart', 'product_amount', 'discount_amount',
            'convenience_fee', 'vat', 'emis_qnt',
        ];

        foreach ($additionalKeys as $key) {
            if (isset($requestData[$key])) {
                $this->data[$key] = $requestData[$key];
            }
        }
    }
}
