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
 * @package     Mage_PaypalUk
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * PayPal Express Checkout Module
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PaypalUk_Model_Express extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'paypaluk_express';
    protected $_formBlockType = 'paypaluk/express_form';
    protected $_infoBlockType = 'paypaluk/express_info';

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

    protected $_allowCurrencyCode = array('AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MXN', 'NOK', 'NZD', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'USD');

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return boolean
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_allowCurrencyCode)) {
            return false;
        }
        return true;
    }

    /**
     * Get Paypal API Model
     *
     * @return Mage_PaypalUk_Model_Api_Pro
     */
    public function getApi()
    {
        return Mage::getSingleton('paypalUk/api_pro');
    }

    /**
     * Retrieve redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getApi()->getRedirectUrl();
    }

    /**
     * Get paypal session namespace
     *
     * @return Mage_Paypal_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('paypaluk/session');
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

    /**
     * Getting paypal action url
     *
     * @return string
     */
    public function getPaymentAction($paymentAction=null)
    {
        if (is_null($paymentAction)) {
            $paymentAction = $this->getConfigData('payment_action');
        }
        if (!$paymentAction) {
            $paymentAction = Mage_PaypalUk_Model_Api_Pro::TRXTYPE_AUTH_ONLY;
        } else {
            if ($paymentAction==Mage_PaypalUk_Model_Api_Abstract::PAYMENT_TYPE_AUTH) {
                $paymentAction = Mage_PaypalUk_Model_Api_Pro::TRXTYPE_AUTH_ONLY;
            } else {
                $paymentAction = Mage_PaypalUk_Model_Api_Pro::TRXTYPE_SALE;
            }
        }
        return $paymentAction;
    }

    /*
    * this url will be redirected when the use choose payment
    */
    public function getOrderPlaceRedirectUrl()
    {
        return $this->getRedirectUrl();
    }

    /*
    * to show form block in one page payment page
    */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('paypaluk/express_form', $name)
            ->setMethod('paypaluk_express')
            ->setPayment($this->getPayment())
            ->setTemplate('paypaluk/express/form.phtml');

        return $block;
    }

