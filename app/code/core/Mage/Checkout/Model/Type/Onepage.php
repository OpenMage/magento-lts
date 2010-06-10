<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout processing model
 */
class Mage_Checkout_Model_Type_Onepage
{
    /**
     * Checkout types: Checkout as Guest, Register, Logged In Customer
     */
    const METHOD_GUEST    = 'guest';
    const METHOD_REGISTER = 'register';
    const METHOD_CUSTOMER = 'customer';

    /**
     * Error message of "customer already exists"
     *
     * @var string
     */
    private $_customerEmailExistsMessage = '';

    /**
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Mage_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * @var Mage_Checkout_Helper_Data
     */
    protected $_helper;

    /**
     * Class constructor
     * Set customer already exists message
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('checkout');
        $this->_customerEmailExistsMessage = $this->_helper->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.');
        $this->_checkoutSession = Mage::getSingleton('checkout/session');
        $this->_quote = $this->_checkoutSession->getQuote();
        $this->_customerSession = Mage::getSingleton('customer/session');
    }

    /**
     * Get frontend checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return $this->_checkoutSession;
    }

    /**
     * Quote object getter
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Get customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

    /**
     * Initialize quote state to be valid for one page checkout
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function initCheckout()
    {
        $checkout = $this->getCheckout();
        $customerSession = $this->getCustomerSession();
        if (is_array($checkout->getStepData())) {
            foreach ($checkout->getStepData() as $step=>$data) {
                if (!($step==='login' || $customerSession->isLoggedIn() && $step==='billing')) {
                    $checkout->setStepData($step, 'allow', false);
                }
            }
        }

        /**
         * Reset multishipping flag before any manipulations with quote address
         * addAddress method for quote object related on this flag
         */
        if ($this->getQuote()->getIsMultiShipping()) {
            $this->getQuote()->setIsMultiShipping(false);
            $this->getQuote()->save();
        }

