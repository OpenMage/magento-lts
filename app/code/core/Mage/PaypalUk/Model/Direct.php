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
 * PayPalUk Direct Module
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PaypalUk_Model_Direct extends Mage_Payment_Model_Method_Cc
{
    protected $_code  = 'paypaluk_direct';
    protected $_formBlockType = 'paypaluk/direct_form';
    protected $_infoBlockType = 'paypaluk/direct_info';
    protected $_canSaveCc = false;

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;

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
     * Get 3D secure checking if it enabled or disabled
     *
     * @return string
     */
    public function get3DSecureEnabled()
    {
        return $this->getConfigData('centinel');
    }

    /**
     * Used for enablin line item options
     *
     * @return  string
     */
    public function getLineItemEnabled()
    {
        return $this->getConfigData('line_item');
    }

    /**
     * Return paypaluk 3d secure validation model
     *
     * @return Mage_PaypalUk_Model_Direct_Validate
     */
    public function getValidate()
    {
        return Mage::getSingleton('paypaluk/direct_validate');
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
     * overwrites the method of Mage_Payment_Model_Method_Cc
     * for switch or solo card
     */
    public function OtherCcType($type)
    {
        return (parent::OtherCcType($type) || $type=='SS');
    }

    /**
     * overwrites the method of Mage_Payment_Model_Method_Cc
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {

        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        parent::assignData($data);
        $info = $this->getInfoInstance();

        if ($data->getCcType()=='SS') {
            $info->setCcSsIssue($data->getCcSsIssue())
                ->setCcSsStartMonth($data->getCcSsStartMonth())
                ->setCcSsStartYear($data->getCcSsStartYear())
            ;
        }
        return $this;
    }

    /**
     * Get Paypal API Model
     *
     * @return Mage_PaypalUk_Model_Api_Pro
     */
    public function getApi()
    {
        return Mage::getSingleton('paypaluk/api_pro');
    }

    /**
     * Authorize payment
     * @param Verien_Object $payment
     * @param double $amount
     * @return Mage_PayPalUk_Model_Direct
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        $isCentinelVerified = false;
        $items = null;
        if ($this->getLineItemEnabled()) {
            $items = $this->getQuote()->getAllItems();
        }

        $api = $this->getApi()
            ->setLineItems($items)
            ->setShippingAmount($payment->getOrder()->getBaseShippingAmount())
            ->setDiscountAmount($payment->getOrder()->getBaseDiscountAmount())
            ->setItemAmount($payment->getOrder()->getBaseSubtotal())
            ->setItemTaxAmount($payment->getOrder()->getTaxAmount())
            ->setTrxtype(Mage_PaypalUk_Model_Api_Pro::TRXTYPE_AUTH_ONLY)
            ->setAmount($amount)
            ->setBillingAddress($payment->getOrder()->getBillingAddress())
            ->setShippingAddress($payment->getOrder()->getShippingAddress())
            ->setPayment($payment);

        if ($this->get3DSecureEnabled()) {
            $isCentinelVerified = true;
            $api->setAuthStatus($this->getValidate()->getPaResStatus())
                ->setMpiVendor($this->getValidate()->getEnrolled())
                ->setCavv($this->getValidate()->getCavv())
                ->setEci3d($this->getValidate()->getEciFlag())
                ->setXid($this->getValidate()->getXid());
        }

        if($api->callDoDirectPayment()!==false) {
              $payment
               ->setStatus('APPROVED')
               ->setPaymentStatus('AUTHORIZE')
               ->setCcTransId($api->getTransactionId())
               ->setCcSecureVerify($isCentinelVerified)
               ->setCcAvsStatus($api->getAvsCode())
               ->setCcCidStatus($api->getCvv2Match());
        }else{
            $e = $api->getError();
            Mage::throwException($e['message']?$e['message']:Mage::helper('paypal')->__('There has been an error processing your payment. Please try later or contact us for help.'));
        }
        return $this;
    }

    /**
     * Capture payment
     * @param Verien_Object $payment
     * @param double $amount
     * @return Mage_PayPalUk_Model_Direct
     */
    public function capture(Varien_Object $payment, $amount)
    {
       $isCentinelVerified = false;
       if ($payment->getCcTransId()) {
           $trxType=Mage_PaypalUk_Model_Api_Pro::TRXTYPE_DELAYED_CAPTURE;
        } else {
           //when there is no transaction id, we do sales trxtype
           $trxType=Mage_PaypalUk_Model_Api_Pro::TRXTYPE_SALE;
        }

        $items = null;
        if ($this->getLineItemEnabled()) {
            $items = $this->getQuote()->getAllItems();
        }

        $api = $this->getApi()
            ->setLineItems($items)
            ->setShippingAmount($payment->getOrder()->getBaseShippingAmount())
            ->setDiscountAmount($payment->getOrder()->getBaseDiscountAmount())
            ->setItemAmount($payment->getOrder()->getBaseSubtotal())
            ->setItemTaxAmount($payment->getOrder()->getTaxAmount())
            ->setTrxtype($trxType)
            ->setAmount($amount)
            ->setTransactionId($payment->getCcTransId())
            ->setBillingAddress($payment->getOrder()->getBillingAddress())
            ->setShippingAddress($payment->getOrder()->getShippingAddress())
            ->setPayment($payment);

        if ($this->get3DSecureEnabled()) {
            $isCentinelVerified = true;
            $api->setAuthStatus($this->getValidate()->getPaResStatus())
                ->setMpiVendor($this->getValidate()->getEnrolled())
                ->setCavv($this->getValidate()->getCavv())
                ->setEci3d($this->getValidate()->getEciFlag())
                ->setXid($this->getValidate()->getXid());
        }

        if ($api->callDoDirectPayment()!==false) {
               $payment
                ->setStatus('APPROVED')
                ->setPaymentStatus('CAPTURE')
                ->setCcSecureVerify($isCentinelVerified)
                ->setCcTransId($api->getTransactionId())
                ->setCcAvsStatus($api->getAvsCode())
                ->setCcCidStatus($api->getCvv2Match());
        } else {
            $e = $api->getError();
            Mage::throwException($e['message']?$e['message']:Mage::helper('paypal')->__('There has been an error processing your payment. Please try later or contact us for help.'));
        }

        return $this;
    }

    /**
     * checking the transaction id is valid or not and transction id was not settled
     *
     * @return Mage_PaypalUk_Model_Direct
     */
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
            $payment->setStatusDescription(Mage::helper('paypal')->__('Invalid transaction id'));
        }
        return $this;
    }

    /**
     * Void payment
     * @param Verien_Object $payment
     * @return Mage_PayPalUk_Model_Direct
     */
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
            $error = Mage::helper('paypal')->__('Invalid transaction id');
        }
        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    /**
     * Refund payment
     * @param Verien_Object $payment
     * @param double $amount
     * @return Mage_PayPalUk_Model_Direct
     */
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
            $error = Mage::helper('paypal')->__('Error in refunding the payment');
        }
        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    /**
    * cancel payment
    *
    * @param Varien_Object $payment
    * @return Mage_PaypalUk_Model_Direct
    */
    public function cancel(Varien_Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count() && ($payment->getCcTransId() || $payment->getLastTransId())) {
            if ($payment->getCcTransId()) {
                $payment->setVoidTransactionId($payment->getCcTransId());
            } else {
                $payment->setVoidTransactionId($payment->getLastTransId());
            }

            $this->void($payment);
        }
        parent::cancel($payment);
        return $this;
    }
}
