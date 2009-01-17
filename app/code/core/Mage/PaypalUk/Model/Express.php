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
 * @category   Mage
 * @package    Mage_PaypalUk
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    /**
     * Get Paypal API Model
     *
     * @return Mage_PaypalUk_Model_Api_Pro
     */
    public function getApi()
    {
        return Mage::getSingleton('paypalUk/api_pro');
    }

    public function getRedirectUrl()
    {
        return $this->getApi()->getRedirectUrl();
    }

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

    public function getPaymentAction()
    {
        $paymentAction = Mage::getStoreConfig('payment/paypaluk_express/payment_action');
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
    public function getCheckoutRedirectUrl()
    {
        return Mage::getUrl('paypaluk/express/mark');
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
    public function markSetExpressCheckout()
    {
        $address = $this->getQuote()->getShippingAddress();

        $this->getApi()
            ->setTrxtype($this->getPaymentAction())
            ->setAmount($address->getBaseGrandTotal())
            ->setCurrencyCode($this->getQuote()->getBaseCurrencyCode())
            ->setShippingAddress($address)
            ->callSetExpressCheckout();

        $this->catchError();
        $this->getSession()->setExpressCheckoutMethod('mark');
        return $this;
    }
    /*
    * set the express check out and get token with response
    */
    public function shortcutSetExpressCheckout()
    {
        $this->getApi()
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

/*********************** GET EXPRESS CHECKOUT DETAILS ***************************/
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
                $this->getApi()->setRedirectUrl(Mage::getUrl('paypaluk/express/saveOrder'));
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
        ($this->getSession()->getExpressCheckoutMethod()=='mark' && $q->getCheckoutMethod()!='register')){
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
        $api = $this->getApi();
        $api->setAmount($payment->getOrder()->getBaseGrandTotal())
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
            $e = $api->getError();
            Mage::throwException($e['message']);
        }
        return $this;
    }

/*********************** capture, void and refund ***************************/
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
}