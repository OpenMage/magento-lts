<?php

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
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession = null;

    /**
     * @var Mage_Paypal_Model_Paypal
     */
    protected $_paypal = null;
    /**
     * Create PayPal order
     */
    public function createAction()
    {
        try {
            if (!$this->getRequest()->isAjax()) {
                throw new Mage_Core_Exception('Invalid request');
            }

            if (!$this->getRequest()->isPost() || !$this->getRequest()->getParam('form_key')) {
                Mage::throwException(Mage::helper('core')->__('Invalid form key'));
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

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $quoteCheckoutMethod = Mage::getSingleton('checkout/type_onepage')->getCheckoutMethod();
            if ($customer && $customer->getId()) {
                $this->_getQuote()->assignCustomerWithAddressChange($customer, $this->_getQuote()->getBillingAddress(), $this->_getQuote()->getShippingAddress());
            } elseif ((!$quoteCheckoutMethod
                    || $quoteCheckoutMethod != Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER)
                && !Mage::helper('checkout')->isAllowedGuestCheckout(
                    $this->_getQuote(),
                    $this->_getQuote()->getStoreId()
                )
            ) {
                Mage::getSingleton('core/session')->addNotice(
                    Mage::helper('paypal')->__('To proceed to Checkout, please log in using your email address.')
                );
                $this->_redirectLogin();
                Mage::getSingleton('customer/session')
                    ->setBeforeAuthUrl(Mage::getUrl('*/*/*', ['_current' => true]));
                return;
            }

            $result = $this->_getPaypal()->create($this->_getQuote());

            $this->getResponse()
                ->setHeader('Content-Type', 'application/json')
                ->setBody(Mage::helper('core')->jsonEncode($result));
        } catch (Exception $e) {
            Mage::logException($e);

            $this->getResponse()
                ->setHeader('Content-Type', 'application/json')
                ->setBody(Mage::helper('core')->jsonEncode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]));
        }
    }

    /**
     * Process PayPal payment (authorize or capture based on configuration)
     */
    public function processAction()
    {
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $paymentAction = Mage::getSingleton('paypal/config')->getPaymentAction();

            if (!$orderId) {
                throw new Exception('PayPal order ID is required');
            }
            if (!$this->getRequest()->isPost() || !$this->getRequest()->getParam('form_key')) {
                Mage::throwException(Mage::helper('core')->__('Invalid form key'));
            }

            if ($paymentAction === strtolower(CheckoutPaymentIntent::AUTHORIZE)) {
                $this->_getPaypal()->authorizePayment($orderId, $this->_getQuote());
            } else {
                $this->_getPaypal()->captureOrder($orderId, $this->_getQuote());
            }

            $this->getResponse()
                ->setHeader('Content-Type', 'application/json')->setBody(Mage::helper('core')->jsonEncode([
                    'success' => true
                ]));
        } catch (Exception $e) {
            Mage::logException($e);

            $this->getResponse()
                ->setHeader('Content-Type', 'application/json')->setBody(Mage::helper('core')->jsonEncode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }

    public function placeOrderAction()
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
                    ->setTxnId($orderPayment->getAdditionalInformation(Mage_Paypal_Model_Paypal::PAYPAL_PAYMENT_AUTHORIZATION_ID))
                    ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH)
                    ->setIsClosed(false);
                $storeTimezone = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
                $expirationTime = $orderPayment->getAdditionalInformation(Mage_Paypal_Model_Paypal::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME);

                $date = new DateTime($expirationTime, new DateTimeZone('UTC'));
                $date->setTimezone(new DateTimeZone($storeTimezone));

                $order->addStatusHistoryComment(
                    Mage::helper('paypal')->__('Paypal payment has been authorized, capture is required before date ' . $date->format('Y-m-d H:i:s')),
                    false
                );
                $order->setState(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    true,
                    Mage::helper('paypal')->__('Payment has been authorized. Capture is required.')
                )->save();
            } else {
                $order->addStatusHistoryComment(
                    Mage::helper('paypal')->__('PayPal payment captured successfully. Capture ID: %s', $transaction->getTxnId()),
                    false
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
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return checkout quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    private function _getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Return PayPal model
     *
     * @return Mage_Paypal_Model_Paypal
     */
    private function _getPaypal()
    {
        if (!$this->_paypal) {
            $this->_paypal = Mage::getModel('paypal/paypal');
        }
        return $this->_paypal;
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @return $this
     */
    protected function _prepareGuestQuote()
    {
        $quote = $this->_quote;
        $quote->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        return $this;
    }

    /**
     * Prepare quote for customer registration and customer order submit
     * and restore magento customer data from quote
     *
     * @return $this
     */
    protected function _prepareNewCustomerQuote()
    {
        $quote      = $this->_quote;
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customerId = $this->_lookupCustomerId();
        if ($customerId && !$this->_customerEmailExists($quote->getCustomerEmail())) {
            $this->getCustomerSession()->loginById($customerId);
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
        /**
         * @todo integration with dynamica attributes customer_dob, customer_taxvat, customer_gender
         */
        if ($quote->getCustomerDob() && !$billing->getCustomerDob()) {
            $billing->setCustomerDob($quote->getCustomerDob());
        }

        if ($quote->getCustomerTaxvat() && !$billing->getCustomerTaxvat()) {
            $billing->setCustomerTaxvat($quote->getCustomerTaxvat());
        }

        if ($quote->getCustomerGender() && !$billing->getCustomerGender()) {
            $billing->setCustomerGender($quote->getCustomerGender());
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
     * Prepare quote for customer order submit
     *
     * @return $this
     */
    protected function _prepareCustomerQuote()
    {
        $quote      = $this->_getQuote();
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customer = $this->getCustomerSession()->getCustomer();
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
     * Get customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        if (is_null($this->_customerSession)) {
            $this->_customerSession = Mage::getSingleton('customer/session');
        }
        return $this->_customerSession;
    }

    /**
     * Check if customer email exists
     *
     * @param string $email
     * @return bool
     */
    protected function _customerEmailExists($email)
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
     * Checks if customer with email coming from Express checkout exists
     *
     * @return int
     */
    protected function _lookupCustomerId()
    {
        return Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getWebsite()->getId())
            ->loadByEmail($this->_quote->getCustomerEmail())
            ->getId();
    }

    /**
     * Make sure addresses will be saved without validation errors
     */
    private function _ignoreAddressValidation()
    {
        $this->_quote->getBillingAddress()->setShouldIgnoreValidation(true);
        if (!$this->_quote->getIsVirtual()) {
            $this->_quote->getShippingAddress()->setShouldIgnoreValidation(true);
        }
    }

    /**
     * Redirect to login page
     *
     */
    protected function _redirectLogin()
    {
        $this->setFlag('', 'no-dispatch', true);
        $this->getResponse()->setRedirect(
            Mage::helper('core/url')->addRequestParam(
                Mage::helper('customer')->getLoginUrl(),
                ['context' => 'checkout']
            )
        );
    }

    /**
     * Involve new customer to system
     *
     * @return $this
     */
    protected function _involveNewCustomer()
    {
        $customer = $this->_quote->getCustomer();
        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation', '', $this->_quote->getStoreId());
            $url = Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail());
            $this->getCustomerSession()->addSuccess(
                Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', $url)
            );
        } else {
            $customer->sendNewAccountEmail('registered', '', $this->_quote->getStoreId());
            $this->getCustomerSession()->loginById($customer->getId());
        }
        return $this;
    }
}
