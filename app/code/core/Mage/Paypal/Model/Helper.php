<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use Monolog\Level;
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

    private const ERROR_UNVERIFIED_PAYMENT = 'Unable to verify the PayPal payment. Please restart PayPal checkout.';

    private const ERROR_PAYMENT_QUOTE_MISMATCH = 'The PayPal payment does not match this quote. Please restart PayPal checkout.';

    private const ERROR_PAYMENT_AMOUNT_MISMATCH = 'The PayPal payment amount no longer matches your quote total. Please restart PayPal checkout.';

    /**
     * Keys whose values hold customer PII and must be redacted before any
     * request/response payload is persisted to the debug log.
     *
     * @var string[]
     */
    private const REDACTED_KEYS = ['payer', 'shipping', 'payment_source', 'email_address', 'phone', 'phone_number'];

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
     * @param string                                        $action   Action being performed (e.g., 'Create Order').
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote    quote or order object
     * @param array                                         $request  request object or data sent to the API
     * @param null|ApiResponse                              $response API response, if available
     */
    public function logDebug(
        string $action,
        Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote,
        array $request,
        ?ApiResponse $response = null
    ): void {
        if (Mage::getStoreConfigFlag('payment/paypal/debug')) {
            $debug = Mage::getModel('paypal/debug');
            if ($quote instanceof Mage_Sales_Model_Quote) {
                $debug->setQuoteId($quote->getId());
            } elseif ($quote instanceof Mage_Sales_Model_Order) {
                $debug->setIncrementId($quote->getIncrementId());
            }

            $debug->setAction($action)
                ->setRequestBody($this->_encodeRedacted($request));
            if ($response instanceof ApiResponse) {
                $result = $response->getResult();
                if ($response->isError()) {
                    $debug->setTransactionId(is_array($result) ? ($result['debug_id'] ?? null) : null)
                        ->setResponseBody($this->_encodeRedacted($result));
                } else {
                    $debug->setTransactionId($result->getId() ?? null)
                        ->setResponseBody($this->_encodeRedacted($result));
                }
            }

            try {
                $debug->save();
            } catch (Exception $exception) {
                // The debug table is unavailable - fall back to the file log
                // so the request/response is not lost entirely.
                Mage::log($debug->getData(), Level::Error, 'paypal.log');
                Mage::logException($exception);
            }
        }
    }

    /**
     * Logs an error message and exception details if debugging is enabled.
     *
     * @param string    $message   the error message
     * @param Exception $exception the exception object
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

            Mage::log($errorData, Level::Error, 'paypal.log', true);
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
     * Re-request the shipping rates and recollect the totals, so the quote prices the selected method.
     *
     * Always through collectTotals(), never collectShippingRates() alone: requestShippingRates() writes the raw,
     * base-currency rate price straight into shipping_amount (a known core quirk; Total_Shipping::collect() is
     * what converts it back). On its own it would leave the unconverted amount on the address, which then rides
     * into the order and its invoice while grand_total keeps the converted value.
     */
    public function recollectShippingTotals(Mage_Sales_Model_Quote $quote): void
    {
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();
    }

    /**
     * Ensure physical quotes have a valid selected shipping method.
     *
     * Mage_Sales_Model_Service_Quote::_validate() enforces this at submit time, which for PayPal
     * is *after* the buyer has been charged — a failure there leaves money captured and no order.
     * Callers must run this before creating or capturing a PayPal order.
     *
     * Pass $refreshRates = false once PayPal already holds an approved amount: re-quoting a live
     * carrier there can move the total away from what the buyer approved, and the capture paths
     * that lack a re-patch step would then charge the stale figure.
     *
     * @throws Mage_Core_Exception
     */
    public function validateShippingMethodForQuote(Mage_Sales_Model_Quote $quote, bool $refreshRates = true): void
    {
        if ($quote->isVirtual()) {
            return;
        }

        $address = $quote->getShippingAddress();
        if ($refreshRates) {
            $this->recollectShippingTotals($quote);
        }

        $method = (string) $address->getShippingMethod();
        if ($method === '') {
            Mage::throwException($this->_helper->__('Please specify a shipping method.'));
        }

        $rate = $address->getShippingRateByCode($method);
        if (!$rate instanceof Mage_Sales_Model_Quote_Address_Rate) {
            Mage::throwException($this->_helper->__('Please specify a valid shipping method.'));
        }

        $rateErrorMessage = $rate->getErrorMessage();
        if (!in_array($rateErrorMessage, [null, false, ''], true)) {
            Mage::throwException($this->_helper->__('Please specify a valid shipping method.'));
        }
    }

    /**
     * Validate that the PayPal payment stored on the quote is the payment
     * Magento is about to convert into an order.
     *
     * @throws Mage_Paypal_Model_Exception
     */
    public function validateProcessedPaymentForQuote(
        Mage_Sales_Model_Quote $quote,
        bool $isAuthorize,
        string $paypalOrderId = ''
    ): void {
        $payment = $quote->getPayment();
        if ($payment->getMethod() !== 'paypal') {
            throw new Mage_Paypal_Model_Exception($this->_helper->__(self::ERROR_UNVERIFIED_PAYMENT));
        }

        $rawDetails = $payment->getAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS);
        if (!is_array($rawDetails) || $rawDetails === []) {
            throw new Mage_Paypal_Model_Exception($this->_helper->__(self::ERROR_UNVERIFIED_PAYMENT));
        }

        $currency = $this->_getQuoteCurrencyCode($quote);
        if ($currency === '') {
            throw new Mage_Paypal_Model_Exception($this->_helper->__(self::ERROR_UNVERIFIED_PAYMENT));
        }

        $this->_assertProcessedPaymentBelongsToQuote($rawDetails, $quote, $paypalOrderId);

        if ($isAuthorize) {
            $transactionId = (string) (
                $rawDetails['authorization_id']
                ?? $payment->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID)
            );
            $paypalAmount = $this->_extractRawDetailAmount(
                $rawDetails['authorization_amount'] ?? ($rawDetails['amount'] ?? null),
                $currency,
            );
        } else {
            $transactionId = (string) ($rawDetails['capture_id'] ?? $payment->getPaypalCorrelationId());
            $paypalAmount = $this->_extractRawDetailAmount(
                $rawDetails['capture_amount'] ?? ($rawDetails['amount'] ?? null),
                $currency,
            );
        }

        if ($transactionId === '' || $paypalAmount === null) {
            throw new Mage_Paypal_Model_Exception($this->_helper->__(self::ERROR_UNVERIFIED_PAYMENT));
        }

        $expectedAmount = $this->_helper->formatPrice((float) $quote->getGrandTotal(), $currency);
        $processedAmount = $this->_helper->formatPrice($paypalAmount, $currency);

        if ($processedAmount !== $expectedAmount) {
            throw new Mage_Paypal_Model_Exception($this->_helper->__(self::ERROR_PAYMENT_AMOUNT_MISMATCH));
        }
    }

    /**
     * @param  array<string, mixed>        $rawDetails
     * @throws Mage_Paypal_Model_Exception
     */
    private function _assertProcessedPaymentBelongsToQuote(
        array $rawDetails,
        Mage_Sales_Model_Quote $quote,
        string $paypalOrderId
    ): void {
        $reservedOrderId = $quote->getReservedOrderId();
        $expectedInvoiceId = (string) (
            ($reservedOrderId !== null && $reservedOrderId !== '')
                ? $reservedOrderId
                : $quote->getId()
        );
        $processedInvoiceId = (string) ($rawDetails['invoice_id'] ?? '');

        if (
            $expectedInvoiceId === ''
            || $processedInvoiceId === ''
            || !hash_equals($expectedInvoiceId, $processedInvoiceId)
        ) {
            throw new Mage_Paypal_Model_Exception($this->_helper->__(self::ERROR_PAYMENT_QUOTE_MISMATCH));
        }

        if ($paypalOrderId === '') {
            return;
        }

        $processedOrderId = (string) ($rawDetails['id'] ?? '');
        if ($processedOrderId === '' || !hash_equals($processedOrderId, $paypalOrderId)) {
            throw new Mage_Paypal_Model_Exception($this->_helper->__(self::ERROR_PAYMENT_QUOTE_MISMATCH));
        }
    }

    /**
     * Validate a PayPal order read back from the API against the quote it is about to be charged for:
     * same order, same invoice id, same currency and the same amount as the quote total right now.
     *
     * @param  array<string, mixed>        $details decoded PayPal order
     * @throws Mage_Paypal_Model_Exception
     */
    public function assertPaypalOrderMatchesQuote(array $details, Mage_Sales_Model_Quote $quote, string $paypalOrderId): void
    {
        $unit = is_array($details['purchase_units'][0] ?? null) ? $details['purchase_units'][0] : [];
        $rawDetails = [
            'id'         => (string) ($details['id'] ?? ''),
            'invoice_id' => (string) ($unit['invoice_id'] ?? ''),
        ];
        $this->_assertProcessedPaymentBelongsToQuote($rawDetails, $quote, $paypalOrderId);

        $currency = $this->_getQuoteCurrencyCode($quote);
        $paypalCurrency = (string) ($unit['amount']['currency_code'] ?? '');
        if ($currency === '' || strcasecmp($currency, $paypalCurrency) !== 0) {
            throw new Mage_Paypal_Model_Exception($this->_helper->__(self::ERROR_PAYMENT_QUOTE_MISMATCH));
        }

        $paypalAmount = $unit['amount']['value'] ?? null;
        if (!is_numeric($paypalAmount)
            || $this->_helper->formatPrice((float) $paypalAmount, $currency)
                !== $this->_helper->formatPrice((float) $quote->getGrandTotal(), $currency)
        ) {
            throw new Mage_Paypal_Model_Exception($this->_helper->__(self::ERROR_PAYMENT_AMOUNT_MISMATCH));
        }
    }

    /**
     * The PayPal order the quote's current payment attempt was created for.
     */
    public function getQuotePaypalOrderId(Mage_Sales_Model_Quote $quote): string
    {
        $payment = $quote->getPayment();
        $paypalOrderId = (string) $payment->getData(Mage_Paypal_Model_Payment::PAYPAL_ORDER_ID);
        if ($paypalOrderId !== '') {
            return $paypalOrderId;
        }

        // Attempts started before the paypal_order_id column existed only carry the id in the raw details.
        $rawDetails = $payment->getAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS);

        return is_array($rawDetails) ? (string) ($rawDetails['id'] ?? '') : '';
    }

    /**
     * Whether an active quote holds a captured or authorized PayPal payment that never became an order.
     *
     * Such a quote must be turned into its order; resetting it or starting another PayPal order for it
     * charges the buyer a second time.
     */
    public function hasUnplacedPaypalPayment(Mage_Sales_Model_Quote $quote): bool
    {
        if ((int) $quote->getId() === 0 || (int) $quote->getIsActive() !== 1) {
            return false;
        }

        return $this->hasRecordedPaypalCharge($quote->getPayment());
    }

    /**
     * Whether a PayPal capture or authorization has been recorded on the payment.
     */
    public function hasRecordedPaypalCharge(Mage_Payment_Model_Info $payment): bool
    {
        $rawDetails = $payment->getAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS);
        if (!is_array($rawDetails)) {
            return false;
        }

        return ($rawDetails['capture_id'] ?? '') !== '' || ($rawDetails['authorization_id'] ?? '') !== '';
    }

    /**
     * The PayPal order an active quote may already be paid through: one recorded as charged, or one whose charge
     * was attempted without a recorded outcome. Empty when neither applies.
     */
    public function getPendingChargePaypalOrderId(Mage_Sales_Model_Quote $quote): string
    {
        if ($this->hasUnplacedPaypalPayment($quote)) {
            return $this->getQuotePaypalOrderId($quote);
        }

        if ((int) $quote->getId() === 0 || (int) $quote->getIsActive() !== 1) {
            return '';
        }

        return (string) $quote->getPayment()->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_CHECKOUT_ATTEMPT_ORDER_ID);
    }

    /**
     * Whether the quote may already be paid through PayPal. Resetting such a quote or starting another PayPal
     * order for it risks charging the buyer twice.
     */
    public function hasPendingPaypalCharge(Mage_Sales_Model_Quote $quote): bool
    {
        return $this->getPendingChargePaypalOrderId($quote) !== '';
    }

    /**
     * Serialise work on one PayPal order across requests, webhooks and cron.
     *
     * A MySQL named lock is server-wide and released automatically if the PHP process dies.
     */
    public function acquirePaypalOrderLock(string $paypalOrderId, int $timeoutSeconds): bool
    {
        return (string) $this->_getWriteConnection()->fetchOne(
            'SELECT GET_LOCK(?, ?)',
            [$this->_getPaypalOrderLockName($paypalOrderId), $timeoutSeconds],
        ) === '1';
    }

    public function releasePaypalOrderLock(string $paypalOrderId): void
    {
        $this->_getWriteConnection()->fetchOne('SELECT RELEASE_LOCK(?)', [$this->_getPaypalOrderLockName($paypalOrderId)]);
    }

    private function _getPaypalOrderLockName(string $paypalOrderId): string
    {
        return 'paypal_order_' . $paypalOrderId;
    }

    /**
     * @throws Mage_Paypal_Model_Exception
     */
    private function _getWriteConnection(): Varien_Db_Adapter_Interface
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        if (!$connection instanceof Varien_Db_Adapter_Interface) {
            throw new Mage_Paypal_Model_Exception('The database write connection is unavailable.');
        }

        return $connection;
    }

    /**
     * Resolve the quote currency used for the PayPal payment.
     */
    private function _getQuoteCurrencyCode(Mage_Sales_Model_Quote $quote): string
    {
        $orderCurrency = $quote->getOrderCurrencyCode();
        if ($orderCurrency !== null && $orderCurrency !== '') {
            return (string) $orderCurrency;
        }

        return (string) $quote->getQuoteCurrencyCode();
    }

    /**
     * Extract a numeric amount from a whitelisted raw-details value.
     */
    private function _extractRawDetailAmount(mixed $amount, string $currency): ?float
    {
        if (is_int($amount) || is_float($amount)) {
            return (float) $amount;
        }

        if (!is_string($amount)) {
            return null;
        }

        $amount = trim($amount);
        if ($amount === '') {
            return null;
        }

        if (is_numeric($amount)) {
            return (float) $amount;
        }

        $parts = preg_split('/\s+/', $amount, 2);
        if (!is_array($parts) || count($parts) !== 2) {
            return null;
        }

        [$amountCurrency, $amountValue] = $parts;
        if (strcasecmp($amountCurrency, $currency) !== 0 || !is_numeric($amountValue)) {
            return null;
        }

        return (float) $amountValue;
    }

    /**
     * Handle API error response
     *
     * @param  ApiResponse                 $response       API response
     * @param  string                      $defaultMessage Default error message
     * @throws Mage_Paypal_Model_Exception
     */
    public function handleApiError(ApiResponse $response, string $defaultMessage): never
    {
        $errorMsg = $this->extractErrorMessage($response, $defaultMessage);
        // Carry the raw error payload: PAYER_ACTION_REQUIRED handling needs the
        // payer-action link PayPal ships inside the failed response's links.
        $result = $response->getResult();
        if (!is_array($result)) {
            $decoded = json_decode((string) $response->getBody(), true);
            $result = is_array($decoded) ? $decoded : [];
        }

        throw new Mage_Paypal_Model_Exception($errorMsg, $result);
    }

    /**
     * Extract comprehensive error message from PayPal API response
     *
     * @param ApiResponse $response       API response
     * @param string      $defaultMessage Default error message
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
     * @param array  $result         API result array
     * @param string $defaultMessage Default error message
     */
    private function _extractFromArrayResponse(array $result, string $defaultMessage): string
    {
        $mainMessage = $this->_normalizeApiMessage($result['message'] ?? null);
        $detailedMessage = $this->_extractDetailedError($result);
        if (!empty($detailedMessage)) {
            return empty($mainMessage) ? $detailedMessage : $mainMessage . ' ' . $detailedMessage;
        }

        return empty($mainMessage) ? $defaultMessage : $this->_helper->__($mainMessage);
    }

    /**
     * Extract error message from object response structure
     *
     * @param object $result         API result object
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
                    $issue = $this->_normalizeApiMessage($detail['issue'] ?? null);
                } elseif (is_object($detail)) {
                    $issue = $this->_normalizeApiMessage($detail->issue ?? null);
                }

                if ($issue !== '') {
                    $errorMessages[] = $this->_getReadableErrorMessage($issue);
                }
            }
        }

        if ($errorMessages === [] && isset($result['message'])) {
            $normalized = $this->_normalizeApiMessage($result['message']);
            if ($normalized !== '') {
                $errorMessages[] = $normalized;
            }
        }

        return implode('; ', $errorMessages);
    }

    /**
     * Coerce a PayPal error payload field to a usable string.
     *
     * Why: PayPal v2 error envelopes normally hold scalars in `message` /
     * `issue`, but the apimatic SDK has been seen handing back nested arrays
     * or objects on edge-case errors. Concatenating those directly emits
     * "Array to string conversion". JSON-encoding non-scalars preserves the
     * payload for the merchant log instead of swallowing it.
     */
    private function _normalizeApiMessage(mixed $value): string
    {
        if (is_scalar($value)) {
            return (string) $value;
        }

        if ($value === null) {
            return '';
        }

        $encoded = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return $encoded === false ? '' : $encoded;
    }

    /**
     * Convert PayPal error codes to user-friendly messages
     *
     * @param string $errorCode PayPal error code
     */
    private function _getReadableErrorMessage(string $errorCode): string
    {
        $errorMessages = [
            'INSTRUMENT_DECLINED' => $this->_helper->__("The instrument presented was either declined by the processor or bank, or it can't be used for this payment."),
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
            !is_object($result)
            || !method_exists($result, 'getPurchaseUnits')
            || !is_array($result->getPurchaseUnits())
            || $result->getPurchaseUnits() === []
        ) {
            Mage::log('PayPal: unable to extract capture ID - no purchase units in response.', Level::Warning, 'paypal.log');
            return null;
        }

        $purchaseUnit = $result->getPurchaseUnits()[0];
        $payments = $purchaseUnit->getPayments();

        if (
            !is_object($payments)
            || !method_exists($payments, 'getCaptures')
            || !is_array($payments->getCaptures())
            || $payments->getCaptures() === []
        ) {
            Mage::log('PayPal: unable to extract capture ID - no captures in response.', Level::Warning, 'paypal.log');
            return null;
        }

        return $payments->getCaptures()[0]->getId();
    }

    /**
     * Extract the captured amount from a PayPal order API result.
     *
     * @param mixed $result API result (an Order with nested capture)
     */
    public function extractCaptureAmount(mixed $result): ?float
    {
        if (
            !is_object($result)
            || !method_exists($result, 'getPurchaseUnits')
            || !is_array($result->getPurchaseUnits())
            || $result->getPurchaseUnits() === []
        ) {
            return null;
        }

        $payments = $result->getPurchaseUnits()[0]->getPayments();
        if (
            !is_object($payments)
            || !method_exists($payments, 'getCaptures')
            || !is_array($payments->getCaptures())
            || $payments->getCaptures() === []
        ) {
            return null;
        }

        $amount = $payments->getCaptures()[0]->getAmount();

        return $amount ? (float) $amount->getValue() : null;
    }

    /**
     * Extract the payment-relevant fields from a PayPal API response for
     * storage on the transaction record.
     *
     * Only a whitelist of useful scalar fields is kept; verbose, unbounded or
     * redundant data (line items, links, payee, raw breakdown trees) is
     * dropped so the admin transaction grid stays readable regardless of
     * order size. The full payload remains available in the debug log.
     *
     * Handles every PayPal resource shape the module persists: Orders v2
     * order responses (capture or authorize intent) and the standalone
     * Payments v2 capture / refund / authorization resources.
     *
     * @param  string                $details JSON details object
     * @return array<string, string>
     */
    public function prepareRawDetails(string $details): array
    {
        $decoded = json_decode($details, true);
        if (!is_array($decoded)) {
            return [];
        }

        $unit     = is_array($decoded['purchase_units'][0] ?? null) ? $decoded['purchase_units'][0] : [];
        $payments = is_array($unit['payments'] ?? null) ? $unit['payments'] : [];

        $capture = is_array($payments['captures'][0] ?? null) ? $payments['captures'][0] : [];
        if ($capture === [] && isset($decoded['seller_receivable_breakdown'])) {
            $capture = $decoded; // standalone Payments v2 capture resource
        }

        $refund = is_array($payments['refunds'][0] ?? null) ? $payments['refunds'][0] : [];
        if ($refund === [] && isset($decoded['seller_payable_breakdown'])) {
            $refund = $decoded; // standalone Payments v2 refund resource
        }

        $authorization = is_array($payments['authorizations'][0] ?? null) ? $payments['authorizations'][0] : [];
        if ($authorization === [] && isset($decoded['expiration_time'])) {
            $authorization = $decoded; // standalone authorization resource
        }

        // The breakdown key differs: captures use "receivable", refunds "payable".
        $breakdown = $capture['seller_receivable_breakdown'] ?? ($refund['seller_payable_breakdown'] ?? []);
        if (!is_array($breakdown)) {
            $breakdown = [];
        }

        $payer = $this->_resolvePayer($decoded);

        $extracted = [
            'id'                   => $decoded['id'] ?? null,
            'intent'               => $decoded['intent'] ?? null,
            'status'               => $decoded['status'] ?? ($capture['status'] ?? null),
            'payer_email'          => $payer['email_address'] ?? null,
            'payer_name'           => $this->_extractPayerName($payer),
            'amount'               => $this->_formatPaypalAmount(
                $decoded['amount'] ?? $unit['amount'] ?? $capture['amount'] ?? null,
            ),
            'invoice_id'           => $unit['invoice_id'] ?? ($decoded['invoice_id'] ?? null),
            'capture_id'           => $capture['id'] ?? null,
            'capture_amount'       => $this->_formatPaypalAmount($capture['amount'] ?? null),
            'paypal_fee'           => $this->_formatPaypalAmount($breakdown['paypal_fee'] ?? null),
            'net_amount'           => $this->_formatPaypalAmount($breakdown['net_amount'] ?? null),
            'refund_id'            => $refund['id'] ?? null,
            'refund_amount'        => $this->_formatPaypalAmount($refund['amount'] ?? null),
            'authorization_id'     => $authorization['id'] ?? null,
            'authorization_status' => $authorization['status'] ?? null,
            'authorization_amount' => $this->_formatPaypalAmount($authorization['amount'] ?? null),
            'expiration_time'      => $decoded['expiration_time'] ?? ($authorization['expiration_time'] ?? null),
            'create_time'          => $decoded['create_time'] ?? null,
            'update_time'          => $decoded['update_time'] ?? null,
        ];

        return array_map(
            strval(...),
            array_filter($extracted, static fn($value) => is_scalar($value) && $value !== ''),
        );
    }

    /**
     * Resolve the payer structure from a PayPal response.
     *
     * The legacy "payer" object is deprecated in newer Orders v2 responses in
     * favour of "payment_source.paypal"; fall back to it when needed.
     *
     * @param  array<string, mixed> $decoded
     * @return array<string, mixed>
     */
    private function _resolvePayer(array $decoded): array
    {
        if (is_array($decoded['payer'] ?? null)) {
            return $decoded['payer'];
        }

        if (is_array($decoded['payment_source']['paypal'] ?? null)) {
            return $decoded['payment_source']['paypal'];
        }

        return [];
    }

    /**
     * Format a PayPal amount object ("currency_code" + "value") as a string.
     */
    private function _formatPaypalAmount(mixed $amount): ?string
    {
        if (!is_array($amount) || !is_scalar($amount['value'] ?? null)) {
            return null;
        }

        $currency = $amount['currency_code'] ?? '';

        return trim((is_scalar($currency) ? (string) $currency : '') . ' ' . $amount['value']);
    }

    /**
     * Build the payer's full name from a PayPal "payer" structure.
     *
     * @param array<string, mixed> $payer
     */
    private function _extractPayerName(array $payer): ?string
    {
        $name = is_array($payer['name'] ?? null) ? $payer['name'] : [];
        $full = trim(
            ($name['given_name'] ?? '') . ' ' . ($name['surname'] ?? ''),
        );

        return $full === '' ? null : $full;
    }

    /**
     * JSON-encode a request/response payload with customer PII redacted.
     *
     * @param mixed $payload Array or SDK object to serialise for the debug log
     */
    private function _encodeRedacted(mixed $payload): string
    {
        $asArray = json_decode((string) json_encode($payload), true);

        return (string) json_encode(
            is_array($asArray) ? $this->_redactSensitiveData($asArray) : $asArray,
        );
    }

    /**
     * Recursively replace the values of PII-bearing keys with a redaction marker.
     *
     * @param  array<mixed> $data
     * @return array<mixed>
     */
    private function _redactSensitiveData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), self::REDACTED_KEYS, true)) {
                $data[$key] = '[REDACTED]';
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->_redactSensitiveData($value);
            }
        }

        return $data;
    }

    /**
     * Add order status comment
     *
     * @param string      $action        Action performed (captured, authorized, etc.)
     * @param null|string $transactionId Transaction ID
     */
    public function addOrderComment(
        Mage_Sales_Model_Order|Mage_Sales_Model_Quote $quote,
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
            $quote?->getIsMultiShipping()
            && Mage::getSingleton('paypal/config')->getPaymentAction() === strtolower(CheckoutPaymentIntent::AUTHORIZE)
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
}
