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
 * PayPal payment controller
 */
class Mage_Paypal_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var false|Mage_Sales_Model_Quote
     */
    protected $_quote = false;

    /**
     * @var null|Mage_Customer_Model_Session
     */
    protected $_customerSession = null;

    /**
     * @var null|Mage_Paypal_Model_Paypal
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
            if (!$this->_validateFormKey()) {
                Mage::throwException(Mage::helper('paypal')->__('Invalid form key.'));
            }

            // Enforce checkout agreements before the PayPal order is created,
            // so the customer is never charged without accepting the terms.
            $this->_validateCheckoutAgreements();

            // The cart may already be paid for: settle that payment before the buyer can start another one.
            if ($this->_getPlacement()->resolvePendingCharge($this->_getQuote()) instanceof Mage_Sales_Model_Order) {
                $this->getResponse()
                    ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)
                    ->setBody(Mage::helper('core')->jsonEncode([
                        'success' => false,
                        'redirect_url' => $this->_getSuccessUrl(),
                    ]));
                return;
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

            $fundingSource = (string) $this->getRequest()->getParam('funding_source') ?: 'paypal';

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

            // This is the in-checkout flow: the shopper already supplied an address here, so the
            // quote must carry a usable shipping method before we quote an amount to PayPal.
            // (The shortcut flow legitimately starts address-less and picks the method later on
            // the review page, which is why ExpressController::startAction has no equivalent check.)
            // Without this, an unselected shipping method survives all the way to submitAll(), which
            // rejects it only after the buyer has been charged.
            Mage::getSingleton('paypal/helper')->validateShippingMethodForQuote($this->_getQuote());

            $result = $this->_getPaypal()->create($this->_getQuote(), $fundingSource, true);
            $this->getResponse()
                ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)
                ->setBody(Mage::helper('core')->jsonEncode($result));
        } catch (Exception $exception) {
            Mage::logException($exception);
            $this->getResponse()
                ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)
                ->setBody(Mage::helper('core')->jsonEncode([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ]));
        }
    }

    /**
     * Charges the approved PayPal order and places the Magento order in the same request.
     *
     * The browser only has to follow the returned redirect: once the buyer has been charged,
     * placing the order no longer depends on a second request that the browser might never send.
     */
    public function processAction(): void
    {
        try {
            if (!$this->_validateFormKey()) {
                Mage::throwException(Mage::helper('paypal')->__('Invalid form key.'));
            }

            // Agreements must hold here too: this action charges the buyer.
            $this->_validateCheckoutAgreements();

            $orderId = (string) $this->getRequest()->getParam('order_id');
            if ($orderId === '') {
                Mage::throwException(Mage::helper('paypal')->__('PayPal order ID is required'));
            }

            $this->_getPlacement()->placeApprovedOrder($this->_getQuote(), $orderId);

            $this->getResponse()
                ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)->setBody(Mage::helper('core')->jsonEncode([
                    'success' => true,
                    'redirect_url' => $this->_getSuccessUrl(),
                ]));
        } catch (Mage_Paypal_Model_Checkout_UnplacedPaymentException $exception) {
            // The buyer was charged: leave the attempt unmarked so the webhook fallback still places the order.
            Mage::logException($exception);

            $this->getResponse()
                ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)->setBody(Mage::helper('core')->jsonEncode([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ]));
        } catch (Exception $exception) {
            Mage::logException($exception);

            // The buyer is now told this approval failed, so the webhook fallback must not charge it later.
            try {
                $this->_getPlacement()->markCheckoutAttempt(
                    $this->_getQuote(),
                    (string) $this->getRequest()->getParam('order_id'),
                );
            } catch (Exception $markException) {
                Mage::logException($markException);
            }

            // PayPal's re-approval page returns to the Express review, which an in-checkout order cannot use.
            // The next click on the PayPal button starts a fresh order instead.
            $message = $exception instanceof Mage_Paypal_Model_Exception && $exception->isPayerActionRequired()
                ? Mage::helper('paypal')->__('PayPal needs you to confirm this payment again. Please click the PayPal button to continue.')
                : $exception->getMessage();

            $this->getResponse()
                ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)->setBody(Mage::helper('core')->jsonEncode([
                    'success' => false,
                    'message' => $message,
                ]));
        }
    }

    /**
     * Cancel Payment
     */
    public function cancelAction(): void
    {
        try {
            if (!$this->_validateFormKey()) {
                Mage::throwException(Mage::helper('paypal')->__('Invalid form key.'));
            }

            if (Mage::getSingleton('paypal/helper')->hasPendingPaypalCharge($this->_getQuote())) {
                // A payment that may have been charged has to stay on the quote until it is settled.
                $this->getResponse()
                    ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)
                    ->setBody(Mage::helper('core')->jsonEncode(['success' => true]));
                return;
            }

            $payment = $this->_getQuote()->getPayment();
            $additionalInfo = $payment->getAdditionalInformation();
            foreach (array_keys($additionalInfo) as $key) {
                $payment->unsAdditionalInformation((string) $key);
            }

            $payment->setData(Mage_Paypal_Model_Payment::PAYPAL_ORDER_ID, null)
                ->setPaypalCorrelationId('')
                ->save();
            $this->_getQuote()->setReservedOrderId(null)->save();
            $this->getResponse()
                ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)
                ->setBody(Mage::helper('core')->jsonEncode(['success' => true]));
        } catch (Exception $exception) {
            Mage::logException($exception);
            $this->getResponse()
                ->setHeader('Content-Type', self::CONTENT_TYPE_JSON)
                ->setBody(Mage::helper('core')->jsonEncode([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ]));
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

    private function _getPlacement(): Mage_Paypal_Model_Checkout_Placement
    {
        return Mage::getSingleton('paypal/checkout_placement');
    }

    private function _getSuccessUrl(): string
    {
        return Mage::getUrl('checkout/onepage/success', ['_secure' => true]);
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
        $quote      = $this->_getQuote();
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
        $customer->setPasswordCreatedAt(Carbon::now()->getTimestamp());
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
        $this->_customerSession ??= Mage::getSingleton('customer/session');

        return $this->_customerSession;
    }

    /**
     * Checks if a customer with the given email address already exists for the current website.
     *
     * @param string $email the customer email to check
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
            return true;
        }

        return $result;
    }

    /**
     * Looks up a customer ID by the email address stored in the quote.
     *
     * @return null|int the customer ID if found, otherwise null
     */
    protected function _lookupCustomerId(): ?int
    {
        return Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getWebsite()->getId())
            ->loadByEmail($this->_getQuote()->getCustomerEmail())
            ->getId();
    }

    /**
     * Ensures every required checkout agreement was accepted.
     *
     * The PayPal button bypasses the normal review.save() flow, so the
     * agreement enforcement that flow performs must be repeated here.
     *
     * @throws Mage_Core_Exception when a required agreement is missing
     */
    private function _validateCheckoutAgreements(): void
    {
        $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
        if ($requiredAgreements === null || $requiredAgreements === []) {
            return;
        }

        $postedAgreements = array_keys($this->getRequest()->getPost('agreement', []));
        if (array_diff($requiredAgreements, $postedAgreements) !== []) {
            Mage::throwException(
                Mage::helper('paypal')->__('Please agree to all the terms and conditions before placing the order.'),
            );
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
        $customer = $this->_getQuote()->getCustomer();
        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation', '', $this->_getQuote()->getStoreId());
            $url = Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail());
            $this->_getCustomerSession()->addSuccess(
                Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', $url),
            );
        } else {
            $customer->sendNewAccountEmail('registered', '', $this->_getQuote()->getStoreId());
            $this->_getCustomerSession()->loginById($customer->getId());
        }

        return $this;
    }
}
