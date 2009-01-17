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
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * PayPal Express Checkout Module
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Express extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'paypal_express';
    protected $_formBlockType = 'paypal/express_form';
    protected $_infoBlockType = 'paypal/express_info';

    /**
     * Availability options
     */
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    //Sometime we need this. Reffer to isInitilizeNeeded() method
    protected $_isInitializeNeeded      = true;

    /**
     * Get Paypal API Model
     *
     * @return Mage_Paypal_Model_Api_Nvp
     */
    public function getApi()
    {
        return Mage::getSingleton('paypal/api_nvp');
    }

    /**
     * Get paypal session namespace
     *
     * @return Mage_Paypal_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('paypal/session');
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function getRedirectUrl()
    {
        return $this->getApi()->getRedirectUrl();
    }

    public function getCountryRegionId()
    {
        $a = $this->getApi()->getShippingAddress();
        return $this;
    }

    /**
     * Using internal pages for input payment data
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return false;
    }

    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return false;
    }

    public function catchError()
    {
        if ($this->getApi()->getError()) {
            $s = $this->getCheckout();
            $e = $this->getApi()->getError();
            switch ($e['type']) {
                case 'CURL':
                    $s->addError(Mage::helper('paypal')->__('There was an error connecting to the Paypal server: %s', $e['message']));
                    break;

                case 'API':
                    $s->addError(Mage::helper('paypal')->__('There was an error during communication with Paypal: %s - %s', $e['short_message'], $e['long_message']));
                    break;
            }
        }
        return $this;
    }
    /**
     * Works same as catchError method but instead of saving
     * error message in session throws exception
     *
     * @return Mage_Paypal_Model_Express
     */
    public function throwError()
    {
        if ($this->getApi()->getError()) {
            $s = $this->getCheckout();
            $e = $this->getApi()->getError();
            switch ($e['type']) {
                case 'CURL':
                    Mage::throwException(Mage::helper('paypal')->__('There was an error connecting to the Paypal server: %s', $e['message']));
                    break;

                case 'API':
                    Mage::throwException(Mage::helper('paypal')->__('There was an error during communication with Paypal: %s - %s', $e['short_message'], $e['long_message']));
                    break;
            }
        }
        return $this;
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('paypal/express_form', $name)
            ->setMethod('paypal_express')
            ->setPayment($this->getPayment())
            ->setTemplate('paypal/express/form.phtml');

        return $block;
    }

    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('paypal/express_info', $name)
            ->setPayment($this->getPayment())
            ->setTemplate('paypal/express/info.phtml');
        return $block;
    }

    public function getOrderPlaceRedirectUrl()
    {
        return $this->getRedirectUrl();
    }

    public function getPaymentAction()
    {
        $paymentAction = Mage::getStoreConfig('payment/paypal_express/payment_action');
        if (!$paymentAction) {
            $paymentAction = Mage_Paypal_Model_Api_Nvp::PAYMENT_TYPE_AUTH;
        }
        return $paymentAction;
    }

    public function shortcutSetExpressCheckout()
    {
        $this->getQuote()->reserveOrderId()->save();
        $this->getApi()
            ->setPaymentType($this->getPaymentAction())
            ->setAmount($this->getQuote()->getBaseGrandTotal())
            ->setCurrencyCode($this->getQuote()->getBaseCurrencyCode())
            ->setInvNum($this->getQuote()->getReservedOrderId())
            ->callSetExpressCheckout();

        $this->catchError();

        $this->getSession()->setExpressCheckoutMethod('shortcut');

        return $this;
    }

    public function returnFromPaypal()
    {
        $error = '';

        try {
            $this->_getExpressCheckoutDetails();
        } catch (Exception $e) {
            $error=$e->getMessage();
            $this->getSession()->addError($e->getMessage());
            $this->getApi()->setRedirectUrl('paypal/express/review');
        }
        switch ($this->getApi()->getUserAction()) {
            case Mage_Paypal_Model_Api_Nvp::USER_ACTION_CONTINUE:
                $this->getApi()->setRedirectUrl(Mage::getUrl('paypal/express/review'));
                break;

            case Mage_Paypal_Model_Api_Nvp::USER_ACTION_COMMIT:
                if ($this->getSession()->getExpressCheckoutMethod() == 'shortcut') {
                    $this->getApi()->setRedirectUrl(Mage::getUrl('paypal/express/saveOrder'));
                } else {
                    $this->getApi()->setRedirectUrl(Mage::getUrl('paypal/express/updateOrder'));
                }
                break;
        }
        return $this;
    }

    protected function _getExpressCheckoutDetails()
    {
        $api = $this->getApi();
        if (!$api->callGetExpressCheckoutDetails()) {
            Mage::throwException(Mage::helper('paypal')->__('Problem during communication with PayPal'));
        }
        $q = $this->getQuote();
        $a = $api->getShippingAddress();

        $a->setCountryId(
            Mage::getModel('directory/country')->loadByCode($a->getCountry())->getId()
        );
        $a->setRegionId(
            Mage::getModel('directory/region')->loadByCode($a->getRegion(), $a->getCountryId())->getId()
        );

        /*
        we want to set the billing information
        only if the customer checkout from shortcut(shopping cart) or
        if the customer checkout from mark(one page) and guest
        */

        if ($this->getSession()->getExpressCheckoutMethod()=='shortcut'
        || ($this->getSession()->getExpressCheckoutMethod()!='shortcut' && $q->getCheckoutMethod()!='register')){
            $q->getBillingAddress()
                ->setPrefix($a->getPrefix())
                ->setFirstname($a->getFirstname())
                ->setMiddlename($a->getMiddlename())
                ->setLastname($a->getLastname())
                ->setSuffix($a->getSuffix())
                ->setEmail($a->getEmail());
        }

        $q->getShippingAddress()
            ->importCustomerAddress($a)
            ->setCollectShippingRates(true);

        //$q->setCheckoutMethod('paypal_express');

        $q->getPayment()
            ->setMethod('paypal_express')
            ->setPaypalCorrelationId($api->getCorrelationId())
            ->setPaypalPayerId($api->getPayerId())
            ->setPaypalPayerStatus($api->getPayerStatus())
            ->setAdditionalData($api->getPaypalPayerEmail())
        ;

        $q->collectTotals()->save();

    }

    /**
     * Authorize
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        $this->placeOrder($payment);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if ($payment->getCcTransId()) {
            $api = $this->getApi()
                ->setPaymentType(Mage_Paypal_Model_Api_Nvp::PAYMENT_TYPE_SALE)
                ->setAmount($amount)
                ->setBillingAddress($payment->getOrder()->getBillingAddress())
                ->setPayment($payment);

            $api->setAuthorizationId($payment->getCcTransId())
                ->setCompleteType('NotComplete');
            $result = $api->callDoCapture()!==false;

            if ($result) {
                $payment->setStatus('APPROVED');
                //$payment->setCcTransId($api->getTransactionId());
                $payment->setLastTransId($api->getTransactionId());
            } else {
                $e = $api->getError();
                if (isset($e['short_message'])) {
                    $message = $e['short_message'];
                } else {
                    $message = Mage::helper('paypal')->__("Unknown PayPal API error: %s", $e['code']);
                }
                if (isset($e['long_message'])) {
                    $message .= ': '.$e['long_message'];
                }
                Mage::throwException($message);
            }
        } else {
            $this->placeOrder($payment);
        }
        return $this;
    }

    public function placeOrder(Varien_Object $payment)
    {
        $api = $this->getApi();

        $api->setAmount($payment->getOrder()->getBaseGrandTotal())
            ->setCurrencyCode($payment->getOrder()->getBaseCurrencyCode())
            ->setInvNum($this->getQuote()->getReservedOrderId());

        if ($api->callDoExpressCheckoutPayment()!==false) {
            $payment->setStatus('APPROVED')
                ->setPayerId($api->getPayerId());
            if ($this->getPaymentAction()== Mage_Paypal_Model_Api_Nvp::PAYMENT_TYPE_AUTH) {
                $payment->setCcTransId($api->getTransactionId());
            } else {
                $payment->setLastTransId($api->getTransactionId());
            }
        } else {
            $e = $api->getError();
            die($e['short_message'].': '.$e['long_message']);
        }
        return $this;
    }

    /**
     * void
     *
     * @access public
     * @param string $payment Varien_Object object
     * @return Mage_Payment_Model_Abstract
     */
    public function void(Varien_Object $payment)
    {
        $error = false;
        if($payment->getVoidTransactionId()){
            $api = $this->getApi();
            $api->setAuthorizationId($payment->getVoidTransactionId());

             if ($api->callDoVoid()!==false){
                 $payment->setStatus('SUCCESS')
                    ->setCcTransId($api->getTransactionId());
             }else{
               $e = $api->getError();
               $error = $e['short_message'].': '.$e['long_message'];
             }
        }else{
            $error = Mage::helper('paypal')->__('Invalid transaction id');
        }
        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    /**
     * refund the amount with transaction id
     *
     * @access public
     * @param string $payment Varien_Object object
     * @return Mage_Payment_Model_Abstract
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $error = false;
        if ($payment->getRefundTransactionId() && $amount>0) {
            $api = $this->getApi();
            //we can refund the amount full or partial so it is good to set up as partial refund
            $api->setTransactionId($payment->getRefundTransactionId())
                ->setRefundType(Mage_Paypal_Model_Api_Nvp::REFUND_TYPE_PARTIAL)
                ->setAmount($amount);

            if ($api->callRefundTransaction()!==false){
                $payment->setStatus('SUCCESS')
                    ->setCcTransId($api->getTransactionId());
            } else {
                $e = $api->getError();
                $error = $e['short_message'].': '.$e['long_message'];
            }
        }else{
            $error = Mage::helper('paypal')->__('Error in refunding the payment');
        }

        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    /**
     * initialize payment transaction in case
     * we doing checkout through onepage checkout
     */
    public function initialize($paymentAction, $stateObject)
    {
        if ($this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getBillingAddress();
        } else {
            $address = $this->getQuote()->getShippingAddress();
        }

        $this->getApi()
            ->setPaymentType($paymentAction)
            ->setAmount($address->getBaseGrandTotal())
            ->setCurrencyCode($this->getQuote()->getBaseCurrencyCode())
            ->setShippingAddress($address)
            ->setInvNum($this->getQuote()->getReservedOrderId())
            ->callSetExpressCheckout();

        $this->throwError();

        $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus('pending_paypal');
        $stateObject->setIsNotified(false);

        Mage::getSingleton('paypal/session')->unsExpressCheckoutMethod();

        return $this;
    }

    /**
     * Rewrite standard logic
     *
     * @return bool
     */
    public function isInitializeNeeded()
    {
        return is_object(Mage::registry('_singleton/checkout/type_onepage'));
    }
}
