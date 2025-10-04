<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\Models\Builders\OrdersCaptureBuilder;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Http\ApiResponse;
use PaypalServerSdkLib\Models\Builders\NameBuilder;
use PaypalServerSdkLib\Models\Builders\OrderBuilder;
use PaypalServerSdkLib\Models\Builders\PayerBuilder;
use PaypalServerSdkLib\Models\Builders\PaymentCollectionBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitBuilder;
use PaypalServerSdkLib\PaypalServerSdkClient;

/**
 * PayPal API Model
 *
 * This class provides methods to interact with the PayPal REST API.
 * It handles order creation, authorization, capture, refund, and void operations.
 */
class Mage_Paypal_Model_Api extends Varien_Object
{
    // API preference constants
    public const PREFER_RETURN_REPRESENTATION = 'return=representation';

    // Error messages
    public const ERROR_INVALID_QUOTE = 'Invalid quote provided';
    public const ERROR_EMPTY_ORDER_ID = 'Order ID cannot be empty';
    public const ERROR_EMPTY_CAPTURE_ID = 'Capture ID cannot be empty';
    public const ERROR_EMPTY_AUTH_ID = 'Authorization ID cannot be empty';
    public const ERROR_INVALID_AMOUNT = 'Amount must be greater than 0';

    private readonly Mage_Paypal_Model_Helper $helper;
    private readonly Mage_Paypal_Model_Config $config;
    private ?PaypalServerSdkClient $client = null;

    /**
     * Initialize API dependencies
     */
    public function __construct()
    {
        parent::__construct();
        $this->helper = Mage::getSingleton('paypal/helper');
        $this->config = Mage::getSingleton('paypal/config');
    }

    /**
     * Create a new PayPal order with a pre-built request
     *
     * @param object $orderRequest Pre-built order request object
     * @param string $paypalRequestId PayPal request ID
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote Customer quote or order
     * @throws Mage_Paypal_Model_Exception
     */
    public function createOrder(
        Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote,
        object $orderRequest,
        string $paypalRequestId
    ): ?ApiResponse {
        $this->_validateQuote($quote);

        try {
            $request = [
                'prefer' => self::PREFER_RETURN_REPRESENTATION,
                'body' => $orderRequest,
                'paypalRequestId' => $paypalRequestId,
            ];
            $response = $this->getClient()->getOrdersController()->createOrder($request);

            $this->helper->logDebug('Create Order', $quote, $request, $response);
            return $response;
        } catch (Exception $e) {
            $this->_logAndThrowError('Create Order Error', $e);
        }
    }

    /**
     * Authorize payment for a PayPal order
     *
     * @param string $id PayPal order ID
     * @throws Mage_Paypal_Model_Exception
     */
    public function authorizeOrder(
        string $id,
        Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote
    ): ?ApiResponse {
        $this->_validateOrderId($id);

        try {
            $request = [
                'id' => $id,
                'prefer' => self::PREFER_RETURN_REPRESENTATION,
            ];

            $response = $this->getClient()->getOrdersController()->authorizeOrder($request);

            $this->helper->logDebug('Authorize Order', $quote, $request, $response);
            return $response;
        } catch (Exception $e) {
            $this->_logAndThrowError('Authorize Order Error', $e);
        }
    }
    /**
     * Authorize payment for a PayPal order
     *
     * @param string $id PayPal order ID
     * @throws Mage_Paypal_Model_Exception
     */
    public function reAuthorizeOrder(
        string $id,
        Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote
    ): ?ApiResponse {
        $this->_validateAuthorizationId($id);

        try {
            $request = [
                'authorizationId' => $id,
                'prefer' => self::PREFER_RETURN_REPRESENTATION,
            ];

            $response = $this->getClient()->getPaymentsController()->reauthorizePayment($request);

            $this->helper->logDebug('Reauthorize Order', $quote, $request, $response);
            return $response;
        } catch (Exception $e) {
            $this->_logAndThrowError('Reauthorize Order Error', $e);
        }
    }

