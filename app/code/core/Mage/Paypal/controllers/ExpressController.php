<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\PurchaseUnitRequest;

/**
 * PayPal Express shortcut controller.
 */
class Mage_Paypal_ExpressController extends Mage_Core_Controller_Front_Action
{
    private const CONTENT_TYPE_JSON = 'application/json';

    private const SHORTCUT_CONTEXT_PRODUCT = 'product';

    private const SHORTCUT_CONTEXT_CART = 'cart';

    /**
     * @var false|Mage_Sales_Model_Quote
     */
    protected $_quote = false;

    /**
     * @var null|Mage_Paypal_Model_Paypal
     */
    protected $_paypal = null;

    /**
     * Create a PayPal order from a product or cart shortcut.
     */
    public function startAction(): void
    {
        try {
            if (!$this->_validateFormKey()) {
                Mage::throwException(Mage::helper('paypal')->__('Invalid form key.'));
            }

            $quote = $this->_getQuote();
            if (!$this->_enforceGuestCheckoutAllowed($quote)) {
                return;
            }

            $this->_clearShortcutState($quote);

            if ($this->_getShortcutContext() === self::SHORTCUT_CONTEXT_PRODUCT) {
                $this->_addProductToQuote();
                $quote = $this->_getQuote();
            }

            $this->_prepareQuoteForStart($quote);
            $this->_validateQuoteUsable($quote);
            $this->_validateMinimumAmount($quote);

            $fundingSource = (string) $this->getRequest()->getParam('funding_source');
            if ($fundingSource === '') {
                $fundingSource = 'paypal';
            }

            $fingerprint = $this->_buildQuoteFingerprint($quote);
            $result = $this->_getPaypal()->create($quote, $fundingSource);
            $resultId = $result['id'] ?? null;
            if (($result['success'] ?? false) !== true || !is_scalar($resultId) || (string) $resultId === '') {
                Mage::throwException((string) ($result['error'] ?? Mage::helper('paypal')->__('Unable to initialize Express Checkout.')));
            }

            $orderId = (string) $resultId;
            $this->_storeShortcutState($quote, $orderId, $fingerprint);
            $this->_jsonResponse([
                'success' => true,
                'id' => $orderId,
            ]);
        } catch (Exception $exception) {
            Mage::logException($exception);
            $this->_jsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Render the store-side PayPal Express review page.
     */
    public function reviewAction(): void
    {
        try {
            if (!$this->_validateFormKey()) {
                Mage::throwException(Mage::helper('paypal')->__('Invalid form key.'));
            }

            $quote = $this->_getQuote();
            $orderId = trim((string) $this->getRequest()->getParam('token'));
            $this->_validateShortcutAttempt($quote, $orderId);
            $this->_assertCurrencyLock($quote);

            $api = Mage::getSingleton('paypal/helper')->getApi()->setStore($quote->getStore());
            $response = $api->getOrderDetails($orderId);
            if ($response->isError()) {
                Mage::getSingleton('paypal/helper')->handleApiError($response, 'Unable to read PayPal order details.');
            }

            $details = $this->_assertPaypalOrderBelongsToQuote($quote, $response->getBody());
            $this->_assertPatchablePaypalStatus($details);

            Mage::getModel('paypal/express_addressImporter')->importFromOrderDetails($quote, $response->getBody());
            $this->_prepareShippingRates($quote);
            $quote->collectTotals()->save();

            $patchResponse = $api->patchOrder($orderId, $this->_buildPatch($quote));
            if ($patchResponse !== null && $patchResponse->isError()) {
                Mage::getSingleton('paypal/helper')->handleApiError($patchResponse, 'Unable to update PayPal order.');
            }

            $this->loadLayout()
                ->_initLayoutMessages('checkout/session')
                ->_initLayoutMessages('core/session');
            $headBlock = $this->getLayout()->getBlock('head');
            if ($headBlock instanceof Mage_Page_Block_Html_Head) {
                $headBlock->setTitle($this->__('Review PayPal Order'));
            }

            $this->renderLayout();
        } catch (Exception $exception) {
            Mage::logException($exception);
            $this->_getCheckoutSession()->addError($exception->getMessage());
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Save the selected shipping method and return refreshed totals.
     */
    public function saveShippingMethodAction(): void
    {
        try {
            if (!$this->_validateFormKey()) {
                Mage::throwException(Mage::helper('paypal')->__('Invalid form key.'));
            }

            $quote = $this->_getQuote();
            $orderId = trim((string) $this->getRequest()->getParam('token'));
            $this->_validateShortcutAttempt($quote, $orderId);
            $this->_assertCurrencyLock($quote);

            if ($quote->isVirtual()) {
                Mage::throwException(Mage::helper('paypal')->__('This order does not require a shipping method.'));
            }

            $shippingMethod = trim((string) $this->getRequest()->getPost('shipping_method'));
            if ($shippingMethod === '') {
                Mage::throwException(Mage::helper('paypal')->__('Please specify a shipping method.'));
            }

            $address = $quote->getShippingAddress();
            $address->setCollectShippingRates(true)->collectShippingRates();
            $rate = $address->getShippingRateByCode($shippingMethod);
            if (!$rate instanceof Mage_Sales_Model_Quote_Address_Rate) {
                Mage::throwException(Mage::helper('paypal')->__('Please specify a valid shipping method.'));
            }

            $rateErrorMessage = $rate->getErrorMessage();
            if (!in_array($rateErrorMessage, [null, false, ''], true)) {
                Mage::throwException(Mage::helper('paypal')->__('Please specify a valid shipping method.'));
            }

            $address->setShippingMethod($shippingMethod);
            $quote->collectTotals()->save();

            $this->_jsonResponse([
                'success' => true,
                'totals_html' => $this->_renderTotalsHtml($quote),
                'grand_total' => $this->_formatQuoteGrandTotal($quote),
            ]);
        } catch (Exception $exception) {
            Mage::logException($exception);
            $this->_jsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Patch the PayPal order, capture/authorize, and submit the Magento order.
     */
    public function placeOrderAction(): void
    {
        $orderId = trim((string) $this->getRequest()->getParam('token'));
        try {
            if (!$this->_validateFormKey()) {
                Mage::throwException(Mage::helper('paypal')->__('Invalid form key.'));
            }

            $quote = $this->_getQuote();
            $this->_validateShortcutAttempt($quote, $orderId);
            $this->_assertCurrencyLock($quote);
            $this->_validateCheckoutAgreements();

            $api = Mage::getSingleton('paypal/helper')->getApi()->setStore($quote->getStore());
            $response = $api->getOrderDetails($orderId);
            if ($response->isError()) {
                Mage::getSingleton('paypal/helper')->handleApiError($response, 'Unable to read PayPal order details.');
            }

            $details = $this->_assertPaypalOrderBelongsToQuote($quote, $response->getBody());
            $this->_assertPatchablePaypalStatus($details);

            /** @var Mage_Paypal_Model_Checkout_Finalizer $finalizer */
            $finalizer = Mage::getModel('paypal/checkout_finalizer');
            $isNewCustomer = $finalizer->prepareQuoteForCheckout($quote, false);
            $finalizer->ignoreAddressValidation($quote);

            $quote->collectTotals();
            $this->_validateQuoteUsable($quote);
            $this->_validateMinimumAmount($quote);
            $this->_validateShippingMethod($quote);

            if (!$this->_isPostedGrandTotalCurrent($quote)) {
                $this->_getCheckoutSession()->addNotice(
                    Mage::helper('paypal')->__('The order total changed. Please review your order before placing it.'),
                );
                $quote->save();
                $this->_redirectReview($orderId);
                return;
            }

            $this->_assertQuoteFingerprint($quote);

            $patchResponse = $api->patchOrder($orderId, $this->_buildPatch($quote));
            if ($patchResponse !== null && $patchResponse->isError()) {
                Mage::getSingleton('paypal/helper')->handleApiError($patchResponse, 'Unable to update PayPal order.');
            }

            $paymentAction = Mage::getSingleton('paypal/config')->getPaymentAction();
            $isAuthorize = ($paymentAction === strtolower(CheckoutPaymentIntent::AUTHORIZE));
            if ($isAuthorize) {
                $this->_getPaypal()->authorizePayment($orderId, $quote);
            } else {
                $this->_getPaypal()->captureOrder($orderId, $quote);
            }

            Mage::getSingleton('paypal/helper')->validateProcessedPaymentForQuote($quote, $isAuthorize, $orderId);

            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();
            $order = $finalizer->finalizeSubmittedOrder($quote, $service, $isAuthorize, $isNewCustomer);
            if ($order === null) {
                return;
            }

            $this->_redirect('checkout/onepage/success');
        } catch (Exception $exception) {
            Mage::logException($exception);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->_getQuote(), $exception->getMessage());
            $this->_getCheckoutSession()->addError($exception->getMessage());
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Cancel the current PayPal shortcut attempt.
     */
    public function cancelAction(): void
    {
        try {
            if (!$this->_validateFormKey()) {
                Mage::throwException(Mage::helper('paypal')->__('Invalid form key.'));
            }

            $quote = $this->_getQuote();
            $this->_clearShortcutState($quote);
            $quote->setReservedOrderId(null)->save();
            $this->_jsonResponse(['success' => true]);
        } catch (Exception $exception) {
            Mage::logException($exception);
            $this->_jsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Add the current product form request to the quote.
     */
    private function _addProductToQuote(): void
    {
        $params = $this->getRequest()->getParams();
        if (isset($params['qty'])) {
            $filter = new Zend_Filter_LocalizedToNormalized(
                ['locale' => Mage::app()->getLocale()->getLocaleCode()],
            );
            $params['qty'] = $filter->filter($params['qty']);
        }

        $productId = (int) ($params['product'] ?? 0);
        if ($productId <= 0) {
            Mage::throwException(Mage::helper('checkout')->__('The product could not be found.'));
        }

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
        if ((int) $product->getId() === 0) {
            Mage::throwException(Mage::helper('checkout')->__('The product could not be found.'));
        }

        /** @var Mage_Checkout_Model_Cart $cart */
        $cart = Mage::getSingleton('checkout/cart');
        $cart->addProduct($product, $params);
        $cart->save();

        $this->_quote = $cart->getQuote();
        $this->_getCheckoutSession()->setCartWasUpdated(false);
    }

    /**
     * Enforce guest checkout settings before PayPal opens.
     */
    private function _enforceGuestCheckoutAllowed(Mage_Sales_Model_Quote $quote): bool
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return true;
        }

        if (!Mage::helper('checkout')->isAllowedGuestCheckout($quote, $quote->getStoreId())) {
            Mage::getSingleton('core/session')->addNotice(
                Mage::helper('paypal')->__('To proceed to Checkout, please log in using your email address.'),
            );
            $beforeAuthUrl = (string) $this->_getRefererUrl();
            if ($beforeAuthUrl === '' || !$this->_isUrlInternal($beforeAuthUrl)) {
                $beforeAuthUrl = Mage::getUrl('checkout/cart');
            }

            Mage::getSingleton('customer/session')->setBeforeAuthUrl($beforeAuthUrl);
            $this->_jsonResponse(
                [
                    'success' => false,
                    'redirect' => Mage::helper('core/url')->addRequestParam(
                        Mage::helper('customer')->getLoginUrl(),
                        ['context' => 'checkout'],
                    ),
                    'message' => Mage::helper('paypal')->__('To proceed to Checkout, please log in using your email address.'),
                ],
                403,
            );

            return false;
        }

        return true;
    }

    /**
     * Prepare quote state before creating the PayPal order.
     */
    private function _prepareQuoteForStart(Mage_Sales_Model_Quote $quote): void
    {
        if ((int) $quote->getIsMultiShipping() !== 0) {
            $quote->setIsMultiShipping(false);
            $quote->removeAllAddresses();
        }

        if (!$quote->isVirtual()) {
            $quote->getShippingAddress()
                ->setShippingMethod(null)
                ->setShippingDescription(null)
                ->setCollectShippingRates(false)
                ->removeAllShippingRates();
        }

        $this->_lockCurrency($quote);
        $quote->getPayment()->setMethod('paypal');
        $quote->collectTotals()->save();
    }

    /**
     * Lock the current store currency onto the quote for this PayPal attempt.
     */
    private function _lockCurrency(Mage_Sales_Model_Quote $quote): void
    {
        $store = Mage::app()->getStore();
        $currency = $store->getCurrentCurrency();
        $baseCurrency = $store->getBaseCurrency();
        $currencyCode = $currency->getCode();
        $rate = $baseCurrency->getRate($currency);

        $quote->setStoreId($store->getId())
            ->setBaseCurrencyCode($baseCurrency->getCode())
            ->setStoreCurrencyCode($baseCurrency->getCode())
            ->setQuoteCurrencyCode($currencyCode)
            ->setOrderCurrencyCode($currencyCode)
            ->setBaseToQuoteRate($rate)
            ->setStoreToQuoteRate($rate);
    }

    /**
     * Store per-attempt PayPal state on the quote payment.
     */
    private function _storeShortcutState(Mage_Sales_Model_Quote $quote, string $orderId, string $fingerprint): void
    {
        $payment = $quote->getPayment();
        $requestId = (string) $payment->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_REQUEST_ID);
        $reservedOrderId = (string) $quote->getReservedOrderId();
        $currency = $this->_getQuoteCurrencyCode($quote);

        $payment->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID, $orderId)
            ->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_REQUEST_ID, $requestId)
            ->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID, $reservedOrderId)
            ->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY, $currency)
            ->setAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_FINGERPRINT, $fingerprint)
            ->save();
        $this->_getCheckoutSession()
            ->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID, $orderId)
            ->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_REQUEST_ID, $requestId)
            ->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID, $reservedOrderId)
            ->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY, $currency)
            ->setData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_FINGERPRINT, $fingerprint);
        $quote->save();
    }

    /**
     * Clear stale PayPal state before or after a shortcut attempt.
     */
    private function _clearShortcutState(Mage_Sales_Model_Quote $quote): void
    {
        $payment = $quote->getPayment();
        foreach ($this->_getPaypalStateKeys() as $key) {
            $payment->unsAdditionalInformation($key);
        }

        $payment->setPaypalCorrelationId(null)
            ->setTransactionId(null)
            ->setIsTransactionClosed(false);
        if ((int) $quote->getId() > 0) {
            $payment->save();
        }

        foreach ($this->_getPaypalStateKeys() as $key) {
            $this->_getCheckoutSession()->unsetData($key);
        }
    }

    /**
     * @return string[]
     */
    private function _getPaypalStateKeys(): array
    {
        return [
            Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
            Mage_Paypal_Model_Payment::PAYPAL_REQUEST_ID,
            Mage_Paypal_Model_Payment::PAYPAL_PAYMENT_SOURCE,
            Mage_Paypal_Model_Payment::PAYPAL_PAYMENT_AUTHORIZATION_ID,
            Mage_Paypal_Model_Payment::PAYPAL_PAYMENT_AUTHORIZATION_REAUTHORIZED,
            Mage_Paypal_Model_Payment::PAYPAL_CAPTURED_AMOUNT,
            Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_STATUS,
            Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID,
            Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME,
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID,
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_REQUEST_ID,
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID,
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY,
            Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_FINGERPRINT,
        ];
    }

    /**
     * Validate that the current request belongs to the active PayPal shortcut attempt.
     */
    private function _validateShortcutAttempt(Mage_Sales_Model_Quote $quote, string $orderId): void
    {
        $quote->collectTotals();
        if ($orderId === '') {
            Mage::throwException(Mage::helper('paypal')->__('PayPal order ID is required.'));
        }

        $payment = $quote->getPayment();
        $storedOrderId = (string) $payment->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID);
        if ($storedOrderId === '') {
            $storedOrderId = (string) $this->_getCheckoutSession()
                ->getData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID);
        }

        if ($storedOrderId === '' || !hash_equals($storedOrderId, $orderId)) {
            Mage::throwException(Mage::helper('paypal')->__('PayPal checkout session has expired. Please restart PayPal checkout.'));
        }

        $this->_assertQuoteFingerprint($quote);
    }

    /**
     * Ensure the active quote still matches the quote that created the PayPal order.
     */
    private function _assertQuoteFingerprint(Mage_Sales_Model_Quote $quote): void
    {
        $expected = (string) $quote->getPayment()
            ->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_FINGERPRINT);
        if ($expected === '') {
            $expected = (string) $this->_getCheckoutSession()->getData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_FINGERPRINT);
        }

        $actual = $this->_buildQuoteFingerprint($quote);
        if ($expected === '' || !hash_equals($expected, $actual)) {
            Mage::throwException(
                Mage::helper('paypal')->__('Your cart changed after PayPal approval. Please restart PayPal checkout.'),
            );
        }
    }

    /**
     * Ensure the quote currency stayed locked to the PayPal attempt currency.
     */
    private function _assertCurrencyLock(Mage_Sales_Model_Quote $quote): void
    {
        $lockedCurrency = (string) $quote->getPayment()
            ->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY);
        if ($lockedCurrency === '') {
            $lockedCurrency = (string) $this->_getCheckoutSession()
                ->getData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY);
        }

        $quoteCurrency = $this->_getQuoteCurrencyCode($quote);
        $storeCurrency = (string) Mage::app()->getStore($quote->getStoreId())->getCurrentCurrencyCode();
        if (
            $lockedCurrency === ''
            || $quoteCurrency === ''
            || !hash_equals($lockedCurrency, $quoteCurrency)
            || !hash_equals($lockedCurrency, $storeCurrency)
        ) {
            Mage::throwException(
                Mage::helper('paypal')->__('The store currency changed after PayPal checkout started. Please restart PayPal checkout.'),
            );
        }
    }

    /**
     * Validate PayPal order details against the quote reserved order id.
     *
     * @return array<string, mixed>
     */
    private function _assertPaypalOrderBelongsToQuote(Mage_Sales_Model_Quote $quote, string $responseBody): array
    {
        $details = json_decode($responseBody, true);
        if (!is_array($details)) {
            Mage::throwException(Mage::helper('paypal')->__('PayPal order details were not valid JSON.'));
        }

        $storedOrderId = (string) $quote->getPayment()
            ->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID);
        if ($storedOrderId === '') {
            $storedOrderId = (string) $this->_getCheckoutSession()
                ->getData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_ORDER_ID);
        }

        $paypalOrderId = (string) ($details['id'] ?? '');
        if ($paypalOrderId === '' || !hash_equals($storedOrderId, $paypalOrderId)) {
            Mage::throwException(Mage::helper('paypal')->__('The PayPal order does not match this checkout session.'));
        }

        $reservedOrderId = (string) $quote->getPayment()
            ->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID);
        if ($reservedOrderId === '') {
            $reservedOrderId = (string) $this->_getCheckoutSession()
                ->getData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID);
        }

        $unit = is_array($details['purchase_units'][0] ?? null) ? $details['purchase_units'][0] : [];
        $invoiceId = (string) ($unit['invoice_id'] ?? '');
        $referenceId = (string) ($unit['reference_id'] ?? '');
        if (
            $reservedOrderId === ''
            || $invoiceId === ''
            || $referenceId === ''
            || !hash_equals($reservedOrderId, $invoiceId)
            || !hash_equals($reservedOrderId, $referenceId)
        ) {
            Mage::throwException(Mage::helper('paypal')->__('The PayPal order does not match this quote.'));
        }

        $lockedCurrency = (string) $quote->getPayment()
            ->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY);
        if ($lockedCurrency === '') {
            $lockedCurrency = (string) $this->_getCheckoutSession()
                ->getData(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_CURRENCY);
        }

        $paypalCurrency = strtoupper((string) ($unit['amount']['currency_code'] ?? ''));
        $quoteCurrency = strtoupper($this->_getQuoteCurrencyCode($quote));
        if (
            $paypalCurrency === ''
            || $lockedCurrency === ''
            || !hash_equals(strtoupper($lockedCurrency), $paypalCurrency)
            || !hash_equals($quoteCurrency, $paypalCurrency)
        ) {
            Mage::throwException(
                Mage::helper('paypal')->__('The PayPal order currency does not match the current store currency.'),
            );
        }

        return $details;
    }

    /**
     * Ensure the PayPal order can still be patched before capture/authorization.
     *
     * @param array<string, mixed> $details
     */
    private function _assertPatchablePaypalStatus(array $details): void
    {
        $status = strtoupper((string) ($details['status'] ?? ''));
        if (!in_array($status, ['CREATED', 'APPROVED'], true)) {
            Mage::throwException(Mage::helper('paypal')->__('This PayPal order can no longer be updated. Please restart PayPal checkout.'));
        }
    }

    /**
     * Collect shipping rates and select the sole valid rate automatically.
     */
    private function _prepareShippingRates(Mage_Sales_Model_Quote $quote): void
    {
        if ($quote->isVirtual()) {
            return;
        }

        $address = $quote->getShippingAddress();
        $address->setCollectShippingRates(true)->collectShippingRates();
        $validRates = $this->_getValidShippingRates($address);
        if ($validRates === []) {
            $this->_getCheckoutSession()->addError(
                Mage::helper('paypal')->__('Sorry, no shipping quotes are available for this order at this time.'),
            );
            return;
        }

        $currentMethod = (string) $address->getShippingMethod();
        if ($currentMethod !== '') {
            $currentRate = $address->getShippingRateByCode($currentMethod);
            if ($currentRate instanceof Mage_Sales_Model_Quote_Address_Rate) {
                return;
            }
        }

        if (count($validRates) === 1) {
            $address->setShippingMethod($validRates[0]->getCode());
        }
    }

    /**
     * @return Mage_Sales_Model_Quote_Address_Rate[]
     */
    private function _getValidShippingRates(Mage_Sales_Model_Quote_Address $address): array
    {
        $validRates = [];
        foreach ($address->getGroupedAllShippingRates() as $rates) {
            foreach ($rates as $rate) {
                $rateErrorMessage = $rate->getErrorMessage();
                if (in_array($rateErrorMessage, [null, false, ''], true)) {
                    $validRates[] = $rate;
                }
            }
        }

        return $validRates;
    }

    /**
     * Check basic quote, item, stock, and zero-total state before charging.
     */
    private function _validateQuoteUsable(Mage_Sales_Model_Quote $quote): void
    {
        if ($quote->hasItems() === false) {
            Mage::throwException(Mage::helper('paypal')->__('Your shopping cart is empty.'));
        }

        foreach ($quote->getAllItems() as $item) {
            $hasError = $item->getHasError();
            if (!in_array($hasError, [null, false, 0, '0'], true)) {
                /** @var string $message */
                $message = $item->getMessage();
                Mage::throwException($message);
            }
        }

        $quoteHasError = $quote->getHasError();
        if (!in_array($quoteHasError, [null, false, 0, '0'], true)) {
            Mage::throwException(Mage::helper('paypal')->__('Unable to initialize Express Checkout.'));
        }

        Mage::getSingleton('paypal/helper')->validateQuoteForPayment($quote);
    }

    /**
     * Enforce the store minimum-order amount.
     */
    private function _validateMinimumAmount(Mage_Sales_Model_Quote $quote): void
    {
        if ($quote->validateMinimumAmount() === true) {
            return;
        }

        $configuredMessage = Mage::getStoreConfig('sales/minimum_order/error_message');
        $message = ($configuredMessage !== null && $configuredMessage !== '')
            ? (string) $configuredMessage
            : Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');
        Mage::throwException((string) $message);
    }

    /**
     * Ensure physical quotes have a valid selected shipping method.
     */
    private function _validateShippingMethod(Mage_Sales_Model_Quote $quote): void
    {
        if ($quote->isVirtual()) {
            return;
        }

        $address = $quote->getShippingAddress();
        $address->setCollectShippingRates(true)->collectShippingRates();
        $method = (string) $address->getShippingMethod();
        if ($method === '') {
            Mage::throwException(Mage::helper('paypal')->__('Please specify a shipping method.'));
        }

        $rate = $address->getShippingRateByCode($method);
        if (!$rate instanceof Mage_Sales_Model_Quote_Address_Rate) {
            Mage::throwException(Mage::helper('paypal')->__('Please specify a valid shipping method.'));
        }

        $rateErrorMessage = $rate->getErrorMessage();
        if (!in_array($rateErrorMessage, [null, false, ''], true)) {
            Mage::throwException(Mage::helper('paypal')->__('Please specify a valid shipping method.'));
        }
    }

    /**
     * Build the PayPal PATCH operations for the final quote amount and items.
     *
     * @return array<int, array<string, mixed>>
     */
    private function _buildPatch(Mage_Sales_Model_Quote $quote): array
    {
        $referenceId = (string) $quote->getPayment()
            ->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_SHORTCUT_RESERVED_ORDER_ID);
        $purchaseUnit = Mage::getModel('paypal/order')->buildPurchaseUnit($quote, $referenceId);
        assert($purchaseUnit instanceof PurchaseUnitRequest);
        $pathPrefix = "/purchase_units/@reference_id=='" . str_replace("'", "\\'", $referenceId) . "'";

        $patch = [
            [
                'op' => 'replace',
                'path' => $pathPrefix . '/amount',
                'value' => $purchaseUnit->getAmount(),
            ],
        ];

        $items = $purchaseUnit->getItems();
        if (is_array($items) && $items !== []) {
            $patch[] = [
                'op' => 'replace',
                'path' => $pathPrefix . '/items',
                'value' => $items,
            ];
        }

        $shipping = $this->_buildShippingPatchValue($quote);
        if ($shipping !== null) {
            $patch[] = [
                'op' => 'replace',
                'path' => $pathPrefix . '/shipping',
                'value' => $shipping,
            ];
        }

        return $patch;
    }

    /**
     * Build the PayPal purchase-unit shipping payload from the quote shipping address.
     *
     * @return null|array{name: array{full_name: string}, address: array<string, string>}
     */
    private function _buildShippingPatchValue(Mage_Sales_Model_Quote $quote): ?array
    {
        if ($quote->isVirtual()) {
            return null;
        }

        $address = $quote->getShippingAddress();
        $countryId = strtoupper(trim((string) $address->getCountryId()));
        if ($countryId === '') {
            return null;
        }

        $fullName = trim(
            trim((string) $address->getFirstname()) . ' ' . trim((string) $address->getLastname()),
        );
        if ($fullName === '') {
            $fullName = 'PayPal Customer';
        }

        $regionCode = $address->getRegionCode();
        if ($regionCode === null || $regionCode === '') {
            $regionCode = $address->getRegion();
        }

        $paypalAddress = [
            'address_line_1' => trim((string) $address->getStreetLine(1)),
            'address_line_2' => trim((string) $address->getStreetLine(2)),
            'admin_area_2' => trim((string) $address->getCity()),
            'admin_area_1' => trim((string) $regionCode),
            'postal_code' => trim((string) $address->getPostcode()),
            'country_code' => $countryId,
        ];

        return [
            'name' => ['full_name' => $fullName],
            'address' => array_filter(
                $paypalAddress,
                static fn(string $value): bool => $value !== '',
            ),
        ];
    }

    /**
     * Check whether the total submitted from the rendered review page is current.
     */
    private function _isPostedGrandTotalCurrent(Mage_Sales_Model_Quote $quote): bool
    {
        $postedTotal = trim((string) $this->getRequest()->getPost('quote_grand_total'));
        if ($postedTotal === '') {
            return true;
        }

        return hash_equals($this->_formatQuoteGrandTotal($quote), $postedTotal);
    }

    /**
     * Build a stable fingerprint for the cart contents and pricing state.
     */
    private function _buildQuoteFingerprint(Mage_Sales_Model_Quote $quote): string
    {
        $items = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $items[] = [
                'item_id' => (int) $item->getId(),
                'product_id' => (int) $item->getProductId(),
                'sku' => (string) $item->getSku(),
                'type' => (string) $item->getProductType(),
                'qty' => (float) $item->getQty(),
                'price' => (float) $item->getCalculationPrice(),
                'base_price' => (float) $item->getBaseCalculationPrice(),
                'row_total' => (float) $item->getRowTotal(),
                'base_row_total' => (float) $item->getBaseRowTotal(),
                'discount' => (float) $item->getDiscountAmount(),
                'base_discount' => (float) $item->getBaseDiscountAmount(),
            ];
        }

        usort($items, static fn(array $left, array $right): int => $left['item_id'] <=> $right['item_id']);

        $payload = json_encode([
            'quote_id' => (int) $quote->getId(),
            'store_id' => (int) $quote->getStoreId(),
            'customer_id' => (int) $quote->getCustomerId(),
            'currency' => $this->_getQuoteCurrencyCode($quote),
            'coupon' => $quote->getCouponCode(),
            'items' => $items,
        ]);

        return hash('sha256', $payload === false ? '' : $payload);
    }

    /**
     * Render the current totals block for AJAX updates.
     */
    private function _renderTotalsHtml(Mage_Sales_Model_Quote $quote): string
    {
        $this->loadLayout(false);
        $block = $this->getLayout()
            ->createBlock('checkout/cart_totals', 'paypal.express.review.totals.ajax');
        if (!$block instanceof Mage_Checkout_Block_Cart_Totals) {
            return '';
        }

        $block->setTemplate('checkout/cart/totals.phtml')
            ->setCustomQuote($quote);

        return $block->toHtml();
    }

    /**
     * Format the quote grand total in the PayPal currency precision.
     */
    private function _formatQuoteGrandTotal(Mage_Sales_Model_Quote $quote): string
    {
        return Mage::helper('paypal')->formatPrice((float) $quote->getGrandTotal(), $this->_getQuoteCurrencyCode($quote));
    }

    /**
     * Resolve quote currency.
     */
    private function _getQuoteCurrencyCode(Mage_Sales_Model_Quote $quote): string
    {
        $currency = $quote->getOrderCurrencyCode();
        if ($currency === null || $currency === '') {
            $currency = $quote->getQuoteCurrencyCode();
        }

        return (string) $currency;
    }

    /**
     * Validate checkout agreements submitted from the review form.
     */
    private function _validateCheckoutAgreements(): void
    {
        $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
        if ($requiredAgreements === null || $requiredAgreements === []) {
            return;
        }

        $postedAgreementData = $this->getRequest()->getPost('agreement', []);
        $postedAgreements = is_array($postedAgreementData) ? array_keys($postedAgreementData) : [];
        if (array_diff($requiredAgreements, $postedAgreements) !== []) {
            Mage::throwException(
                Mage::helper('paypal')->__('Please agree to all the terms and conditions before placing the order.'),
            );
        }
    }

    /**
     * Return checkout quote object.
     */
    private function _getQuote(): Mage_Sales_Model_Quote
    {
        if (!$this->_quote instanceof Mage_Sales_Model_Quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }

        return $this->_quote;
    }

    /**
     * Retrieves the checkout session model.
     */
    private function _getCheckoutSession(): Mage_Checkout_Model_Session
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Retrieves the PayPal model instance.
     */
    private function _getPaypal(): Mage_Paypal_Model_Paypal
    {
        if (!$this->_paypal instanceof Mage_Paypal_Model_Paypal) {
            $this->_paypal = Mage::getModel('paypal/paypal');
        }

        return $this->_paypal;
    }

    /**
     * Resolve the shortcut context.
     */
    private function _getShortcutContext(): string
    {
        $context = (string) $this->getRequest()->getParam('shortcut_context', self::SHORTCUT_CONTEXT_CART);

        return $context === self::SHORTCUT_CONTEXT_PRODUCT ? self::SHORTCUT_CONTEXT_PRODUCT : self::SHORTCUT_CONTEXT_CART;
    }

    /**
     * Redirect back to the current PayPal review page.
     */
    private function _redirectReview(string $orderId): void
    {
        $this->_redirect('paypal/express/review', [
            'token' => $orderId,
            'form_key' => Mage::getSingleton('core/session')->getFormKey(),
        ]);
    }

    /**
     * Send JSON response.
     *
     * @param array<string, mixed> $data
     */
    private function _jsonResponse(array $data, int $statusCode = 200): void
    {
        $this->getResponse()
            ->setHttpResponseCode($statusCode)
            ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)
            ->setBody(Mage::helper('core')->jsonEncode($data));
    }
}