/*********************** SET EXPRESS CHECKOUT ***************************/
    /*
    * set the express check out and get token with response
    */
    public function shortcutSetExpressCheckout()
    {
        $this->getQuote()->reserveOrderId();
        $this->getApi()
            ->setPayment($this->getPayment())
            ->setTrxtype($this->getPaymentAction())
            ->setAmount($this->getQuote()->getBaseGrandTotal())
            ->setCurrencyCode($this->getQuote()->getBaseCurrencyCode())
            ->callSetExpressCheckout();

        $this->catchError();

        $this->getSession()->setExpressCheckoutMethod('shortcut');

        return $this;
    }

    /*
    catch error when there is error
    */
    public function catchError()
    {
        if ($this->getApi()->hasError() || !$this->getRedirectUrl()) {
            $s = $this->getCheckout();
            $e = $this->getApi()->getError();
            $s->addError(Mage::helper('paypalUk')->__('There was an error connecting to the Paypal server: %s', $e['message']));
            $this->getApi()->setRedirectUrl(Mage::getUrl('checkout/cart'));
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
        if ($this->getApi()->hasError() || !$this->getRedirectUrl()) {
            $s = $this->getCheckout();
            $e = $this->getApi()->getError();
            Mage::throwException(Mage::helper('paypalUk')->__('There was an error connecting to the Paypal server: %s', $e['message']));
        }
        return $this;
    }

/*********************** GET EXPRESS CHECKOUT DETAILS ***************************/
    /**
     * Making API call to check transaction's status when customer returns from paypal
     *
     * @return Mage_Paypal_Model_Express
     */
    public function returnFromPaypal()
    {
        $error='';
        try {
            $this->_getExpressCheckoutDetails();
        } catch (Exception $e) {
            $error=$e->getMessage();
             $this->getSession()->addError($e->getMessage());
             $this->_redirect('paypaluk/express/review');
        }

        switch ($this->getApi()->getUserAction()) {
            case Mage_Paypal_Model_Api_Nvp::USER_ACTION_CONTINUE:
                $this->getApi()->setRedirectUrl(Mage::getUrl('paypaluk/express/review'));
                break;
            case Mage_Paypal_Model_Api_Nvp::USER_ACTION_COMMIT:
                if ($this->getSession()->getExpressCheckoutMethod() == 'shortcut') {
                    $this->getApi()->setRedirectUrl(Mage::getUrl('paypaluk/express/saveOrder'));
                } else {
                    $this->getApi()->setRedirectUrl(Mage::getUrl('paypaluk/express/updateOrder'));
                }

                break;
        }
        return $this;
    }

    /*
    * gett
    */
    protected function _getExpressCheckoutDetails()
    {
        $api = $this->getApi()
            ->setPayment($this->getPayment())
            ->setTrxtype($this->getPaymentAction());

        if ($api->callGetExpressCheckoutDetails()===false) {
            //here need to take care where is the page should land
            Mage::throwException(Mage::helper('paypalUk')->__('There has been an error processing your payment. Please try later or contact us for help.'));
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
        if ($this->getSession()->getExpressCheckoutMethod()=='shortcut' ||
        ($this->getSession()->getExpressCheckoutMethod()!='shortcut' && $q->getCheckoutMethod()!=Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER)){
            $q->getBillingAddress()
                ->setFirstname($a->getFirstname())
                ->setLastname($a->getLastname())
                ->setEmail($a->getEmail());
        }

        $q->getShippingAddress()
            ->importCustomerAddress($a)
            ->setCollectShippingRates(true);

        //$q->setCheckoutMethod('paypaluk_express');

        $q->getPayment()
            ->setMethod('paypaluk_express')
            ->setPaypalCorrelationId($api->getCorrelationId())
            ->setPaypalPayerId($api->getPayerId())
            ->setPaypalPayerStatus($api->getPayerStatus())
            ->setAdditionalData($api->getPaypalPayerEmail())
        ;

        $q->collectTotals()->save();
    }
/*********************** DO EXPRESS CHECKOUT DETAILS ***************************/
    public function placeOrder(Varien_Object $payment)
    {
        $api = $this->getApi()
            ->setPayment($payment)
            ->setAmount($payment->getOrder()->getBaseGrandTotal())
            ->setTrxtype($this->getPaymentAction())
            ->setCurrencyCode($payment->getOrder()->getBaseCurrencyCode());

        if ($api->callDoExpressCheckoutPayment()!==false) {
            $payment->setStatus('APPROVED')
                ->setPayerId($api->getPayerId());
           if ($this->getPaymentAction()==Mage_PaypalUk_Model_Api_Pro::TRXTYPE_AUTH_ONLY) {
                $payment->setCcTransId($api->getTransactionId());
           } else {
                $payment->setLastTransId($api->getTransactionId());
           }
        } else {
            $this->throwError();
        }
        return $this;
    }

/*********************** capture, void and refund ***************************/
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
            $trxType=Mage_PaypalUk_Model_Api_Pro::TRXTYPE_DELAYED_CAPTURE;
            $api = $this->getApi()
                ->setTrxtype($trxType)
                ->setAmount($amount)
                ->setTransactionId($payment->getCcTransId())
                ->setBillingAddress($payment->getOrder()->getBillingAddress())
                ->setPayment($payment);

             if ($api->callDoDirectPayment()!==false) {
                   $payment
                    ->setStatus('APPROVED')
                    ->setPaymentStatus('CAPTURE')
                    //->setCcTransId($api->getTransactionId())
                    ->setLastTransId($api->getTransactionId())
                    ->setCcAvsStatus($api->getAvsCode())
                    ->setCcCidStatus($api->getCvv2Match());
             } else {
                $e = $api->getError();
                Mage::throwException($e['message']?$e['message']:Mage::helper('paypalUk')->__('Error in capture payment'));
             }
        } else {
            $this->placeOrder($payment);
        }
        return $this;
    }

    public function canVoid(Varien_Object $payment)
    {
        if ($payment->getCcTransId()) {
            $api = $this->getApi()
                ->setTransactionId($payment->getCcTransId())
                ->setPayment($payment);
            if ($api->canVoid()!==false) {
                $payment->setStatus(self::STATUS_VOID);
            } else {
                $e = $api->getError();
                $payment->setStatus(self::STATUS_ERROR);
                $payment->setStatusDescription($e['message']);
            }
        } else {
            $payment->setStatus(self::STATUS_ERROR);
            $payment->setStatusDescription(Mage::helper('paypalUk')->__('Invalid transaction id'));
        }
        return $this;
    }

    public function void(Varien_Object $payment)
    {
        $error = false;
        if ($payment->getVoidTransactionId()) {
             $api = $this->getApi()
                ->setTransactionId($payment->getVoidTransactionId())
                ->setPayment($payment);

             if ($api->void()!==false) {
                 $payment->setCcTransId($api->getTransactionId());
                 $payment->setStatus(self::STATUS_VOID);
             } else {
                 $e = $api->getError();
                $error = $e['message'];
             }
        } else {
            $error = Mage::helper('paypalUk')->__('Invalid transaction id');
        }
        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    public function refund(Varien_Object $payment, $amount)
    {
        $error = false;
        if (($payment->getRefundTransactionId() && $amount>0)) {
        $api = $this->getApi()
            ->setTransactionId($payment->getRefundTransactionId())
            ->setPayment($payment)
            ->setAmount($amount);
         if ($api->refund()!==false) {
             $payment->setCcTransId($api->getTransactionId());
             $payment->setStatus(self::STATUS_SUCCESS);
         } else {
             $e = $api->getError();
             $error = $e['message'];
         }

        } else {
            $error = Mage::helper('paypalUk')->__('Error in refunding the payment');
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
            ->setPayment($this->getPayment())
            ->setTrxtype($this->getPaymentAction($paymentAction))
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
