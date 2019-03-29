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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    protected $_quote = null;

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
        $this->_customerEmailExistsMessage = Mage::helper('checkout')->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.');
        $this->_checkoutSession = Mage::getSingleton('checkout/session');
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
        if ($this->_quote === null) {
            return $this->_checkoutSession->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Declare checkout quote instance
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
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

        $quoteSave = false;
        $collectTotals = false;

        /**
         * Reset multishipping flag before any manipulations with quote address
         * addAddress method for quote object related on this flag
         */
        if ($this->getQuote()->getIsMultiShipping()) {
            $this->getQuote()->setIsMultiShipping(false);
            $quoteSave = true;
        }

        /**
         *  Reset customer balance
         */
        if ($this->getQuote()->getUseCustomerBalance()) {
            $this->getQuote()->setUseCustomerBalance(false);
            $quoteSave = true;
            $collectTotals = true;
        }
        /**
         *  Reset reward points
         */
        if ($this->getQuote()->getUseRewardPoints()) {
            $this->getQuote()->setUseRewardPoints(false);
            $quoteSave = true;
            $collectTotals = true;
        }

        if ($collectTotals) {
            $this->getQuote()->collectTotals();
        }

        if ($quoteSave) {
            $this->getQuote()->save();
        }

        /*
        * want to load the correct customer information by assigning to address
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
     * Specify checkout method
     *
     * @param   string $method
     * @return  array
     */
    public function saveCheckoutMethod($method)
    {
        if (empty($method)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
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
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }

        $address = $this->getQuote()->getBillingAddress();
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntityType('customer_address')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => Mage::helper('checkout')->__('Customer Address is not valid.')
                    );
                }

                $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
                $addressForm->setEntity($address);
                $addressErrors  = $addressForm->validateData($address->getData());
                if ($addressErrors !== true) {
                    return array('error' => 1, 'message' => $addressErrors);
                }
            }
        } else {
            $addressForm->setEntity($address);
            // emulate request object
            $addressData    = $addressForm->extractData($addressForm->prepareRequest($data));
            $addressErrors  = $addressForm->validateData($addressData);
            if ($addressErrors !== true) {
                return array('error' => 1, 'message' => array_values($addressErrors));
            }
            $addressForm->compactData($addressData);
            //unset billing address attributes which were not shown in form
            foreach ($addressForm->getAttributes() as $attribute) {
                if (!isset($data[$attribute->getAttributeCode()])) {
                    $address->setData($attribute->getAttributeCode(), NULL);
                }
            }
            $address->setCustomerAddressId(null);
            // Additional form data, not fetched by extractData (as it fetches only attributes)
            $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
        }

        // set email for newly created user
        if (!$address->getEmail() && $this->getQuote()->getCustomerEmail()) {
            $address->setEmail($this->getQuote()->getCustomerEmail());
        }

        // validate billing address
        if (($validateRes = $address->validate()) !== true) {
            return array('error' => 1, 'message' => $validateRes);
        }

        $address->implodeStreetAddress();

        if (true !== ($result = $this->_validateCustomerData($data))) {
            return $result;
        }

        if (!$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
            if ($this->_customerEmailExists($address->getEmail(), Mage::app()->getWebsite()->getId())) {
                return array('error' => 1, 'message' => $this->_customerEmailExistsMessage);
            }
        }

        if (!$this->getQuote()->isVirtual()) {
            /**
             * Billing address using otions
             */
            $usingCase = isset($data['use_for_shipping']) ? (int)$data['use_for_shipping'] : 0;

            switch ($usingCase) {
                case 0:
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shipping->setSameAsBilling(0);
                    break;
                case 1:
                    $billing = clone $address;
                    $billing->unsAddressId()->unsAddressType();
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shippingMethod = $shipping->getShippingMethod();

                    // Billing address properties that must be always copied to shipping address
                    $requiredBillingAttributes = array('customer_address_id');

                    // don't reset original shipping data, if it was not changed by customer
                    foreach ($shipping->getData() as $shippingKey => $shippingValue) {
                        if (!is_null($shippingValue) && !is_null($billing->getData($shippingKey))
                            && !isset($data[$shippingKey]) && !in_array($shippingKey, $requiredBillingAttributes)
                        ) {
                            $billing->unsetData($shippingKey);
                        }
                    }
                    $shipping->addData($billing->getData())
                        ->setSameAsBilling(1)
                        ->setSaveInAddressBook(0)
                        ->setShippingMethod($shippingMethod)
                        ->setCollectShippingRates(true);
                    $this->getCheckout()->setStepData('shipping', 'complete', true);
                    $this->_setCartCouponCode();
                    break;
            }
        }

        $this->getQuote()->collectTotals();
        $this->getQuote()->save();

        if (!$this->getQuote()->isVirtual() && $this->getCheckout()->getStepData('shipping', 'complete') == true) {
            //Recollect Shipping rates for shipping methods
            $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        }

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
     * @param array $data
     * @return true|array
     */
    protected function _validateCustomerData(array $data)
    {
        /** @var $customerForm Mage_Customer_Model_Form */
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setFormCode('checkout_register')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        $quote = $this->getQuote();
        if ($quote->getCustomerId()) {
            $customer = $quote->getCustomer();
            $customerForm->setEntity($customer);
            $customerData = $quote->getCustomer()->getData();
        } else {
            /* @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer');
            $customerForm->setEntity($customer);
            $customerRequest = $customerForm->prepareRequest($data);
            $customerData = $customerForm->extractData($customerRequest);
        }

        $customerErrors = $customerForm->validateData($customerData);
        if ($customerErrors !== true) {
            return array(
                'error'     => -1,
                'message'   => implode(', ', $customerErrors)
            );
        }

        if ($quote->getCustomerId()) {
            return true;
        }

        $customerForm->compactData($customerData);

        if ($quote->getCheckoutMethod() == self::METHOD_REGISTER) {
            // set customer password
            $customer->setPassword($customerRequest->getParam('customer_password'));
            $customer->setPasswordConfirmation($customerRequest->getParam('confirm_password'));
        } else {
            // spoof customer password for guest
            $password = $customer->generatePassword();
            $customer->setPassword($password);
            $customer->setPasswordConfirmation($password);
            // set NOT LOGGED IN group id explicitly,
            // otherwise copyFieldset('customer_account', 'to_quote') will fill it with default group id value
            $customer->setGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        }

        $result = $customer->validate();
        if (true !== $result && is_array($result)) {
            return array(
                'error'   => -1,
                'message' => implode(', ', $result)
            );
        }

        if ($quote->getCheckoutMethod() == self::METHOD_REGISTER) {
            // save customer encrypted password in quote
            $quote->setPasswordHash($customer->encryptPassword($customer->getPassword()));
        }

        // copy customer/guest email to address
        $quote->getBillingAddress()->setEmail($customer->getEmail());

        // copy customer data to quote
        Mage::helper('core')->copyFieldset('customer_account', 'to_quote', $customer, $quote);

        return true;
    }

    /**
     * Validate customer data and set some its data for further usage in quote
     * Will return either true or array with error messages
     *
     * @deprecated since 1.4.0.1
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
        } else if (self::METHOD_GUEST == $this->getQuote()->getCheckoutMethod()) {
            $email = $address->getData('email');
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                return array(
                    'error'   => -1,
                    'message' => Mage::helper('checkout')->__('Invalid email address "%s"', $email)
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
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }
        $address = $this->getQuote()->getShippingAddress();

        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm    = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntityType('customer_address')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => Mage::helper('checkout')->__('Customer Address is not valid.')
                    );
                }

                $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
                $addressForm->setEntity($address);
                $addressErrors  = $addressForm->validateData($address->getData());
                if ($addressErrors !== true) {
                    return array('error' => 1, 'message' => $addressErrors);
                }
            }
        } else {
            $addressForm->setEntity($address);
            // emulate request object
            $addressData    = $addressForm->extractData($addressForm->prepareRequest($data));
            $addressErrors  = $addressForm->validateData($addressData);
            if ($addressErrors !== true) {
                return array('error' => 1, 'message' => $addressErrors);
            }
            $addressForm->compactData($addressData);
            // unset shipping address attributes which were not shown in form
            foreach ($addressForm->getAttributes() as $attribute) {
                if (!isset($data[$attribute->getAttributeCode()])) {
                    $address->setData($attribute->getAttributeCode(), NULL);
                }
            }

            $address->setCustomerAddressId(null);
            // Additional form data, not fetched by extractData (as it fetches only attributes)
            $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
            $address->setSameAsBilling(empty($data['same_as_billing']) ? 0 : 1);
        }

        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);

        if (($validateRes = $address->validate())!==true) {
            return array('error' => 1, 'message' => $validateRes);
        }

        $this->_setCartCouponCode();

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
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
        }
        $rate = $this->getQuote()->getShippingAddress()->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
        }
        $this->getQuote()->getShippingAddress()
            ->setShippingMethod($shippingMethod);

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
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }
        $quote = $this->getQuote();
        if ($quote->isVirtual()) {
            $quote->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $quote->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        // shipping totals may be affected by payment method
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
            $quote->getShippingAddress()->setCollectShippingRates(true);
        }

        $data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
            | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
            | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
            | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
            | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;

        $payment = $quote->getPayment();
        $payment->importData($data);

        $quote->save();

        $this->getCheckout()
            ->setStepData('payment', 'complete', true)
            ->setStepData('review', 'allow', true);

        return array();
    }

    /**
     * Validate quote state to be integrated with one page checkout process
     */
    public function validate()
    {
        $quote  = $this->getQuote();
        if ($quote->getIsMultiShipping()) {
            Mage::throwException(Mage::helper('checkout')->__('Invalid checkout type.'));
        }

        if ($quote->getCheckoutMethod() == self::METHOD_GUEST && !$quote->isAllowedGuestCheckout()) {
            Mage::throwException(Mage::helper('checkout')->__('Sorry, guest checkout is not enabled. Please try again or contact store owner.'));
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
        } else {
            $customerBilling->setIsDefaultShipping(true);
        }

        Mage::helper('core')->copyFieldset('checkout_onepage_quote', 'to_customer', $quote, $customer);
        $customer->setPassword($customer->decryptPassword($quote->getPasswordHash()));
        $passwordCreatedTime = $this->_checkoutSession->getData('_session_validator_data')['session_expire_timestamp']
            - Mage::getSingleton('core/cookie')->getLifetime();
        $customer->setPasswordCreatedAt($passwordCreatedTime);
        $quote->setCustomer($customer)
            ->setCustomerId(true);
        $quote->setPasswordHash('');
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
        if ($shipping && !$shipping->getSameAsBilling() &&
            (!$shipping->getCustomerId() || $shipping->getSaveInAddressBook())) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
        }

        if (isset($customerBilling) && !$customer->getDefaultBilling()) {
            $customerBilling->setIsDefaultBilling(true);
        }
        if ($shipping && isset($customerShipping) && !$customer->getDefaultShipping()) {
            $customerShipping->setIsDefaultShipping(true);
        } else if (isset($customerBilling) && !$customer->getDefaultShipping()) {
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
            $customer->sendNewAccountEmail('confirmation', '', $this->getQuote()->getStoreId());
            $url = Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail());
            $this->getCustomerSession()->addSuccess(
                Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', $url)
            );
        } else {
            $customer->sendNewAccountEmail('registered', '', $this->getQuote()->getStoreId());
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
            ->clearHelperData();

        $order = $service->getOrder();
        if ($order) {
            Mage::dispatchEvent('checkout_type_onepage_save_order_after',
                array('order'=>$order, 'quote'=>$this->getQuote()));

            /**
             * a flag to set that there will be redirect to third party after confirmation
             * eg: paypal standard ipn
             */
            $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
            /**
             * we only want to send to customer about new order when there is no redirect to third party
             */
            if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
                try {
                    $order->queueNewOrderEmail();
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
            foreach ($profiles as $profile) {
                $ids[] = $profile->getId();
            }
            $this->_checkoutSession->setLastRecurringProfileIds($ids);
            // TODO: send recurring profile emails
        }

        Mage::dispatchEvent(
            'checkout_submit_all_after',
            array('order' => $order, 'quote' => $this->getQuote(), 'recurring_profiles' => $profiles)
        );

        return $this;
    }

    /**
     * Validate quote state to be able submitted from one page checkout page
     *
     * @deprecated after 1.4 - service model doing quote validation
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function validateOrder()
    {
        if ($this->getQuote()->getIsMultiShipping()) {
            Mage::throwException(Mage::helper('checkout')->__('Invalid checkout type.'));
        }

        if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
            $addressValidation = $address->validate();
            if ($addressValidation !== true) {
                Mage::throwException(Mage::helper('checkout')->__('Please check shipping address information.'));
            }
            $method= $address->getShippingMethod();
            $rate  = $address->getShippingRateByCode($method);
            if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                Mage::throwException(Mage::helper('checkout')->__('Please specify shipping method.'));
            }
        }

        $addressValidation = $this->getQuote()->getBillingAddress()->validate();
        if ($addressValidation !== true) {
            Mage::throwException(Mage::helper('checkout')->__('Please check billing address information.'));
        }

        if (!($this->getQuote()->getPayment()->getMethod())) {
            Mage::throwException(Mage::helper('checkout')->__('Please select valid payment method.'));
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
     * Sets cart coupon code from checkout to quote
     *
     * @return $this
     */
    protected function _setCartCouponCode()
    {
        if ($couponCode = $this->getCheckout()->getCartCouponCode()) {
            $this->getQuote()->setCouponCode($couponCode);
        }
        return $this;
    }
}
