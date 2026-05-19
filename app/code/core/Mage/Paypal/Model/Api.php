<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Http\ApiResponse;
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
     * @param  Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote           Customer quote or order
     * @param  object                                        $orderRequest    Pre-built order request object
     * @param  string                                        $paypalRequestId PayPal request ID
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
        } catch (Exception $exception) {
            $this->_logAndThrowError('Create Order Error', $exception);
        }
    }

    /**
     * Authorize payment for a PayPal order
     *
     * @param  string                      $id PayPal order ID
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
        } catch (Exception $exception) {
            $this->_logAndThrowError('Authorize Order Error', $exception);
        }
    }

    /**
     * Authorize payment for a PayPal order
     *
     * @param  string                      $id PayPal order ID
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
        } catch (Exception $exception) {
            $this->_logAndThrowError('Reauthorize Order Error', $exception);
        }
    }

    /**
     * Capture authorized payment for a PayPal order
     *
     * @param  string                      $id PayPal order ID
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
        } catch (Exception $exception) {
            $this->_logAndThrowError('Capture Authorized Order Error', $exception);
        }
    }

    /**
     * Capture payment for a PayPal order
     *
     * @param  string                                        $id              PayPal order ID
     * @param  Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote           Customer quote or order
     * @param  string                                        $paypalRequestId PayPal request ID
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
        } catch (Exception $exception) {
            $this->_logAndThrowError('Capture Order Error', $exception);
        }
    }

    /**
     * Refund a captured payment
     *
     * @param  string                      $captureId    PayPal capture ID
     * @param  float                       $amount       Amount to refund
     * @param  string                      $currencyCode Currency code
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
        } catch (Exception $exception) {
            $this->_logAndThrowError('Refund Payment Error', $exception);
        }
    }

    /**
     * Void an authorization
     *
     * @param  string                      $authorizationId PayPal authorization ID
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
        } catch (Exception $exception) {
            $this->_logAndThrowError('Void Authorization Error', $exception);
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
            $this->client = $this->_initializeClient();
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
     * Scope the API client to a specific store.
     *
     * Admin, cron and webhook paths have no frontend store context, so the
     * client must be rebuilt against the order's store to pick up
     * website-scoped PayPal credentials and sandbox mode.
     *
     * @param mixed $store store id or object
     * @return $this
     */
    public function setStore(mixed $store): self
    {
        $this->config->setStoreId((int) Mage::app()->getStore($store)->getId());
        return $this->resetClient();
    }

    /**
     * Fetch a PayPal REST access token from the SDK OAuth manager.
     */
    public function getAccessToken(): string
    {
        return $this->getClient()->getClientCredentialsAuth()->fetchToken()->getAccessToken();
    }

    /**
     * POST to a PayPal REST endpoint that is not modeled by the SDK.
     *
     * @param  array<string, mixed>        $body
     * @param  array<string, string>       $headers
     * @return array<string, mixed>
     * @throws Mage_Paypal_Model_Exception
     */
    public function postPaypalRest(string $path, array $body, array $headers = []): array
    {
        $payload = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!is_string($payload)) {
            throw new Mage_Paypal_Model_Exception('Failed to encode PayPal REST request body.');
        }

        $client = new Varien_Http_Client(
            rtrim($this->getClient()->getBaseUri(), '/') . '/' . ltrim($path, '/'),
        );
        $client->setConfig([
            'maxredirects' => 0,
            'timeout'      => $this->config->getApiTimeout() ?: 30,
            'verifyhost'   => 2,
            'verifypeer'   => true,
        ]);

        $requestHeaders = [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];
        foreach ($headers as $name => $value) {
            if ($name === '') {
                continue;
            }

            $requestHeaders[$name] = $value;
        }

        $response = $client
            ->setHeaders($requestHeaders)
            ->setRawData($payload, 'application/json')
            ->request(Zend_Http_Client::POST);

        $statusCode = (int) $response->getStatus();
        $responseBody = (string) $response->getBody();
        $decodedResponse = $responseBody === '' ? [] : json_decode($responseBody, true);
        if (!is_array($decodedResponse)) {
            throw new Mage_Paypal_Model_Exception('PayPal REST response was not valid JSON.');
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            $message = $decodedResponse['message']
                ?? $decodedResponse['error_description']
                ?? $decodedResponse['name']
                ?? 'HTTP ' . $statusCode . ' error';
            if (!is_scalar($message)) {
                $message = 'HTTP ' . $statusCode . ' error';
            }

            throw new Mage_Paypal_Model_Exception(
                'PayPal REST request failed: ' . $message,
                [
                    'status_code' => $statusCode,
                    'response'    => $decodedResponse,
                ],
            );
        }

        return $decodedResponse;
    }

    /**
     * Read a PayPal order through the SDK.
     *
     * @throws Mage_Paypal_Model_Exception
     */
    public function getOrder(string $orderId): ApiResponse
    {
        $this->_validateOrderId($orderId);
        return $this->getClient()->getOrdersController()->getOrder(['id' => $orderId]);
    }

    /**
     * Read a captured PayPal payment through the SDK.
     *
     * @throws Mage_Paypal_Model_Exception
     */
    public function getCapturedPayment(string $captureId): ApiResponse
    {
        $this->_validateCaptureId($captureId);
        return $this->getClient()->getPaymentsController()->getCapturedPayment(['captureId' => $captureId]);
    }

    /**
     * Read a PayPal refund through the SDK.
     *
     * @throws Mage_Paypal_Model_Exception
     */
    public function getRefund(string $refundId): ApiResponse
    {
        if ($refundId === '') {
            throw new Mage_Paypal_Model_Exception('Refund ID cannot be empty');
        }

        return $this->getClient()->getPaymentsController()->getRefund(['refundId' => $refundId]);
    }

    /**
     * Read an authorized PayPal payment through the SDK.
     *
     * @throws Mage_Paypal_Model_Exception
     */
    public function getAuthorizedPayment(string $authorizationId): ApiResponse
    {
        $this->_validateAuthorizationId($authorizationId);
        return $this->getClient()->getPaymentsController()->getAuthorizedPayment(['authorizationId' => $authorizationId]);
    }

    /**
     * Initialize PayPal client
     *
     * @throws Mage_Paypal_Model_Exception
     */
    private function _initializeClient(): PaypalServerSdkClient
    {
        try {
            $credentials = $this->config->getApiCredentials();
            $environment = $this->config->isSandbox() ? Environment::SANDBOX : Environment::PRODUCTION;

            $builder = PaypalServerSdkClientBuilder::init()
                ->clientCredentialsAuthCredentials(
                    ClientCredentialsAuthCredentialsBuilder::init(
                        $credentials['client_id'],
                        $credentials['client_secret'],
                    ),
                )
                ->environment($environment);

            $apiTimeout = $this->config->getApiTimeout();
            if ($apiTimeout > 0) {
                $builder->timeout($apiTimeout);
            }

            $retry = $this->config->getRetryConfiguration();
            if ($retry['enabled']) {
                $builder
                    ->enableRetries(true)
                    ->numberOfRetries($retry['number_of_retries'])
                    ->retryInterval($retry['retry_interval'])
                    ->backOffFactor($retry['backoff_factor'])
                    ->maximumRetryWaitTime($retry['maximum_retry_wait_time'])
                    ->retryOnTimeout($retry['retry_on_timeout'])
                    ->httpStatusCodesToRetry($retry['http_status_codes'])
                    ->httpMethodsToRetry($retry['http_methods']);
            }

            return $builder->build();
        } catch (Exception $exception) {
            throw new Mage_Paypal_Model_Exception('Failed to initialize PayPal client: ' . $exception->getMessage(), [], $exception);
        }
    }

    /**
     * Validate quote object
     *
     * @throws Mage_Paypal_Model_Exception
     */
    private function _validateQuote(mixed $quote): void
    {
        if (!$quote instanceof Varien_Object || !$quote->hasItems()) {
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
        if ($id === '') {
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
        if ($captureId === '') {
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
        if ($authorizationId === '') {
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
                // PayPal rejects decimals for zero-decimal currencies (JPY,
                // HUF, TWD); format with the precision PayPal expects.
                'value' => Mage::helper('paypal')->formatPrice($amount, $currencyCode),
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
}