        /*
        * want to laod the correct customer information by assiging to address
        * instead of just loading from sales/quote_address
        */
        $customer = $customerSession->getCustomer();
        if ($customer) {
            $this->getQuote()->assignCustomer($customer);
        }
        return $this;
    }

    /**
     * Get quote checkout method
     *
     * @return string
     */
    public function getCheckoutMethod()
    {
        if ($this->getCustomerSession()->isLoggedIn()) {
            return self::METHOD_CUSTOMER;
        }
        if (!$this->getQuote()->getCheckoutMethod()) {
            if ($this->_helper->isAllowedGuestCheckout($this->getQuote())) {
                $this->getQuote()->setCheckoutMethod(self::METHOD_GUEST);
            } else {
                $this->getQuote()->setCheckoutMethod(self::METHOD_REGISTER);
            }
        }
        return $this->getQuote()->getCheckoutMethod();
    }

    /**
     * Get quote checkout method
     *
     * @deprecated since 1.4.0.1
     * @return string
     */
    public function getCheckoutMehod()
    {
        return $this->getCheckoutMethod();
    }

    /**
     * Specify chceckout method
     *
     * @param   string $method
     * @return  array
     */
    public function saveCheckoutMethod($method)
    {
        if (empty($method)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }

        $this->getQuote()->setCheckoutMethod($method)->save();
        $this->getCheckout()->setStepData('billing', 'allow', true);
        return array();
    }

    /**
     * Get customer address by identifier
     *
     * @param   int $addressId
     * @return  Mage_Customer_Model_Address
     */
    public function getAddress($addressId)
    {
        $address = Mage::getModel('customer/address')->load((int)$addressId);
        $address->explodeStreetAddress();
        if ($address->getRegionId()) {
            $address->setRegion($address->getRegionId());
        }
        return $address;
    }

    /**
     * Save billing address information to quote
     * This method is called by One Page Checkout JS (AJAX) while saving the billing information.
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Mage_Checkout_Model_Type_Onepage
     */
    public function saveBilling($data, $customerAddressId)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }

        $address = $this->getQuote()->getBillingAddress();
        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => $this->_helper->__('Customer Address is not valid.')
                    );
                }
                $address->importCustomerAddress($customerAddress);
            }
        } else {
            unset($data['address_id']);
            $address->addData($data);
            //$address->setId(null);
        }

        if (($validateRes = $address->validate())!==true) {
            return array('error' => 1, 'message' => $validateRes);
        }

        if (!$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
            if ($this->_customerEmailExists($address->getEmail(), Mage::app()->getWebsite()->getId())) {
                return array('error' => 1, 'message' => $this->_customerEmailExistsMessage);
            }
        }

        $address->implodeStreetAddress();

        if (!$this->getQuote()->isVirtual()) {
            /**
             * Billing address using otions
             */
            $usingCase = isset($data['use_for_shipping']) ? (int) $data['use_for_shipping'] : 0;

            switch($usingCase) {
                case 0:
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shipping->setSameAsBilling(0);
                    break;
                case 1:
                    $billing = clone $address;
                    $billing->unsAddressId()->unsAddressType();
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shippingMethod = $shipping->getShippingMethod();
                    $shipping->addData($billing->getData())
                        ->setSameAsBilling(1)
                        ->setShippingMethod($shippingMethod)
                        ->setCollectShippingRates(true);
                    $this->getCheckout()->setStepData('shipping', 'complete', true);
                    break;
            }
        }

        if (true !== $result = $this->_processValidateCustomer($address)) {
            return $result;
        }

        $this->getQuote()->collectTotals();
        $this->getQuote()->save();

        $this->getCheckout()
            ->setStepData('billing', 'allow', true)
            ->setStepData('billing', 'complete', true)
            ->setStepData('shipping', 'allow', true);

        return array();
    }

    /**
     * Validate customer data and set some its data for further usage in quote
     * Will return either true or array with error messages
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return true|array
     */
    protected function _processValidateCustomer(Mage_Sales_Model_Quote_Address $address)
    {
        // set customer date of birth for further usage
        $dob = '';
        if ($address->getDob()) {
            $dob = Mage::app()->getLocale()->date($address->getDob(), null, null, false)->toString('yyyy-MM-dd');
            $this->getQuote()->setCustomerDob($dob);
        }

        // set customer tax/vat number for further usage
        if ($address->getTaxvat()) {
            $this->getQuote()->setCustomerTaxvat($address->getTaxvat());
        }

        // set customer gender for further usage
        if ($address->getGender()) {
            $this->getQuote()->setCustomerGender($address->getGender());
        }

        // invoke customer model, if it is registering
        if (self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
            // set customer password hash for further usage
            $customer = Mage::getModel('customer/customer');
            $this->getQuote()->setPasswordHash($customer->encryptPassword($address->getCustomerPassword()));

            // validate customer
            foreach (array(
                'firstname'    => 'firstname',
                'lastname'     => 'lastname',
                'email'        => 'email',
                'password'     => 'customer_password',
                'confirmation' => 'confirm_password',
                'taxvat'       => 'taxvat',
                'gender'       => 'gender',
            ) as $key => $dataKey) {
                $customer->setData($key, $address->getData($dataKey));
            }
            if ($dob) {
                $customer->setDob($dob);
            }
            $validationResult = $customer->validate();
            if (true !== $validationResult && is_array($validationResult)) {
                return array(
                    'error'   => -1,
                    'message' => implode(', ', $validationResult)
                );
            }
        } elseif(self::METHOD_GUEST == $this->getQuote()->getCheckoutMethod()) {
            $email = $address->getData('email');
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                return array(
                    'error'   => -1,
                    'message' => $this->_helper->__('Invalid email address "%s"', $email)
                );
            }
        }

        return true;
    }

    /**
     * Save checkout shipping address
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Mage_Checkout_Model_Type_Onepage
     */
    public function saveShipping($data, $customerAddressId)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }
        $address = $this->getQuote()->getShippingAddress();

        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => $this->_helper->__('Customer Address is not valid.')
                    );
                }
                $address->importCustomerAddress($customerAddress);
            }
        } else {
            unset($data['address_id']);
            $address->addData($data);
        }
        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);

        if (($validateRes = $address->validate())!==true) {
            return array('error' => 1, 'message' => $validateRes);
        }

        $this->getQuote()->collectTotals()->save();

        $this->getCheckout()
            ->setStepData('shipping', 'complete', true)
            ->setStepData('shipping_method', 'allow', true);

        return array();
    }

    /**
     * Specify quote shipping method
     *
     * @param   string $shippingMethod
     * @return  array
     */
    public function saveShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid shipping method.'));
        }
        $rate = $this->getQuote()->getShippingAddress()->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid shipping method.'));
        }
        $this->getQuote()->getShippingAddress()
            ->setShippingMethod($shippingMethod);
        $this->getQuote()->collectTotals()
            ->save();

        $this->getCheckout()
            ->setStepData('shipping_method', 'complete', true)
            ->setStepData('payment', 'allow', true);

        return array();
    }

    /**
     * Specify quote payment method
     *
     * @param   array $data
     * @return  array
     */
    public function savePayment($data)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }
        if ($this->getQuote()->isVirtual()) {
            $this->getQuote()->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $this->getQuote()->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        $payment = $this->getQuote()->getPayment();
        $payment->importData($data);

        $this->getQuote()->save();

        $this->getCheckout()
            ->setStepData('payment', 'complete', true)
            ->setStepData('review', 'allow', true);

        return array();
    }

    /**
     * Validate quote state to be integrated with obe page checkout process
     */
    public function validate()
    {
        $helper = Mage::helper('checkout');
        $quote  = $this->getQuote();
        if ($quote->getIsMultiShipping()) {
            Mage::throwException($helper->__('Invalid checkout type.'));
        }

        if ($quote->getCheckoutMethod() == self::METHOD_GUEST && !$quote->isAllowedGuestCheckout()) {
            Mage::throwException($this->_helper->__('Sorry, guest checkout is not enabled. Please try again or contact store owner.'));
        }
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _prepareGuestQuote()
    {
        $quote = $this->getQuote();
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        return $this;
    }

    /**
     * Prepare quote for customer registration and customer order submit
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _prepareNewCustomerQuote()
    {
        $quote      = $this->getQuote();
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        //$customer = Mage::getModel('customer/customer');
        $customer = $quote->getCustomer();
        /* @var $customer Mage_Customer_Model_Customer */
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
        $customer->setPassword($customer->decryptPassword($quote->getPasswordHash()));
        $customer->setPasswordHash($customer->hashPassword($customer->getPassword()));
        $quote->setCustomer($customer)
            ->setCustomerId(true);
    }

    /**
     * Prepare quote for customer order submit
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _prepareCustomerQuote()
    {
        $quote      = $this->getQuote();
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customer = $this->getCustomerSession()->getCustomer();
        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            $customerBilling = $billing->exportCustomerAddress();
            $customer->addAddress($customerBilling);
            $billing->setCustomerAddress($customerBilling);
        }
        if ($shipping && ((!$shipping->getCustomerId() && !$shipping->getSameAsBilling())
            || (!$shipping->getSameAsBilling() && $shipping->getSaveInAddressBook()))) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
        }

        if (isset($customerBilling) && !$customer->getDefaultBilling()) {
            $customerBilling->setIsDefaultBilling(true);
        }
        if ($shipping && isset($customerShipping) && !$customer->getDefaultShipping()) {
            $customerShipping->setIsDefaultShipping(true);
        } elseif (isset($customerBilling) && !$customer->getDefaultShipping()) {
            $customerBilling->setIsDefaultShipping(true);
        }
        $quote->setCustomer($customer);
    }

    /**
     * Involve new customer to system
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _involveNewCustomer()
    {
        $customer = $this->getQuote()->getCustomer();
        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation');
            $url = Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail());
            $this->getCustomerSession()->addSuccess(
                Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', $url)
            );
        } else {
            $customer->sendNewAccountEmail();
            $this->getCustomerSession()->loginById($customer->getId());
        }
        return $this;
    }

    /**
     * Create order based on checkout type. Create customer if necessary.
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function saveOrder()
    {
        $this->validate();
        $isNewCustomer = false;
        switch ($this->getCheckoutMethod()) {
            case self::METHOD_GUEST:
                $this->_prepareGuestQuote();
                break;
            case self::METHOD_REGISTER:
                $this->_prepareNewCustomerQuote();
                $isNewCustomer = true;
                break;
            default:
                $this->_prepareCustomerQuote();
                break;
        }

        $service = Mage::getModel('sales/service_quote', $this->getQuote());
        $service->submitAll();

        if ($isNewCustomer) {
            try {
                $this->_involveNewCustomer();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId())
            ->setLastSuccessQuoteId($this->getQuote()->getId())
            ->clearHelperData()
        ;

        $order = $service->getOrder();
        if ($order) {
            Mage::dispatchEvent('checkout_type_onepage_save_order_after', array('order'=>$order, 'quote'=>$this->getQuote()));

            /**
             * a flag to set that there will be redirect to third party after confirmation
             * eg: paypal standard ipn
             */
            $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
            /**
             * we only want to send to customer about new order when there is no redirect to third party
             */
            if(!$redirectUrl){
                try {
                    $order->sendNewOrderEmail();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            // add order information to the session
            $this->_checkoutSession->setLastOrderId($order->getId())
                ->setRedirectUrl($redirectUrl)
                ->setLastRealOrderId($order->getIncrementId());

            // as well a billing agreement can be created
            $agreement = $order->getPayment()->getBillingAgreement();
            if ($agreement) {
                $this->_checkoutSession->setLastBillingAgreementId($agreement->getId());
            }
        }

        // add recurring profiles information to the session
        $profiles = $service->getRecurringPaymentProfiles();
        if ($profiles) {
            $ids = array();
            foreach($profiles as $profile) {
                $ids[] = $profile->getId();
            }
            $this->_checkoutSession->setLastRecurringProfileIds($ids);
            // TODO: send recurring profile emails
        }

        return $this;
    }

    /**
     * Validate quote state to be able submited from one page checkout page
     *
     * @deprecated after 1.4 - service model doing quote validation
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function validateOrder()
    {
        $helper = Mage::helper('checkout');
        if ($this->getQuote()->getIsMultiShipping()) {
            Mage::throwException($helper->__('Invalid checkout type.'));
        }

        if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
            $addressValidation = $address->validate();
            if ($addressValidation !== true) {
                Mage::throwException($helper->__('Please check shipping address information.'));
            }
            $method= $address->getShippingMethod();
            $rate  = $address->getShippingRateByCode($method);
            if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                Mage::throwException($helper->__('Please specify shipping method.'));
            }
        }

        $addressValidation = $this->getQuote()->getBillingAddress()->validate();
        if ($addressValidation !== true) {
            Mage::throwException($helper->__('Please check billing address information.'));
        }

        if (!($this->getQuote()->getPayment()->getMethod())) {
            Mage::throwException($helper->__('Please select valid payment method.'));
        }
    }

    /**
     * Check if customer email exists
     *
     * @param string $email
     * @param int $websiteId
     * @return false|Mage_Customer_Model_Customer
     */
    protected function _customerEmailExists($email, $websiteId = null)
    {
        $customer = Mage::getModel('customer/customer');
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer;
        }
        return false;
    }

    /**
     * Get last order increment id by order id
     *
     * @return string
     */
    public function getLastOrderId()
    {
        $lastId  = $this->getCheckout()->getLastOrderId();
        $orderId = false;
        if ($lastId) {
            $order = Mage::getModel('sales/order');
            $order->load($lastId);
            $orderId = $order->getIncrementId();
        }
        return $orderId;
    }


    /**
     * Enter description here...
     *
     * @return array
     */
