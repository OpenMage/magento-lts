<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use Carbon\Carbon;

/**
 * Finalizes Magento orders after PayPal authorization or capture.
 */
class Mage_Paypal_Model_Checkout_Finalizer extends Mage_Core_Model_Abstract
{
    /**
     * @var null|Mage_Customer_Model_Session
     */
    protected $_customerSession = null;

    /**
     * Prepare the quote customer state before submitAll().
     *
     * @return bool true when a new customer account must be involved after submit
     */
    public function prepareQuoteForCheckout(
        Mage_Sales_Model_Quote $quote,
        bool $saveCustomerAddresses = true
    ): bool {
        $isNewCustomer = false;
        switch (Mage::getSingleton('checkout/type_onepage')->getCheckoutMethod()) {
            case Mage_Checkout_Model_Type_Onepage::METHOD_GUEST:
                $this->_prepareGuestQuote($quote);
                break;
            case Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER:
                $this->_prepareNewCustomerQuote($quote, $saveCustomerAddresses);
                $isNewCustomer = true;
                break;
            default:
                $this->_prepareCustomerQuote($quote, $saveCustomerAddresses);
                break;
        }

        return $isNewCustomer;
    }

    /**
     * Sets the quote addresses to ignore validation during order placement.
     */
    public function ignoreAddressValidation(Mage_Sales_Model_Quote $quote): void
    {
        $quote->getBillingAddress()->setShouldIgnoreValidation(true);
        if ((int) $quote->getIsVirtual() === 0) {
            $quote->getShippingAddress()->setShouldIgnoreValidation(true);
        }
    }

    /**
     * Finalize the order after the quote service has submitted it.
     */
    public function finalizeSubmittedOrder(
        Mage_Sales_Model_Quote $quote,
        Mage_Sales_Model_Service_Quote $service,
        bool $isAuthorize,
        bool $isNewCustomer
    ): ?Mage_Sales_Model_Order {
        $quote->save();
        if ($isNewCustomer) {
            try {
                $this->_involveNewCustomer($quote);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        $order = $service->getOrder();
        if ($order === null) {
            return null;
        }

        $quotePayment = $quote->getPayment();
        $orderPayment = $order->getPayment();
        assert($orderPayment instanceof Mage_Sales_Model_Order_Payment);

        // For Authorize orders paypal_correlation_id still holds the PayPal
        // order id; the real authorization id lives in additional info and
        // is what the authorization-expiration cron needs as last_trans_id.
        $authorizationId = (string) $quotePayment->getAdditionalInformation(
            Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID,
        );
        $lastTransId = ($isAuthorize && $authorizationId !== '')
            ? $authorizationId
            : (string) $quotePayment->getPaypalCorrelationId();
        $orderPayment->setQuotePaymentId($quotePayment->getId())
            ->setLastTransId($lastTransId);
        foreach ($quotePayment->getAdditionalInformation() as $key => $value) {
            $orderPayment->setAdditionalInformation($key, $value);
        }

        $orderPayment->save();

        $transaction = Mage::getModel('sales/order_payment_transaction');
        foreach ($orderPayment->getAdditionalInformation() as $key => $value) {
            $transaction->setAdditionalInformation($key, $value);
        }

        if ($isAuthorize) {
            $transaction->setOrderPaymentObject($orderPayment)
                ->setTxnId($orderPayment->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID))
                ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH)
                ->setIsClosed(false);
            $storeTimezone = (string) Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
            $expirationTime = (string) $orderPayment->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME);

            $date = new DateTime($expirationTime, new DateTimeZone('UTC'));
            $date->setTimezone(new DateTimeZone($storeTimezone));

            $order->addStatusHistoryComment(
                Mage::helper('paypal')->__(
                    'Paypal payment has been authorized, capture is required before date %s',
                    $date->format('Y-m-d H:i:s'),
                ),
                false,
            );
            $order->setState(
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                true,
                Mage::helper('paypal')->__('Payment has been authorized. Capture is required.'),
            )->save();
        } else {
            $transaction->setOrderPaymentObject($orderPayment)
                ->setTxnId($quotePayment->getPaypalCorrelationId())
                ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE)
                ->setIsClosed($orderPayment->getIsTransactionClosed());
            $order->addStatusHistoryComment(
                Mage::helper('paypal')->__('PayPal payment captured successfully. Capture ID: %s', $transaction->getTxnId()),
                false,
            );
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

        // Dispatched only now that last_trans_id, the auth/capture
        // transaction and the online invoice are persisted, so onepage
        // observers see a fully finalized order as they would normally.
        Mage::dispatchEvent(
            'checkout_type_onepage_save_order_after',
            ['order' => $order, 'quote' => $quote],
        );

        $session = $this->_getCheckoutSession();
        $session->clearHelperData();
        $session->setLastQuoteId($quote->getId())
            ->setLastSuccessQuoteId($quote->getId())
            ->setLastOrderId($order->getId())
            ->setLastRealOrderId($order->getIncrementId());
        $order->queueNewOrderEmail();

        Mage::dispatchEvent(
            'checkout_submit_all_after',
            [
                'order' => $order,
                'quote' => $quote,
                'recurring_profiles' => $service->getRecurringPaymentProfiles(),
            ],
        );