    /**
     * Capture authorized payment for a PayPal order
     *
     * @param string $id PayPal order ID
     * @throws Mage_Paypal_Model_Exception
     */
    public function captureAuthorizedPayment(string $id, Mage_Sales_Model_Order $order): ?ApiResponse
    {
        $this->_validateOrderId($id);

        try {
            $request = [
                'authorizationId' => $id,
                'prefer' => self::PREFER_RETURN_REPRESENTATION,
            ];
            $response = $this->getClient()->getPaymentsController()->captureAuthorizedPayment($request);
            $this->helper->logDebug('Capture Authorized Payment', $order, $request, $response);
            return $response;
        } catch (Exception $e) {
            $this->_logAndThrowError('Capture Authorized Order Error', $e);
        }
    }

    /**
     * Capture payment for a PayPal order
     *
     * @param string $id PayPal order ID
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote Customer quote or order
     * @param string $paypalRequestId PayPal request ID
     * @throws Mage_Paypal_Model_Exception
     */
    public function captureOrder(
        string $id,
        Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote,
        string $paypalRequestId
    ): ?ApiResponse {
        $this->_validateOrderId($id);

        try {
            $request = [
                'id' => $id,
                'prefer' => self::PREFER_RETURN_REPRESENTATION,
                'paypalRequestId' => $paypalRequestId,
            ];

            $response = $this->getClient()->getOrdersController()->captureOrder($request);

            $this->helper->logDebug('Capture Order', $quote, $request, $response);

            return $response;
        } catch (Exception $e) {
            if ($this->_isPhoneNumberMappingError($e)) {
                $response = $this->_handlePhoneNumberMappingError($id, $quote, $e);
                $this->helper->logDebug('Manual Capture Order', $quote, $request, $response);
                return $response;
            }
            $this->_logAndThrowError('Capture Order Error', $e);
        }
    }

    /**
     * Refund a captured payment
     *
     * @param string $captureId PayPal capture ID
     * @param float $amount Amount to refund
     * @param string $currencyCode Currency code
     * @throws Mage_Paypal_Model_Exception
     */
    public function refundCapturedPayment(
        string $captureId,
        float $amount,
        string $currencyCode,
        Mage_Sales_Model_Order $order
    ): ?ApiResponse {
        $this->_validateCaptureId($captureId);
        $this->_validateAmount($amount);

        try {
            $request = [
                'captureId' => $captureId,
                'prefer' => self::PREFER_RETURN_REPRESENTATION,
                'body' => $this->_buildRefundBody($amount, $currencyCode),
            ];
            $response = $this->getClient()->getPaymentsController()->refundCapturedPayment($request);

            $this->helper->logDebug('Refund Payment', $order, $request, $response);
            return $response;
        } catch (Exception $e) {
            $this->_logAndThrowError('Refund Payment Error', $e);
        }
    }

    /**
     * Void an authorization
     *
     * @param string $authorizationId PayPal authorization ID
     * @throws Mage_Paypal_Model_Exception
     */
    public function voidPayment(string $authorizationId, Mage_Sales_Model_Order $order): ?ApiResponse
    {
        $this->_validateAuthorizationId($authorizationId);

        try {
            $request = [
                'authorizationId' => $authorizationId,
                'prefer' => self::PREFER_RETURN_REPRESENTATION,
            ];
            $response = $this->getClient()->getPaymentsController()->voidPayment($request);

            $this->helper->logDebug('Void Authorization', $order, $request, $response);
            return $response;
        } catch (Exception $e) {
            $this->_logAndThrowError('Void Authorization Error', $e);
        }
    }

    /**
     * Get PayPal API client instance with lazy loading
     *
     * @throws Mage_Paypal_Model_Exception
     */
    public function getClient(): PaypalServerSdkClient
    {
        if (!$this->client instanceof PaypalServerSdkClient) {
            $this->_initializeClient();
        }
        return $this->client;
    }

    /**
     * Reset client instance (useful for testing or config changes)
     *
     * @return $this
     */
    public function resetClient(): self
    {
        $this->client = null;
        return $this;
    }