//    public function saveOrder1()
//    {
//        $this->validateOrder();
//        $billing = $this->getQuote()->getBillingAddress();
//        if (!$this->getQuote()->isVirtual()) {
//            $shipping = $this->getQuote()->getShippingAddress();
//        }
//
//        /*
//         * Check if before this step checkout method was not defined use default values.
//         * Related to issue with some browsers when checkout method was not saved during first step.
//         */
//        if (!$this->getQuote()->getCheckoutMethod()) {
//            if ($this->_helper->isAllowedGuestCheckout($this->getQuote(), $this->getQuote()->getStore())) {
//                $this->getQuote()->setCheckoutMethod(Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST);
//            } else {
//                $this->getQuote()->setCheckoutMethod(Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER);
//            }
//        }
//
//        switch ($this->getQuote()->getCheckoutMethod()) {
//        case Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST:
//            if (!$this->getQuote()->isAllowedGuestCheckout()) {
//                Mage::throwException($this->_helper->__('Sorry, guest checkout is not enabled. Please try again or contact the store owner.'));
//            }
//            $this->getQuote()->setCustomerId(null)
//                ->setCustomerEmail($billing->getEmail())
//                ->setCustomerIsGuest(true)
//                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
//            break;
//
//        case Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER:
//            $customer = Mage::getModel('customer/customer');
//            /* @var $customer Mage_Customer_Model_Customer */
//
//            $customerBilling = $billing->exportCustomerAddress();
//            $customer->addAddress($customerBilling);
//
//            if (!$this->getQuote()->isVirtual() && !$shipping->getSameAsBilling()) {
//                $customerShipping = $shipping->exportCustomerAddress();
//                $customer->addAddress($customerShipping);
//            }
//
//            if ($this->getQuote()->getCustomerDob() && !$billing->getCustomerDob()) {
//                $billing->setCustomerDob($this->getQuote()->getCustomerDob());
//            }
//
//            if ($this->getQuote()->getCustomerTaxvat() && !$billing->getCustomerTaxvat()) {
//                $billing->setCustomerTaxvat($this->getQuote()->getCustomerTaxvat());
//            }
//
//            if ($this->getQuote()->getCustomerGender() && !$billing->getCustomerGender()) {
//                $billing->setCustomerGender($this->getQuote()->getCustomerGender());
//            }
//
//            Mage::helper('core')->copyFieldset('checkout_onepage_billing', 'to_customer', $billing, $customer);
//
//            $customer->setPassword($customer->decryptPassword($this->getQuote()->getPasswordHash()));
//            $customer->setPasswordHash($customer->hashPassword($customer->getPassword()));
//
//            $this->getQuote()->setCustomer($customer);
//            break;
//
//        default:
//            $customer = Mage::getSingleton('customer/session')->getCustomer();
//
//            if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
//                $customerBilling = $billing->exportCustomerAddress();
//                $customer->addAddress($customerBilling);
//            }
//            if (!$this->getQuote()->isVirtual() &&
//                ((!$shipping->getCustomerId() && !$shipping->getSameAsBilling()) ||
//                (!$shipping->getSameAsBilling() && $shipping->getSaveInAddressBook()))) {
//
//                $customerShipping = $shipping->exportCustomerAddress();
//                $customer->addAddress($customerShipping);
//            }
//            $customer->setSavedFromQuote(true);
//            $customer->save();
//
//            $changed = false;
//            if (isset($customerBilling) && !$customer->getDefaultBilling()) {
//                $customer->setDefaultBilling($customerBilling->getId());
//                $changed = true;
//            }
//            if (!$this->getQuote()->isVirtual() && isset($customerBilling) && !$customer->getDefaultShipping() && $shipping->getSameAsBilling()) {
//                $customer->setDefaultShipping($customerBilling->getId());
//                $changed = true;
//            }
//            elseif (!$this->getQuote()->isVirtual() && isset($customerShipping) && !$customer->getDefaultShipping()){
//                $customer->setDefaultShipping($customerShipping->getId());
//                $changed = true;
//            }
//
//            if ($changed) {
//                $customer->save();
//            }
//        }
//
//        $this->getQuote()->reserveOrderId();
//        $convertQuote = Mage::getModel('sales/convert_quote');
//        /* @var $convertQuote Mage_Sales_Model_Convert_Quote */
//        //$order = Mage::getModel('sales/order');
//        if ($this->getQuote()->isVirtual()) {
//            $order = $convertQuote->addressToOrder($billing);
//        }
//        else {
//            $order = $convertQuote->addressToOrder($shipping);
//        }
//        /* @var $order Mage_Sales_Model_Order */
//        $order->setBillingAddress($convertQuote->addressToOrderAddress($billing));
//
//        if (!$this->getQuote()->isVirtual()) {
//            $order->setShippingAddress($convertQuote->addressToOrderAddress($shipping));
//        }
//
//        $order->setPayment($convertQuote->paymentToOrderPayment($this->getQuote()->getPayment()));
//
//        foreach ($this->getQuote()->getAllItems() as $item) {
//            $orderItem = $convertQuote->itemToOrderItem($item);
//            if ($item->getParentItem()) {
//                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
//            }
//            $order->addItem($orderItem);
//        }
//
//        /**
//         * We can use configuration data for declare new order status
//         */
//        Mage::dispatchEvent('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$this->getQuote()));
//        // check again, if customer exists
//        if ($this->getQuote()->getCheckoutMethod() == Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER) {
//            if ($this->_customerEmailExists($customer->getEmail(), Mage::app()->getWebsite()->getId())) {
//                Mage::throwException($this->_customerEmailExistsMessage);
//            }
//        }
//        $order->place();
//
//        if ($this->getQuote()->getCheckoutMethod()==Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER) {
//            $customer->save();
//            $customerBillingId = $customerBilling->getId();
//            if (!$this->getQuote()->isVirtual()) {
//                $customerShippingId = isset($customerShipping) ? $customerShipping->getId() : $customerBillingId;
//                $customer->setDefaultShipping($customerShippingId);
//            }
//            $customer->setDefaultBilling($customerBillingId);
//            $customer->save();
//
//            $this->getQuote()->setCustomerId($customer->getId());
//
//            $order->setCustomerId($customer->getId());
//            Mage::helper('core')->copyFieldset('customer_account', 'to_order', $customer, $order);
//
//            $billing->setCustomerId($customer->getId())->setCustomerAddressId($customerBillingId);
//            if (!$this->getQuote()->isVirtual()) {
//                $shipping->setCustomerId($customer->getId())->setCustomerAddressId($customerShippingId);
//            }
//
//            try {
//                if ($customer->isConfirmationRequired()) {
//                    $customer->sendNewAccountEmail('confirmation');
//                }
//                else {
//                    $customer->sendNewAccountEmail();
//                }
//            } catch (Exception $e) {
//                Mage::logException($e);
//            }
//        }
//
//        /**
//         * a flag to set that there will be redirect to third party after confirmation
//         * eg: paypal standard ipn
//         */
//        $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
//        if(!$redirectUrl){
//            $order->setEmailSent(true);
//        }
//
//        $order->save();
//
//        Mage::dispatchEvent('checkout_type_onepage_save_order_after', array('order'=>$order, 'quote'=>$this->getQuote()));
//
//        /**
//         * need to have somelogic to set order as new status to make sure order is not finished yet
//         * quote will be still active when we send the customer to paypal
//         */
//
//        $orderId = $order->getIncrementId();
//        $this->getCheckout()->setLastQuoteId($this->getQuote()->getId());
//        $this->getCheckout()->setLastOrderId($order->getId());
//        $this->getCheckout()->setLastRealOrderId($order->getIncrementId());
//        $this->getCheckout()->setRedirectUrl($redirectUrl);
//
//        /**
//         * we only want to send to customer about new order when there is no redirect to third party
//         */
//        if(!$redirectUrl){
//            try {
//                $order->sendNewOrderEmail();
//            } catch (Exception $e) {
//                Mage::logException($e);
//            }
//        }
//
//        if ($this->getQuote()->getCheckoutMethod(true)==Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER
//            && !Mage::getSingleton('customer/session')->isLoggedIn()) {
//            /**
//             * we need to save quote here to have it saved with Customer Id.
//             * so when loginById() executes checkout/session method loadCustomerQuote
//             * it would not create new quotes and merge it with old one.
//             */
//            $this->getQuote()->save();
//            if ($customer->isConfirmationRequired()) {
//                Mage::getSingleton('checkout/session')->addSuccess(Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
//            }
//            else {
//                Mage::getSingleton('customer/session')->loginById($customer->getId());
//            }
//        }
//
//        //Setting this one more time like control flag that we haves saved order
//        //Must be checkout on success page to show it or not.
//        $this->getCheckout()->setLastSuccessQuoteId($this->getQuote()->getId());
//
//        $this->getQuote()->setIsActive(false);
//        $this->getQuote()->save();
//
//        return $this;
//    }
//
}