        return $order;
    }

    /**
     * Prepare the quote for a guest checkout.
     */
    protected function _prepareGuestQuote(Mage_Sales_Model_Quote $quote): self
    {
        $quote->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(1)
            ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        return $this;
    }

    /**
     * Prepare the quote for a new customer registration during checkout.
     */
    protected function _prepareNewCustomerQuote(Mage_Sales_Model_Quote $quote, bool $saveCustomerAddresses): self
    {
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customerEmail = (string) $quote->getCustomerEmail();
        $customerId = $this->_lookupCustomerId($quote);
        if ($customerId !== null && !$this->_customerEmailExists($customerEmail)) {
            $this->_getCustomerSession()->loginById($customerId);
            return $this->_prepareCustomerQuote($quote, $saveCustomerAddresses);
        }

        $customer = $quote->getCustomer();
        /** @var Mage_Customer_Model_Customer $customer */
        if ($saveCustomerAddresses) {
            $customerBilling = $billing->exportCustomerAddress();
            $customer->addAddress($customerBilling);
            $billing->setCustomerAddress($customerBilling);
            $customerBilling->setIsDefaultBilling(true);
            if ($shipping !== null && (int) $shipping->getSameAsBilling() === 0) {
                $customerShipping = $shipping->exportCustomerAddress();
                $customer->addAddress($customerShipping);
                $shipping->setCustomerAddress($customerShipping);
                $customerShipping->setIsDefaultShipping(true);
            } elseif ($shipping !== null) {
                $customerBilling->setIsDefaultShipping(true);
            }
        }

        $dynamicAttributes = ['customer_dob', 'customer_taxvat', 'customer_gender'];
        foreach ($dynamicAttributes as $attributeCode) {
            $quoteValue = $quote->getData($attributeCode);
            $billingValue = $billing->getData($attributeCode);

            if (
                !in_array($quoteValue, [null, '', false, 0, '0'], true)
                && (in_array($billingValue, [null, '', false, 0, '0'], true))
            ) {
                $billing->setData($attributeCode, $quoteValue);
            }
        }

        Mage::helper('core')->copyFieldset('checkout_onepage_billing', 'to_customer', $billing, $customer);
        $customer->setEmail($customerEmail);
        $customer->setPrefix($quote->getCustomerPrefix());
        $customer->setFirstname($quote->getCustomerFirstname());
        $customer->setMiddlename($quote->getCustomerMiddlename());
        $customer->setLastname($quote->getCustomerLastname());
        $customer->setSuffix($quote->getCustomerSuffix());
        $customer->setPassword($customer->decryptPassword($quote->getPasswordHash()));
        $customer->setPasswordHash($customer->hashPassword($customer->getPassword()));
        $customer->setPasswordCreatedAt(Carbon::now()->getTimestamp());
        $customer->save();

        $quote->setCustomer($customer);
        $quote->setPasswordHash('');

        return $this;
    }

    /**
     * Prepare the quote for an existing, logged-in customer.
     */
    protected function _prepareCustomerQuote(Mage_Sales_Model_Quote $quote, bool $saveCustomerAddresses): self
    {
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customer = $this->_getCustomerSession()->getCustomer();
        if (!$saveCustomerAddresses) {
            $billing->setSaveInAddressBook(0)
                ->setCustomerAddressId(null)
                ->setCustomerAddress(null);
            if ($shipping !== null) {
                $shipping->setSaveInAddressBook(0)
                    ->setCustomerAddressId(null)
                    ->setCustomerAddress(null);
            }

            $quote->setCustomer($customer);
            return $this;
        }

        if ((int) $billing->getCustomerId() === 0 || (int) $billing->getSaveInAddressBook() !== 0) {
            $customerBilling = $billing->exportCustomerAddress();
            $customer->addAddress($customerBilling);
            $billing->setCustomerAddress($customerBilling);
        }

        if (
            $shipping !== null && (((int) $shipping->getCustomerId() === 0 && (int) $shipping->getSameAsBilling() === 0)
                || ((int) $shipping->getSameAsBilling() === 0 && (int) $shipping->getSaveInAddressBook() !== 0))
        ) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
        }

        if (isset($customerBilling) && (int) $customer->getDefaultBilling() === 0) {
            $customerBilling->setIsDefaultBilling(true);
        }

        if (
            $shipping !== null
            && isset($customerBilling)
            && (int) $customer->getDefaultShipping() === 0
            && (int) $shipping->getSameAsBilling() !== 0
        ) {
            $customerBilling->setIsDefaultShipping(true);
        } elseif (
            $shipping !== null
            && isset($customerShipping)
            && (int) $customer->getDefaultShipping() === 0
        ) {
            $customerShipping->setIsDefaultShipping(true);
        }

        $quote->setCustomer($customer);

        return $this;
    }

    /**
     * Checks if a customer with the given email address already exists for the current website.
     */
    protected function _customerEmailExists(string $email): bool
    {
        $customer  = Mage::getModel('customer/customer');
        $websiteId = Mage::app()->getStore()->getWebsiteId();
        if ($websiteId !== null) {
            $customer->setWebsiteId($websiteId);
        }

        $customer->loadByEmail($email);
        return $customer->getId() !== null;
    }

    /**
     * Looks up a customer ID by the email address stored in the quote.
     */
    protected function _lookupCustomerId(Mage_Sales_Model_Quote $quote): ?int
    {
        $customerId = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getWebsite()->getId())
            ->loadByEmail((string) $quote->getCustomerEmail())
            ->getId();

        $customerId = (int) $customerId;
        return $customerId > 0 ? $customerId : null;
    }

    /**
     * Handles post-order actions for a newly registered customer.
     */
    protected function _involveNewCustomer(Mage_Sales_Model_Quote $quote): self
    {
        $customer = $quote->getCustomer();
        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation', '', $quote->getStoreId());
            $url = Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail());
            $this->_getCustomerSession()->addSuccess(
                Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', $url),
            );
        } else {
            $customer->sendNewAccountEmail('registered', '', $quote->getStoreId());
            $this->_getCustomerSession()->loginById((int) $customer->getId());
        }

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
     * Retrieves the checkout session model.
     */
    private function _getCheckoutSession(): Mage_Checkout_Model_Session
    {
        return Mage::getSingleton('checkout/session');
    }
}
