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
 * @package    Mage_PaypalUk
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    /*
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
        return Mage::getSingleton('paypalUk/api_pro');
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        $api = $this->getApi()
            ->setTrxtype(Mage_PaypalUk_Model_Api_Pro::TRXTYPE_AUTH_ONLY)
            ->setAmount($amount)
            ->setBillingAddress($payment->getOrder()->getBillingAddress())
            ->setShippingAddress($payment->getOrder()->getShippingAddress())
            ->setPayment($payment);

         if($api->callDoDirectPayment()!==false) {
               $payment
                ->setStatus('APPROVED')
                ->setPaymentStatus('AUTHORIZE')
                ->setCcTransId($api->getTransactionId())
                ->setCcAvsStatus($api->getAvsCode())
                ->setCcCidStatus($api->getCvv2Match());
         }else{
            $e = $api->getError();
            Mage::throwException($e['message']?$e['message']:Mage::helper('paypalUk')->__('There has been an error processing your payment. Please try later or contact us for help.'));
         }

    }

    public function capture(Varien_Object $payment, $amount)
    {
       if ($payment->getCcTransId()) {
           $trxType=Mage_PaypalUk_Model_Api_Pro::TRXTYPE_DELAYED_CAPTURE;
        } else {
           //when there is no transaction id, we do sales trxtype
           $trxType=Mage_PaypalUk_Model_Api_Pro::TRXTYPE_SALE;
        }

        $api = $this->getApi()
            ->setTrxtype($trxType)
            ->setAmount($amount)
            ->setTransactionId($payment->getCcTransId())
            ->setBillingAddress($payment->getOrder()->getBillingAddress())
            ->setShippingAddress($payment->getOrder()->getShippingAddress())
            ->setPayment($payment);

         if ($api->callDoDirectPayment()!==false) {
               $payment
                ->setStatus('APPROVED')
                ->setPaymentStatus('CAPTURE')
                ->setCcTransId($api->getTransactionId())
                ->setCcAvsStatus($api->getAvsCode())
                ->setCcCidStatus($api->getCvv2Match());
         } else {
            $e = $api->getError();
            Mage::throwException($e['message']?$e['message']:Mage::helper('paypalUk')->__('There has been an error processing your payment. Please try later or contact us for help.'));
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