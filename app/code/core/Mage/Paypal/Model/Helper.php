<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Http\ApiResponse;

/**
 * PayPal Model Helper Class
 * Handles validation, error handling, and utility methods
 */
class Mage_Paypal_Model_Helper extends Mage_Core_Model_Abstract
{
    // Error messages
    private const ERROR_ZERO_AMOUNT = 'PayPal does not support processing orders with zero amount. To complete your purchase, proceed to the standard checkout process.';

    /**
     * @var Mage_Paypal_Helper_Data
     */
    protected $_helper;

    /**
     * Initializes the helper with its dependencies.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_helper = Mage::helper('paypal');
    }

    /**
     * Logs debug information for a PayPal API request if debugging is enabled.
     *
     * @param string $action Action being performed (e.g., 'Create Order').
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote Quote or order object.
     * @param array $request Request object or data sent to the API.
     * @param ApiResponse|null $response API response, if available.
     */
    public function logDebug(
        string $action,
        Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote,
        array $request,
        ?ApiResponse $response = null
    ): void {
        if (Mage::getStoreConfigFlag('payment/paypal/debug')) {
            $requestData = json_encode($request);
            $debug = Mage::getModel('paypal/debug');
            if ($quote instanceof Mage_Sales_Model_Quote) {
                $debug->setQuoteId($quote->getId());
            } elseif ($quote instanceof Mage_Sales_Model_Order) {
                $debug->setIncrementId($quote->getIncrementId());
            }

            $debug->setAction($action)
                ->setRequestBody($requestData);
            Mage::log($response, null, 'paypal.log');
            if ($response instanceof ApiResponse) {
                $result = $response->getResult();
                if ($response->isError()) {
                    $debug->setTransactionId($result['debug_id'])
                        ->setResponseBody(json_encode($result));
                } else {
                    $debug->setTransactionId($response->getResult()->getId() ?? null)
                        ->setResponseBody(json_encode($response->getResult()));
                }
            }
            $debug->save();
        }
    }

    /**
     * Logs an error message and exception details if debugging is enabled.
     *
     * @param string $message The error message.
     * @param Exception $exception The exception object.
     */
    public function logError(string $message, Exception $exception): void
    {
        if (Mage::getStoreConfigFlag('payment/paypal/debug')) {
            $errorData = [
                'message' => $message,
                'error' => $exception->getMessage(),
            ];

            if ($exception instanceof Mage_Paypal_Model_Exception) {
                $errorData['debug_data'] = $exception->getDebugData();
            }

            Mage::log($errorData, Zend_Log::ERR, 'paypal.log', true);
        }
    }

    /**
     * Validate quote for PayPal payment processing
     *
     * @throws Mage_Paypal_Model_Exception
     */
    public function validateQuoteForPayment(Mage_Sales_Model_Quote $quote): void
    {
        $quote->collectTotals();

        if (!$quote->getGrandTotal() && !$quote->hasNominalItems()) {
            throw new Mage_Paypal_Model_Exception(
                Mage::helper('paypal')->__(self::ERROR_ZERO_AMOUNT),
            );
        }
    }

    /**
     * Handle API error response
     *
     * @param ApiResponse $response API response
     * @param string $defaultMessage Default error message
     * @throws Mage_Paypal_Model_Exception
     */
    public function handleApiError(ApiResponse $response, string $defaultMessage): never
    {
        $errorMsg = $this->extractErrorMessage($response, $defaultMessage);
        throw new Mage_Paypal_Model_Exception($errorMsg);
    }

    /**
     * Extract comprehensive error message from PayPal API response
     *
     * @param ApiResponse $response API response
     * @param string $defaultMessage Default error message
     */
    public function extractErrorMessage(ApiResponse $response, string $defaultMessage): string
    {
        $result = $response->getResult();
        if (is_array($result)) {
            $errorMessage = $this->_extractFromArrayResponse($result, $defaultMessage);
            if ($errorMessage !== $defaultMessage) {
                return $errorMessage;
            }
        }
        if (is_object($result)) {
            $errorMessage = $this->_extractFromObjectResponse($result, $defaultMessage);
            if ($errorMessage !== $defaultMessage) {
                return $errorMessage;
            }
        }
        if (is_string($result) && !empty($result)) {
            return $result;
        }
        return $defaultMessage;
    }

    /**
     * Extract error message from array response structure
     *
     * @param array $result API result array
     * @param string $defaultMessage Default error message
     */
    private function _extractFromArrayResponse(array $result, string $defaultMessage): string
    {
        $mainMessage = $result['message'] ?? '';
        $detailedMessage = $this->_extractDetailedError($result);
        if (!empty($detailedMessage)) {
            return !empty($mainMessage) ? $mainMessage . ' ' . $detailedMessage : $detailedMessage;
        }

        return !empty($mainMessage) ? $this->_helper->__($mainMessage) : $defaultMessage;
    }

    /**
     * Extract error message from object response structure
     *
     * @param object $result API result object
     * @param string $defaultMessage Default error message
     */
    private function _extractFromObjectResponse(object $result, string $defaultMessage): string
    {
        $getters = ['getMessage', 'getErrorMessage', 'getDescription'];
        foreach ($getters as $getter) {
            if (method_exists($result, $getter)) {
                $message = $result->$getter();
                if (!empty($message)) {
                    return $message;
                }
            }
        }
        $properties = ['message', 'error_message', 'description'];
        foreach ($properties as $property) {
            if (property_exists($result, $property) && !empty($result->$property)) {
                return $result->$property;
            }
        }

        return $defaultMessage;
    }

