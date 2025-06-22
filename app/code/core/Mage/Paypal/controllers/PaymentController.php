<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use PaypalServerSdkLib\Models\CheckoutPaymentIntent;

/**
 * PayPal payment controller
 */
class Mage_Paypal_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Mage_Sales_Model_Quote|false
     */
    protected $_quote = false;

    /**
     * @var Mage_Customer_Model_Session|null
     */
    protected $_customerSession = null;

    /**
     * @var Mage_Paypal_Model_Paypal|null
     */
    protected $_paypal = null;

    private const CONTENT_TYPE_JSON = 'application/json';

    /**
     * Handles the AJAX request to create a PayPal order.
     * Validates the quote and creates a PayPal order via the PayPal model.
     */
    public function createAction(): void
    {
        try {
            if (!$this->getRequest()->isPost() || !$this->getRequest()->getParam('form_key')) {
                Mage::throwException(Mage::helper('paypal')->__('Invalid form key'));
            }

            if (!$this->_getQuote()->hasItems() || $this->_getQuote()->getHasError()) {
                $this->getResponse()->setHeader('HTTP/1.1', '403 Forbidden');
                Mage::throwException(Mage::helper('paypal')->__('Unable to initialize Express Checkout.'));
            }

            if (!$this->_getQuote()->getQuoteCurrencyCode()) {
                $this->_getQuote()->setQuoteCurrencyCode(Mage::app()->getStore()->getCurrentCurrencyCode());
                $this->_getQuote()->save();
            }

            if ($this->_getQuote()->getIsMultiShipping()) {
                $this->_getQuote()->setIsMultiShipping(false);
                $this->_getQuote()->removeAllAddresses();
            }

            $customer = $this->_getCustomerSession()->getCustomer();
            $quoteCheckoutMethod = Mage::getSingleton('checkout/type_onepage')->getCheckoutMethod();
            if ($customer && $customer->getId()) {
                $this->_getQuote()->assignCustomerWithAddressChange($customer, $this->_getQuote()->getBillingAddress(), $this->_getQuote()->getShippingAddress());
            } elseif ((!$quoteCheckoutMethod
                    || $quoteCheckoutMethod != Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER)
                && !Mage::helper('checkout')->isAllowedGuestCheckout(
                    $this->_getQuote(),
                    $this->_getQuote()->getStoreId(),
                )
            ) {
                Mage::getSingleton('core/session')->addNotice(
                    Mage::helper('paypal')->__('To proceed to Checkout, please log in using your email address.'),
                );
                $this->_redirectLogin();
                $this->_getCustomerSession()->setBeforeAuthUrl(Mage::getUrl('*/*/*', ['_current' => true]));
                return;
            }

            $result = $this->_getPaypal()->create($this->_getQuote());

            $this->getResponse()
                ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)
                ->setBody(Mage::helper('core')->jsonEncode($result));
        } catch (Exception $e) {
            Mage::logException($e);

            $this->getResponse()
                ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)
                ->setBody(Mage::helper('core')->jsonEncode([
                    'success' => false,
                    'error' => $e->getMessage(),
                ]));
        }
    }

    /**
     * Processes the PayPal payment after customer approval on the PayPal side.
     * This action will either authorize or capture the payment based on the store's configuration.
     *
     * @throws Exception
     */
    public function processAction(): void
    {
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $paymentAction = Mage::getSingleton('paypal/config')->getPaymentAction();

            if (!$orderId) {
                Mage::throwException(Mage::helper('paypal')->__('PayPal order ID is required'));
            }
            if (!$this->getRequest()->isPost() || !$this->getRequest()->getParam('form_key')) {
                Mage::throwException(Mage::helper('paypal')->__('Invalid form key'));
            }

            if ($paymentAction === strtolower(CheckoutPaymentIntent::AUTHORIZE)) {
                $this->_getPaypal()->authorizePayment($orderId, $this->_getQuote());
            } else {
                $this->_getPaypal()->captureOrder($orderId, $this->_getQuote());
            }

            $this->getResponse()
                ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)->setBody(Mage::helper('core')->jsonEncode([
                    'success' => true,
                ]));
        } catch (Exception $e) {
            Mage::logException($e);

            $this->getResponse()
                ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)->setBody(Mage::helper('core')->jsonEncode([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]));
        }
    }

    /**
     * Places the Magento order after the PayPal payment has been successfully processed.
     * It handles guest, new customer, and existing customer checkouts, creates the order,
     * and redirects to the success or cart page.
     */
    public function placeOrderAction(): void
    {
        try {
            $isNewCustomer = false;
            switch (Mage::getSingleton('checkout/type_onepage')->getCheckoutMethod()) {
                case Mage_Checkout_Model_Type_Onepage::METHOD_GUEST:
                    $this->_prepareGuestQuote();
                    break;
                case Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER:
                    $this->_prepareNewCustomerQuote();
                    $isNewCustomer = true;
                    break;
                default:
                    $this->_prepareCustomerQuote();
                    break;
            }
            $this->_ignoreAddressValidation();
            $this->_quote->collectTotals();
            $session = $this->_getCheckoutSession();
            $service = Mage::getModel('sales/service_quote', $this->_getQuote());

            $service->submitAll();
            $this->_quote->save();
            if ($isNewCustomer) {
                try {
                    $this->_involveNewCustomer();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            /** @var Mage_Sales_Model_Order $order */
            $order = $service->getOrder();
            if (!$order) {
                return;
            }

            $quotePayment = $this->_quote->getPayment();
            $orderPayment = $order->getPayment();
            $orderPayment->setQuotePaymentId($quotePayment->getId())
                ->setLastTransId($quotePayment->getPaypalCorrelationId());
            foreach ($quotePayment->getAdditionalInformation() as $key => $value) {
                $orderPayment->setAdditionalInformation($key, $value);
            }
            $orderPayment->save();

            $paymentAction = Mage::getSingleton('paypal/config')->getPaymentAction();
            $isAuthorize = ($paymentAction === strtolower(PaypalServerSdkLib\Models\CheckoutPaymentIntent::AUTHORIZE));
            $transaction = Mage::getModel('sales/order_payment_transaction');
            foreach ($orderPayment->getAdditionalInformation() as $key => $value) {
                $transaction->setAdditionalInformation($key, $value);
            }
            if ($isAuthorize) {
                $transaction->setOrderPaymentObject($orderPayment)
                    ->setTxnId($orderPayment->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID))
                    ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH)
                    ->setIsClosed(false);
                $storeTimezone = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
                $expirationTime = $orderPayment->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME);

                $date = new DateTime($expirationTime, new DateTimeZone('UTC'));
                $date->setTimezone(new DateTimeZone($storeTimezone));

                $order->addStatusHistoryComment(
                    Mage::helper('paypal')->__('Paypal payment has been authorized, capture is required before date ' . $date->format('Y-m-d H:i:s')),
                    false,
                );
                $order->setState(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    true,
                    Mage::helper('paypal')->__('Payment has been authorized. Capture is required.'),
                )->save();
            } else {
                $order->addStatusHistoryComment(
                    Mage::helper('paypal')->__('PayPal payment captured successfully. Capture ID: %s', $transaction->getTxnId()),
                    false,
                );
                $transaction->setOrderPaymentObject($orderPayment)
                    ->setTxnId($quotePayment->getPaypalCorrelationId())
                    ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE)
                    ->setIsClosed($orderPayment->getIsTransactionClosed());
            }
            $transaction->save();

            if (!$isAuthorize && $order->canInvoice()) {
                $invoice = $order->prepareInvoice();
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                $invoice->register()->setIsPaid(true)->setIsTransactionPending(false)->setTransactionId($transaction->getTxnId());
                Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($order)
                    ->save();
            }

            $session->clearHelperData();
            $session->setLastQuoteId($this->_quote->getId())
                ->setLastSuccessQuoteId($this->_quote->getId())
                ->setLastOrderId($order->getId())
                ->setLastRealOrderId($order->getIncrementId());
            $order->queueNewOrderEmail();

            $this->_redirect('checkout/onepage/success');
            return;
        } catch (Exception $e) {
            Mage::logException($e);

            Mage::helper('checkout')->sendPaymentFailedEmail($this->_quote, $e->getMessage());
            $session->addError($e->getMessage());
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Retrieves the checkout session model.
     */
    protected function _getCheckoutSession(): Mage_Checkout_Model_Session
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return checkout quote object
     */
    private function _getQuote(): Mage_Sales_Model_Quote
    {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Retrieves the PayPal model instance.
     */
    private function _getPaypal(): Mage_Paypal_Model_Paypal
    {
        if (!$this->_paypal) {
            $this->_paypal = Mage::getModel('paypal/paypal');
        }
        return $this->_paypal;
    }

    /**
     * Prepares the quote for a guest checkout.
     */
    protected function _prepareGuestQuote(): self
    {
        $quote = $this->_getQuote();
        $quote->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(1)
            ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        return $this;
    }

    /**
     * Prepares the quote for a new customer registration during checkout.
     */
    protected function _prepareNewCustomerQuote(): self
    {
        $quote      = $this->_quote;
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customerId = $this->_lookupCustomerId();
        if ($customerId && !$this->_customerEmailExists($quote->getCustomerEmail())) {
            $this->_getCustomerSession()->loginById($customerId);
            return $this->_prepareCustomerQuote();
        }

        $customer = $quote->getCustomer();
        /** @var Mage_Customer_Model_Customer $customer */
        $customerBilling = $billing->exportCustomerAddress();
        $customer->addAddress($customerBilling);
        $billing->setCustomerAddress($customerBilling);
        $customerBilling->setIsDefaultBilling(true);
        if ($shipping && !$shipping->getSameAsBilling()) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
            $customerShipping->setIsDefaultShipping(true);
        } elseif ($shipping) {
            $customerBilling->setIsDefaultShipping(true);
        }

        $dynamicAttributes = ['customer_dob', 'customer_taxvat', 'customer_gender'];
        foreach ($dynamicAttributes as $attributeCode) {
            $quoteValue = $quote->getData($attributeCode);
            $billingValue = $billing->getData($attributeCode);

            if ($quoteValue && !$billingValue) {
                $billing->setData($attributeCode, $quoteValue);
            }
        }

        Mage::helper('core')->copyFieldset('checkout_onepage_billing', 'to_customer', $billing, $customer);
        $customer->setEmail($quote->getCustomerEmail());
        $customer->setPrefix($quote->getCustomerPrefix());
        $customer->setFirstname($quote->getCustomerFirstname());
        $customer->setMiddlename($quote->getCustomerMiddlename());
        $customer->setLastname($quote->getCustomerLastname());
        $customer->setSuffix($quote->getCustomerSuffix());
        $customer->setPassword($customer->decryptPassword($quote->getPasswordHash()));
        $customer->setPasswordHash($customer->hashPassword($customer->getPassword()));
        $customer->setPasswordCreatedAt(time());
        $customer->save();
        $quote->setCustomer($customer);
        $quote->setPasswordHash('');

        return $this;
    }

    /**
     * Prepares the quote for an existing, logged-in customer.
     */
    protected function _prepareCustomerQuote(): self
    {
        $quote      = $this->_getQuote();
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customer = $this->_getCustomerSession()->getCustomer();
        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            $customerBilling = $billing->exportCustomerAddress();
            $customer->addAddress($customerBilling);
            $billing->setCustomerAddress($customerBilling);
        }
        if (
            $shipping && ((!$shipping->getCustomerId() && !$shipping->getSameAsBilling())
                || (!$shipping->getSameAsBilling() && $shipping->getSaveInAddressBook()))
        ) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
        }

        if (isset($customerBilling) && !$customer->getDefaultBilling()) {
            $customerBilling->setIsDefaultBilling(true);
        }
        if ($shipping && isset($customerBilling) && !$customer->getDefaultShipping() && $shipping->getSameAsBilling()) {
            $customerBilling->setIsDefaultShipping(true);
        } elseif ($shipping && isset($customerShipping) && !$customer->getDefaultShipping()) {
            $customerShipping->setIsDefaultShipping(true);
        }
        $quote->setCustomer($customer);

        return $this;
    }

    /**
     * Retrieves the customer session model.
     */
    private function _getCustomerSession(): Mage_Customer_Model_Session
    {
        if (is_null($this->_customerSession)) {
            $this->_customerSession = Mage::getSingleton('customer/session');
        }
        return $this->_customerSession;
    }

    /**
     * Checks if a customer with the given email address already exists for the current website.
     *
     * @param string $email The customer email to check.
     */
    protected function _customerEmailExists(string $email): bool
    {
        $result    = false;
        $customer  = Mage::getModel('customer/customer');
        $websiteId = Mage::app()->getStore()->getWebsiteId();
        if (!is_null($websiteId)) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if (!is_null($customer->getId())) {
            $result = true;
        }

        return $result;
    }

    /**
     * Looks up a customer ID by the email address stored in the quote.
     *
     * @return int|null The customer ID if found, otherwise null.
     */
    protected function _lookupCustomerId(): ?int
    {
        return Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getWebsite()->getId())
            ->loadByEmail($this->_quote->getCustomerEmail())
            ->getId();
    }

    /**
     * Sets the quote addresses to ignore validation during the order placement process.
     */
    private function _ignoreAddressValidation(): void
    {
        $this->_quote->getBillingAddress()->setShouldIgnoreValidation(true);
        if (!$this->_quote->getIsVirtual()) {
            $this->_quote->getShippingAddress()->setShouldIgnoreValidation(true);
        }
    }
    /**
     * Redirects the user to the customer login page.
     */
    protected function _redirectLogin(): void
    {
        $this->setFlag('', 'no-dispatch', true);
        $this->getResponse()->setRedirect(
            Mage::helper('core/url')->addRequestParam(
                Mage::helper('customer')->getLoginUrl(),
                ['context' => 'checkout'],
            ),
        );
    }

    /**
     * Handles the post-order actions for a newly registered customer, such as sending confirmation emails.
     */
    protected function _involveNewCustomer(): self
    {
        $customer = $this->_quote->getCustomer();
        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation', '', $this->_quote->getStoreId());
            $url = Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail());
            $this->_getCustomerSession()->addSuccess(
                Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', $url),
            );
        } else {
            $customer->sendNewAccountEmail('registered', '', $this->_quote->getStoreId());
            $this->_getCustomerSession()->loginById($customer->getId());
        }
        return $this;
    }
}