    /**
     * Initialize PayPal client
     *
     * @throws Mage_Paypal_Model_Exception
     */
    private function _initializeClient(): void
    {
        try {
            $credentials = $this->config->getApiCredentials();
            $environment = $this->config->isSandbox() ? Environment::SANDBOX : Environment::PRODUCTION;

            $this->client = PaypalServerSdkClientBuilder::init()
                ->clientCredentialsAuthCredentials(
                    ClientCredentialsAuthCredentialsBuilder::init(
                        $credentials['client_id'],
                        $credentials['client_secret'],
                    ),
                )
                ->environment($environment)
                ->build();
        } catch (Exception $e) {
            throw new Mage_Paypal_Model_Exception('Failed to initialize PayPal client: ' . $e->getMessage(), [], $e);
        }
    }

    /**
     * Validate quote object
     *
     * @throws Mage_Paypal_Model_Exception
     */
    private function _validateQuote(mixed $quote): void
    {
        if (!$quote || !$quote->hasItems()) {
            throw new Mage_Paypal_Model_Exception(self::ERROR_INVALID_QUOTE);
        }
    }

    /**
     * Validate order ID
     *
     * @throws Mage_Paypal_Model_Exception
     */
    private function _validateOrderId(string $id): void
    {
        if (empty($id)) {
            throw new Mage_Paypal_Model_Exception(self::ERROR_EMPTY_ORDER_ID);
        }
    }

    /**
     * Validate capture ID
     *
     * @throws Mage_Paypal_Model_Exception
     */
    private function _validateCaptureId(string $captureId): void
    {
        if (empty($captureId)) {
            throw new Mage_Paypal_Model_Exception(self::ERROR_EMPTY_CAPTURE_ID);
        }
    }

    /**
     * Validate authorization ID
     *
     * @throws Mage_Paypal_Model_Exception
     */
    private function _validateAuthorizationId(string $authorizationId): void
    {
        if (empty($authorizationId)) {
            throw new Mage_Paypal_Model_Exception(self::ERROR_EMPTY_AUTH_ID);
        }
    }

