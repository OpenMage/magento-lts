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
     * @param object $orderRequest Pre-built order request object
     * @throws Mage_Core_Exception
     */
    public function createOrder(
        Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote,
        object $orderRequest
    ): ?ApiResponse {
        $this->_validateQuote($quote);

        try {
            $request = [
                'prefer' => self::PREFER_RETURN_REPRESENTATION,
                'body' => $orderRequest,
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
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
     */
    public function captureOrder(
        string $id,
        Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote
    ): ?ApiResponse {
        $this->_validateOrderId($id);

        try {
            $request = [
                'id' => $id,
                'prefer' => self::PREFER_RETURN_REPRESENTATION,
            ];

            $response = $this->getClient()->getOrdersController()->captureOrder($request);

            $this->helper->logDebug('Capture Order', $quote, $request, $response);

            return $response;
        } catch (Exception $e) {
            $this->_logAndThrowError('Capture Order Error', $e);
        }
    }

    /**
     * Refund a captured payment
     *
     * @param string $captureId PayPal capture ID
     * @param float $amount Amount to refund
     * @param string $currencyCode Currency code
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
     */
    public function getClient(): PaypalServerSdkClient
    {
        if ($this->client === null) {
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
     * @throws Mage_Core_Exception
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
            throw new Mage_Core_Exception('Failed to initialize PayPal client: ' . $e->getMessage());
        }
    }

    /**
     * Validate quote object
     *
     * @throws Mage_Core_Exception
     */
    private function _validateQuote(mixed $quote): void
    {
        if (!$quote || !$quote->hasItems()) {
            throw new Mage_Core_Exception(self::ERROR_INVALID_QUOTE);
        }
    }

    /**
     * Validate order ID
     *
     * @throws Mage_Core_Exception
     */
    private function _validateOrderId(string $id): void
    {
        if (empty($id)) {
            throw new Mage_Core_Exception(self::ERROR_EMPTY_ORDER_ID);
        }
    }

    /**
     * Validate capture ID
     *
     * @throws Mage_Core_Exception
     */
    private function _validateCaptureId(string $captureId): void
    {
        if (empty($captureId)) {
            throw new Mage_Core_Exception(self::ERROR_EMPTY_CAPTURE_ID);
        }
    }

    /**
     * Validate authorization ID
     *
     * @throws Mage_Core_Exception
     */
    private function _validateAuthorizationId(string $authorizationId): void
    {
        if (empty($authorizationId)) {
            throw new Mage_Core_Exception(self::ERROR_EMPTY_AUTH_ID);
        }
    }

    /**
     * Validate amount
     *
     * @throws Mage_Core_Exception
     */
    private function _validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new Mage_Core_Exception(self::ERROR_INVALID_AMOUNT);
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
}