    /**
     * Extract detailed error information from PayPal error response
     *
     * @param array $result API result array
     */
    private function _extractDetailedError(array $result): string
    {
        $errorMessages = [];
        if (isset($result['details']) && is_array($result['details'])) {
            foreach ($result['details'] as $detail) {
                $issue = '';
                if (is_array($detail)) {
                    $issue = $detail['issue'] ?? '';
                } elseif (is_object($detail)) {
                    $issue = $detail->issue ?? '';
                }
                if (!empty($issue)) {
                    $errorMessages[] = $this->_getReadableErrorMessage($issue);
                }
            }
        }

        if (empty($errorMessages) && isset($result['message'])) {
            $errorMessages[] = $result['message'];
        }
        return implode('; ', $errorMessages);
    }

    /**
     * Convert PayPal error codes to user-friendly messages
     *
     * @param string $errorCode PayPal error code
     */
    private function _getReadableErrorMessage(string $errorCode): string
    {
        $errorMessages = [
            'INSTRUMENT_DECLINED' => $this->_helper->__('The instrument presented was either declined by the processor or bank, or it can\'t be used for this payment.'),
        ];

        return $errorMessages[$errorCode] ?? $errorCode;
    }

    /**
     * Extract capture ID from API result
     *
     * @param mixed $result API result
     */
    public function extractCaptureId(mixed $result): ?string
    {
        if (
            !method_exists($result, 'getPurchaseUnits') ||
            !is_array($result->getPurchaseUnits()) ||
            empty($result->getPurchaseUnits())
        ) {
            return null;
        }

        $purchaseUnit = $result->getPurchaseUnits()[0];
        $payments = $purchaseUnit->getPayments();

        if (
            !method_exists($payments, 'getCaptures') ||
            !is_array($payments->getCaptures()) ||
            empty($payments->getCaptures())
        ) {
            return null;
        }

        return $payments->getCaptures()[0]->getId();
    }

    /**
     * Prepare raw details for storage
     *
     * @param string $details JSON details object
     * @return array<string, mixed>
     */
    public function prepareRawDetails(string $details): array
    {
        $decoded = json_decode($details, true);
        if (isset($decoded['links'])) {
            unset($decoded['links']);
        }
        return array_map(
            fn($v) => is_array($v) ? json_encode($v) : $v,
            $decoded ?? [],
        );
    }

    /**
     * Add order status comment
     *
     * @param string $action Action performed (captured, authorized, etc.)
     * @param string|null $transactionId Transaction ID
     */
    public function addOrderComment(
        Mage_Sales_Model_Quote|Mage_Sales_Model_Order $quote,
        string $action,
        ?string $transactionId
    ): void {
        if ($quote instanceof Mage_Sales_Model_Order && $transactionId) {
            $message = match ($action) {
                'captured' => 'PayPal payment captured successfully. Capture ID: %s',
                'authorized' => 'PayPal payment authorized successfully. Authorization ID: %s',
                default => 'PayPal payment %s successfully. Transaction ID: %s'
            };

            $quote->addStatusHistoryComment(
                Mage::helper('paypal')->__($message, $transactionId),
                false,
            );
        }
    }

    /**
     * Handle multishipping notification
     */
    public function handleMultishippingNotification(?Mage_Sales_Model_Quote $quote): void
    {
        if (
            $quote?->getIsMultiShipping() &&
            Mage::getSingleton('paypal/config')->getPaymentAction() === strtolower(CheckoutPaymentIntent::AUTHORIZE)
        ) {
            Mage::getSingleton('core/session')->addNotice(
                Mage::helper('paypal')->__(
                    'PayPal will process multishipping orders as immediate capture regardless of your authorization setting.',
                ),
            );
        }
    }

    /**
     * Get payment intent based on payment action configuration
     */
    public function getPaymentIntent(): string
    {
        // Always use capture for multishipping orders
        if ($this->getQuote()->getIsMultiShipping()) {
            return CheckoutPaymentIntent::CAPTURE;
        }

        $paymentAction = Mage::getSingleton('paypal/config')->getPaymentAction();

        return $paymentAction === strtolower(CheckoutPaymentIntent::AUTHORIZE)
            ? CheckoutPaymentIntent::AUTHORIZE
            : CheckoutPaymentIntent::CAPTURE;
    }

    /**
     * Get PayPal API instance
     */
    public function getApi(): Mage_Paypal_Model_Api
    {
        return Mage::getSingleton('paypal/api');
    }

    /**
     * Get current quote
     */
    public function getQuote(): Mage_Sales_Model_Quote
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }


    /**
     * Retrieve PayPal Transaction PayPal-Request-Id if available
     */
    public function getPaypalRequestId(Mage_Sales_Model_Quote $quote): ?string
    {
        $payment = $quote->getPayment();
        if ($payment && $payment->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_REQUEST_ID)) {
            return $payment->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_REQUEST_ID);
        }
        return null;
    }
}