    /**
     * Validate amount
     *
     * @throws Mage_Paypal_Model_Exception
     */
    private function _validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new Mage_Paypal_Model_Exception(self::ERROR_INVALID_AMOUNT);
        }
    }

    /**
     * Build refund request body
     *
     * @return array<string, mixed>
     */
    private function _buildRefundBody(float $amount, string $currencyCode): array
    {
        return [
            'amount' => [
                'value' => number_format($amount, 2, '.', ''),
                'currency_code' => $currencyCode,
            ],
        ];
    }

    /**
     * Log error and throw exception
     *
     * @throws Exception
     */
    private function _logAndThrowError(string $message, Exception $e): never
    {
        $this->helper->logError($message, $e);
        throw $e;
    }

    /**
     * @TODO: Temporary workaround for PhoneNumberWithCountryCode mapping issues
     * https://github.com/paypal/PayPal-PHP-Server-SDK/issues/46
     * Check if exception is related to PhoneNumberWithCountryCode mapping
     */
    private function _isPhoneNumberMappingError(Exception $e): bool
    {
        return str_contains($e->getMessage(), 'PhoneNumberWithCountryCode') &&
            str_contains($e->getMessage(), 'countryCode');
    }

    /**
     * Handle PhoneNumberWithCountryCode mapping error by making raw API call
     */
    private function _handlePhoneNumberMappingError(
        string $orderId,
        Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote,
        Exception $originalException
    ): ApiResponse {
        try {
            $rawResponse = $this->_makeRawApiCall($orderId);
            $processedResponse = $this->_processPhoneNumberFields($rawResponse);

            $this->helper->logDebug('Capture Order (Phone Fix)', $quote, ['id' => $orderId], null);
            return $this->_createApiResponseFromProcessedData($processedResponse);
        } catch (Exception $e) {
            $this->helper->logError('Failed to handle phone number mapping error', $e);
            throw $originalException;
        }
    }

    /**
     * @TODO: Temporary workaround for PhoneNumberWithCountryCode mapping issues
     * https://github.com/paypal/PayPal-PHP-Server-SDK/issues/46
     * Make raw API call without SDK model mapping
     */
    private function _makeRawApiCall(string $orderId): array
    {
        $environment = $this->config->isSandbox() ? 'sandbox' : 'production';
        $baseUrl = $environment === 'sandbox' ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com';

        $credentials = $this->config->getApiCredentials();
        $accessToken = $this->_getAccessToken($credentials);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "{$baseUrl}/v2/checkout/orders/{$orderId}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$accessToken}",
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("API call failed with status: $httpCode" . " and response: {$response}");
        }

        return json_decode($response, true);
    }

    /**
     * @TODO: Temporary workaround for PhoneNumberWithCountryCode mapping issues
     * https://github.com/paypal/PayPal-PHP-Server-SDK/issues/46
     * Process phone number fields to handle missing country codes
     */
    private function _processPhoneNumberFields(array $responseData): array
    {
        array_walk_recursive($responseData, function (&$value, $key) {
            if ($key === 'phone_number' && is_array($value)) {
                if (isset($value['national_number']) && !isset($value['country_code'])) {
                    $value['country_code'] = '1';
                }
            }
        });

        return $responseData;
    }

    /**
     * @TODO: Temporary workaround for PhoneNumberWithCountryCode mapping issues
     * https://github.com/paypal/PayPal-PHP-Server-SDK/issues/46
     * Create API response from processed data
     */
    private function _createApiResponseFromProcessedData(array $processedData): ApiResponse
    {
        $purchaseUnits = [];
        if (!empty($processedData['purchase_units']) && is_array($processedData['purchase_units'])) {
            foreach ($processedData['purchase_units'] as $purchaseUnit) {
                $captures = [];
                if (isset($purchaseUnit['payments']['captures']) && is_array($purchaseUnit['payments']['captures'])) {
                    foreach ($purchaseUnit['payments']['captures'] as $capture) {
                        $captures[] = OrdersCaptureBuilder::init()
                            ->id($capture['id'] ?? null)
                            ->status($capture['status'] ?? null)
                            ->build();
                    }
                }
                $purchaseUnits[] = PurchaseUnitBuilder::init()
                    ->payments(
                        PaymentCollectionBuilder::init()
                            ->captures($captures)
                            ->build(),
                    )
                    ->build();
            }
        }

        $payer = null;
        if (!empty($processedData['payer'])) {
            $payerName = null;
            if (!empty($processedData['payer']['name'])) {
                $payerName = NameBuilder::init()
                    ->givenName($processedData['payer']['name']['given_name'] ?? null)
                    ->surname($processedData['payer']['name']['surname'] ?? null)
                    ->build();
            }
            $payer = PayerBuilder::init()
                ->name($payerName)
                ->emailAddress($processedData['payer']['email_address'] ?? null)
                ->payerId($processedData['payer']['payer_id'] ?? null)
                ->build();
        }

        $order = OrderBuilder::init()
            ->createTime($processedData['create_time'] ?? null)
            ->updateTime($processedData['update_time'] ?? null)
            ->id($processedData['id'] ?? null)
            ->payer($payer)
            ->intent($processedData['intent'] ?? null)
            ->purchaseUnits($purchaseUnits)
            ->status($processedData['status'] ?? null)
            ->links($processedData['links'] ?? [])
            ->build();

        return new ApiResponse(
            200,
            200,
            null,
            ['Content-Type' => 'application/json'],
            $order,
            json_encode($processedData),
        );
    }

    /**
     * @TODO: Temporary workaround for PhoneNumberWithCountryCode mapping issues
     * https://github.com/paypal/PayPal-PHP-Server-SDK/issues/46
     * Get access token for direct API calls
     */
    private function _getAccessToken(array $credentials): string
    {
        $environment = $this->config->isSandbox() ? 'sandbox' : 'production';
        $baseUrl = $environment === 'sandbox' ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "{$baseUrl}/v1/oauth2/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Accept-Language: en_US',
            ],
            CURLOPT_USERPWD => "{$credentials['client_id']}:{$credentials['client_secret']}",
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Token request failed with status: $httpCode");
        }

        $tokenData = json_decode($response, true);
        return $tokenData['access_token'];
    }
}
