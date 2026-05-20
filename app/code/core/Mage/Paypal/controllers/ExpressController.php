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

            // Drop any leftover Buy-Now quote from a prior attempt so the cart quote is what we see when no fresh product is added.
            $this->_clearExpressQuoteSession();

            $quote = $this->_getQuote();
            if (!$this->_enforceGuestCheckoutAllowed($quote)) {
                return;
            }

            $this->_getShortcutState()->clear($quote);

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

            $result = $this->_getPaypal()->create($quote, $fundingSource);
            $resultId = $result['id'] ?? null;
            if (($result['success'] ?? false) !== true || !is_scalar($resultId) || (string) $resultId === '') {
                Mage::throwException((string) ($result['error'] ?? Mage::helper('paypal')->__('Unable to initialize Express Checkout.')));
            }

            $orderId = (string) $resultId;
            $this->_getShortcutState()->store($quote, $orderId);
            $quote->save();
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
            $this->_getCurrencyLock()->assertHeld($quote);
            $this->_getCurrencyLock()->applyTo($quote);
            $this->_validateShortcutAttempt($quote, $orderId);

            $api = Mage::getSingleton('paypal/helper')->getApi()->setStore($quote->getStore());
            $response = $api->getOrderDetails($orderId);
            if ($response->isError()) {
                Mage::getSingleton('paypal/helper')->handleApiError($response, 'Unable to read PayPal order details.');
            }

            $details = $this->_assertPaypalOrderBelongsToQuote($quote, $response->getBody());
            $this->_assertPatchablePaypalStatus($details);

            Mage::getModel('paypal/express_addressImporter')->importFromOrderDetails($quote, $response->getBody());
            $this->_ignoreAddressValidation($quote);
            // Pre-apply the guest customer-state that Finalizer::_prepareGuestQuote() would set at placeOrder time.
            // Without this, customer_group_id shifts between the review collect (default group) and the placeOrder
            // collect (NOT_LOGGED_IN_ID), which moves tax totals and trips _isPostedGrandTotalCurrent.
            $this->_normalizeCustomerStateForGuest($quote);
            $this->_prepareShippingRates($quote);
            $quote->collectTotals()->save();

            $patch = $this->_buildPatch($quote);
            if (Mage::getSingleton('paypal/config')->isDebugEnabled()) {
                Mage::log(['order_id' => $orderId, 'patch' => $patch], null, 'paypal_patch.log', true);
            }

            $patchResponse = $api->patchOrder($orderId, $patch);
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

            $reviewBlock = $this->getLayout()->getBlock('paypal.express.review');
            if ($reviewBlock instanceof Mage_Paypal_Block_Express_Review) {
                $reviewBlock->setQuote($quote);
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
            $this->_getCurrencyLock()->assertHeld($quote);
            $this->_getCurrencyLock()->applyTo($quote);
            $this->_validateShortcutAttempt($quote, $orderId);

            if ($quote->isVirtual()) {
                Mage::throwException(Mage::helper('paypal')->__('This order does not require a shipping method.'));
            }

            $shippingMethod = trim((string) $this->getRequest()->getPost('shipping_method'));
            if ($shippingMethod === '') {
                Mage::throwException(Mage::helper('paypal')->__('Please specify a shipping method.'));
            }

            $this->_ignoreAddressValidation($quote);
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
            $this->_getCurrencyLock()->assertHeld($quote);
            $this->_getCurrencyLock()->applyTo($quote);
            $this->_validateShortcutAttempt($quote, $orderId);
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
            $this->_ignoreAddressValidation($quote);

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

            $patch = $this->_buildPatch($quote);
            if (Mage::getSingleton('paypal/config')->isDebugEnabled()) {
                Mage::log(['order_id' => $orderId, 'patch' => $patch], null, 'paypal_patch.log', true);
            }

            $patchResponse = $api->patchOrder($orderId, $patch);
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

            // Post-success: the placed-order quote is now inactive (set by submitAll) and is referenced by the order;
            // do NOT delete it. Just drop the session pointer and restore the user's prior browsing currency.
            $this->_getCheckoutSession()->unsetData(Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_BUY_NOW_QUOTE_ID);
            $this->_getCurrencyLock()->restore($quote);
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
            $this->_getShortcutState()->clear($quote);
            $quote->setReservedOrderId(null)->save();
            $this->_getCurrencyLock()->restore($quote);
            $this->_clearExpressQuoteSession();
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

        $quote = $this->_createExpressQuote();

        $request = new Varien_Object($params);
        if (!$request->hasQty()) {
            $defaultQty = Mage::helper('catalog/product')->getDefaultQty($product);
            $request->setQty($defaultQty !== null && $defaultQty !== '' ? $defaultQty : 1);
        }

        if (!$product->isConfigurable() && $product->getStockItem() !== null) {
            $minimumQty = $product->getStockItem()->getMinSaleQty();
            if ($minimumQty > 0 && $request->getQty() < $minimumQty) {
                $request->setQty($minimumQty);
            }
        }

        $result = $quote->addProduct($product, $request);
        if (is_string($result)) {
            Mage::throwException($result);
        }

        $quote->save();
        $this->_getCheckoutSession()->setData(Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_BUY_NOW_QUOTE_ID, (int) $quote->getId());
        $this->_quote = $quote;
    }

    /**
     * Build a fresh, isolated quote for the product-page Buy Now flow so the user's cart is not touched.
     */
    private function _createExpressQuote(): Mage_Sales_Model_Quote
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getModel('sales/quote');
        $quote->setStore(Mage::app()->getStore());

        $customerSession = Mage::getSingleton('customer/session');
        if ($customerSession->isLoggedIn()) {
            $customer = $customerSession->getCustomer();
            $quote->assignCustomer($customer);
        }

        return $quote;
    }

    /**
     * Forget any previously stored Buy-Now quote and delete the abandoned record so a popup-closed-mid-flow attempt
     * doesn't leave orphans behind for the stale-quote cron to chase.
     */
    private function _clearExpressQuoteSession(): void
    {
        $session = $this->_getCheckoutSession();
        $expressQuoteId = (int) $session->getData(Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_BUY_NOW_QUOTE_ID);
        if ($expressQuoteId > 0) {
            try {
                /** @var Mage_Sales_Model_Quote $stale */
                $stale = Mage::getModel('sales/quote')->loadByIdWithoutStore($expressQuoteId);
                // Only delete an abandoned (still-active) Buy-Now quote. A quote whose order succeeded is already
                // inactive via submitAll() and may be referenced by the placed order — leave it alone.
                if ((int) $stale->getId() === $expressQuoteId && (int) $stale->getIsActive() === 1) {
                    $stale->delete();
                }
            } catch (Throwable $throwable) {
                Mage::logException($throwable);
            }
        }

        $session->unsetData(Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_BUY_NOW_QUOTE_ID);
        // Drop any leftover prior-currency snapshot so the next attempt's lock() takes a fresh one.
        $this->_getCurrencyLock()->forgetPriorSnapshot();
        $this->_quote = false;
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
     *
     * Mirrors the legacy Mage_Paypal_Model_Express_Checkout::start() — the
     * shipping address is left intact so PayPal can echo any pre-set values
     * back, and the address importer overwrites it on return regardless.
     */
    private function _prepareQuoteForStart(Mage_Sales_Model_Quote $quote): void
    {
        if ((int) $quote->getIsMultiShipping() !== 0) {
            $quote->setIsMultiShipping(false);
            $quote->removeAllAddresses();
        }

        // Clear the previously-selected method and force fresh rate collection so the PayPal popup shows the current shipping cost, not a stale value from a prior session.
        if (!$quote->isVirtual()) {
            $quote->getShippingAddress()
                ->setShippingMethod(null)
                ->setShippingDescription(null)
                ->setCollectShippingRates(true);
        }

        $this->_getCurrencyLock()->lock($quote);
        $quote->getPayment()->setMethod('paypal');
        $quote->collectTotals()->save();
    }

    /**
     * Bypass address validation while we mutate quote addresses via PayPal
     * data. Mirrors the legacy _ignoreAddressValidation() helper.
     */
    private function _ignoreAddressValidation(Mage_Sales_Model_Quote $quote): void
    {
        $quote->getBillingAddress()->setShouldIgnoreValidation(true);
        if (!$quote->isVirtual()) {
            $quote->getShippingAddress()->setShouldIgnoreValidation(true);
        }
    }

    /**
     * Mirror the guest customer-state that Finalizer::_prepareGuestQuote() applies at placeOrder time, so the
     * review-page totals collect against the same customer_group_id (NOT_LOGGED_IN_ID) the placeOrder collect uses.
     * Logged-in users are untouched — assignCustomer() in _createExpressQuote already set their group correctly.
     */
    private function _normalizeCustomerStateForGuest(Mage_Sales_Model_Quote $quote): void
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return;
        }

        $billingEmail = trim((string) $quote->getBillingAddress()->getEmail());
        if ($billingEmail === '') {
            $billingEmail = trim((string) $quote->getCustomerEmail());
        }

        $quote->setCustomerEmail($billingEmail)
            ->setCustomerIsGuest(1)
            ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
    }

    /**
     * Validate that the current request belongs to the active PayPal shortcut attempt.
     */
    private function _validateShortcutAttempt(Mage_Sales_Model_Quote $quote, string $orderId): void
    {
        if ($orderId === '') {
            Mage::throwException(Mage::helper('paypal')->__('PayPal order ID is required.'));
        }

        $storedOrderId = $this->_getShortcutState()->getOrderId($quote);
        if ($storedOrderId === '' || !hash_equals($storedOrderId, $orderId)) {
            Mage::throwException(Mage::helper('paypal')->__('PayPal checkout session has expired. Please restart PayPal checkout.'));
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

        $state = $this->_getShortcutState();
        $storedOrderId = $state->getOrderId($quote);

        $paypalOrderId = (string) ($details['id'] ?? '');
        if ($paypalOrderId === '' || !hash_equals($storedOrderId, $paypalOrderId)) {
            Mage::throwException(Mage::helper('paypal')->__('The PayPal order does not match this checkout session.'));
        }

        $reservedOrderId = $state->getReservedOrderId($quote);

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

        $lockedCurrency = $state->getLockedCurrency($quote);

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
     * PayPal Smart Buttons do not return a shipping-method hint (the buyer selects shipping on the store-side review
     * page, not in PayPal's UI), so auto-selection falls back to the cheapest valid rate when no valid method is
     * pre-selected.
     */
    private function _prepareShippingRates(Mage_Sales_Model_Quote $quote): void
    {
        if ($quote->isVirtual()) {
            return;
        }

        $address = $quote->getShippingAddress();

        // First pass: collect totals so the shipping collector sees real items, weight and subtotal when it computes rates.
        $address->setCollectShippingRates(true);

        $quote->collectTotals();

        $validRates = $this->_getValidShippingRates($address);
        if ($validRates === []) {
            $address->setShippingMethod(null);
            $this->_getCheckoutSession()->addError(
                Mage::helper('paypal')->__('Sorry, no shipping quotes are available for this order at this time.'),
            );
            return;
        }

        $currentMethod = (string) $address->getShippingMethod();
        $needsAutoSelect = true;
        if ($currentMethod !== '') {
            $currentRate = $address->getShippingRateByCode($currentMethod);
            if ($currentRate instanceof Mage_Sales_Model_Quote_Address_Rate
                && in_array($currentRate->getErrorMessage(), [null, false, ''], true)
            ) {
                $needsAutoSelect = false;
            }
        }

        if ($needsAutoSelect) {
            usort($validRates, static function (
                Mage_Sales_Model_Quote_Address_Rate $left,
                Mage_Sales_Model_Quote_Address_Rate $right
            ): int {
                $leftPrice = (float) $left->getPrice();
                $rightPrice = (float) $right->getPrice();

                if ($leftPrice < $rightPrice) {
                    return -1;
                }

                if ($leftPrice > $rightPrice) {
                    return 1;
                }

                return strcmp((string) $left->getCode(), (string) $right->getCode());
            });

            $address->setShippingMethod($validRates[0]->getCode());
        }

        // Flag the next collectTotals() to re-run the shipping collector so it actually prices the selected method.
        $address->setCollectShippingRates(true);
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
        $referenceId = $this->_getShortcutState()->getReservedOrderId($quote);
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

        return $patch;
    }

    /**
     * Check whether the total submitted from the rendered review page is current.
     */
    private function _isPostedGrandTotalCurrent(Mage_Sales_Model_Quote $quote): bool
    {
        $postedTotal = trim((string) $this->getRequest()->getPost('quote_grand_total'));
        if ($postedTotal === '') {
            return false;
        }

        return hash_equals($this->_formatQuoteGrandTotal($quote), $postedTotal);
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
        if ($this->_quote instanceof Mage_Sales_Model_Quote) {
            return $this->_quote;
        }

        $expressQuoteId = (int) $this->_getCheckoutSession()->getData(Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_BUY_NOW_QUOTE_ID);
        if ($expressQuoteId > 0) {
            /** @var Mage_Sales_Model_Quote $quote */
            $quote = Mage::getModel('sales/quote')->loadByIdWithoutStore($expressQuoteId);
            if ((int) $quote->getId() === $expressQuoteId) {
                $this->_quote = $quote;
                return $quote;
            }

            $this->_getCheckoutSession()->unsetData(Mage_Paypal_Model_Payment::PAYPAL_EXPRESS_BUY_NOW_QUOTE_ID);
        }

        $this->_quote = $this->_getCheckoutSession()->getQuote();
        return $this->_quote;
    }

    /**
     * Retrieves the checkout session model.
     */
    private function _getCheckoutSession(): Mage_Checkout_Model_Session
    {
        return Mage::getSingleton('checkout/session');
    }

    private function _getShortcutState(): Mage_Paypal_Model_Express_ShortcutState
    {
        /** @var Mage_Paypal_Model_Express_ShortcutState $state */
        $state = Mage::getSingleton('paypal/express_shortcutState');
        return $state;
    }

    private function _getCurrencyLock(): Mage_Paypal_Model_Express_CurrencyLock
    {
        /** @var Mage_Paypal_Model_Express_CurrencyLock $lock */
        $lock = Mage::getSingleton('paypal/express_currencyLock');
        return $lock;
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
