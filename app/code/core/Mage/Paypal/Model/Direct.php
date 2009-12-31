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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * PayPal Direct Module
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Direct extends Mage_Payment_Model_Method_Cc
{
    protected $_code  = 'paypal_direct';
    protected $_formBlockType = 'paypal/direct_form';
    protected $_infoBlockType = 'paypal/direct_info';

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
    protected $_canSaveCc = false;

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
     * Used for enablin line item options
     *
     * @return  string
     */
    public function getLineItemEnabled()
    {
        return $this->getConfigData('line_item');
    }

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
     * Return config value for fraud management option
     *
     * @return string
     */
    public function getFraudFilterStatus()
    {
        return $this->getConfigData('fraud_filter');
    }

    /**
     * Return fraud status, if fraud management enabled and api returned fraud suspicious
     * we return true, we may store fraud result, otherwise return false,
     * don't perform any actions with frauds
     *
     * @return bool
     */
    public function canStoreFraud()
    {
        if ($this->getFraudFilterStatus() && $this->getApi()->getIsFraud()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return status if user may perform any action with fraud transaction
     *
     * @param $payment Varien_Object
     *
     * @return bool
     */
    public function canManageFraud(Varien_Object $payment)
    {
        if ($this->getFraudFilterStatus() && $payment->getOrder()->getStatus() == $this->getConfigData('fraud_order_status')) {
            return true;
        } else {
            return false;
        }
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
     * return paypal direct validation object
     * are used for 3d Secure validation
     *
     */
    public function getValidate()
    {
        return Mage::getSingleton('paypal/direct_validate');
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
     * Return redirect url which settuped as paypal request result.
     * comes in response
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getApi()->getRedirectUrl();
    }

    /**
     * return state code
     *
     * return string
     */
    public function getCountryRegionId()
    {
        $a = $this->getApi()->getShippingAddress();
        return $a->getRegionCode();
    }

    /**
     * Return payment action, depends of paypal response.
     *
     * return string
     */
    public function getPaymentAction()
    {
        $paymentAction = $this->getConfigData('payment_action');
        if (!$paymentAction) {
            $paymentAction = Mage_Paypal_Model_Api_Nvp::PAYMENT_TYPE_AUTH;
        }
        return $paymentAction;
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        $api = $this->getApi()
            ->setPaymentType($this->getPaymentAction())
            ->setAmount($amount)
            ->setBillingAddress($payment->getOrder()->getBillingAddress())
            ->setShippingAddress($payment->getOrder()->getShippingAddress())
            ->setEmail($payment->getOrder()->getCustomerEmail())
            ->setPayment($payment)
            ->setInvNum($payment->getOrder()->getIncrementId());

        $this->_appendAdditionalToApi($payment, $api);

        if ($api->callDoDirectPayment()!==false) {
            if ($this->canStoreFraud()) {
                $payment->setFraudFlag(true);
            }

            $payment
                ->setStatus(self::STATUS_APPROVED)
                ->setCcTransId($api->getTransactionId())
                ->setCcAvsStatus($api->getAvsCode())
                ->setCcSecureVerify((bool)$api->getXid())
                ->setCcCidStatus($api->getCvv2Match());


            #$payment->getOrder()->addStatusToHistory(Mage::getStoreConfig('payment/paypal_direct/order_status'));
        } else {
            $e = $api->getError();
            if (isset($e['short_message'])) {
                $message = $e['short_message'];
            } else {
                $message = Mage::helper('paypal')->__('There has been an error processing your payment. Please try later or contact us for help.');
            }
            if (isset($e['long_message'])) {
                $message .= ': '.$e['long_message'];
            }
            Mage::throwException($message);
        }
        return $this;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $api = $this->getApi()
            ->setPaymentType(Mage_Paypal_Model_Api_Nvp::PAYMENT_TYPE_SALE)
            ->setAmount($amount)
            ->setBillingAddress($payment->getOrder()->getBillingAddress())
            ->setShippingAddress($payment->getOrder()->getShippingAddress())
            ->setEmail($payment->getOrder()->getCustomerEmail())
            ->setPayment($payment)
            ->setInvNum($payment->getOrder()->getIncrementId());

        $this->_appendAdditionalToApi($payment, $api);

        if ($payment->getCcTransId()) {
            $api->setAuthorizationId($payment->getCcTransId())
                ->setCompleteType('NotComplete');
            $result = $api->callDoCapture()!==false;
        } else {
            $result = $api->callDoDirectPayment()!==false;
        }
        if ($result) {
            if ($this->canStoreFraud()) {
                $payment->getOrder()->setFraudFlag(true);
            }

            $payment
                ->setStatus(self::STATUS_APPROVED)
                ->setAccountStatus($api->getAccountStatus())
                ->setLastTransId($api->getTransactionId())
                ->setCcSecureVerify((bool)$api->getXid())
                ->setCcAvsStatus($api->getAvsCode())
                ->setCcCidStatus($api->getCvv2Match());
            if ($this->canManageFraud($payment)) {
                $this->updateGatewayStatus($payment, Mage_Paypal_Model_Api_Abstract::ACTION_ACCEPT);
            }
        } else {
            $e = $api->getError();
            if (isset($e['short_message'])) {
                $message = $e['short_message'];
            } else {
                $message = Mage::helper('paypal')->__('There has been an error processing your payment. Please try later or contact us for help.');
            }
            if (isset($e['long_message'])) {
                $message .= ': '.$e['long_message'];
            }
            Mage::throwException($message);
        }
        return $this;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        $api = $this->getApi()
            ->setPaymentType($this->getPaymentAction())
            ->setAmount($payment->getOrder()->getGrandTotal())
            ->setBillingAddress($payment->getOrder()->getBillingAddress())
            ->setPayment($payment)
            ->setInvNum($payment->getOrder()->getIncrementId());

        $this->_appendAdditionalToApi($payment, $api);

        if ($api->callDoDirectPayment()!==false) {
            if ($this->canStoreFraud()) {
                $payment->getOrder()->setFraudFlag(true);
            }

            $payment
                ->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($api->getTransactionId())
                ->setCcSecureVerify((bool)$api->getXid())
                ->setCcAvsStatus($api->getAvsCode())
                ->setCcCidStatus($api->getCvv2Match());
            #$payment->getOrder()->addStatusToHistory(Mage::getStoreConfig('payment/paypal_direct/order_status'));
        } else {
            $e = $api->getError();
            if (isset($e['short_message'])) {
                $message = $e['short_message'];
            } else {
                $message = Mage::helper('paypal')->__('There has been an error processing your payment. Please try later or contact us for help.');
            }
            if (isset($e['long_message'])) {
                $message .= ': '.$e['long_message'];
            }
            $payment
                ->setStatus('ERROR')
                ->setStatusDescription($message);
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
            $api->setPayment($payment);
            $api->setAuthorizationId($payment->getVoidTransactionId());
            if ($api->callDoVoid()!==false){
                 if ($this->canManageFraud($payment)) {
                     $this->updateGatewayStatus($payment, Mage_Paypal_Model_Api_Abstract::ACTION_DENY);
                 }
                 $payment->setStatus('SUCCESS')
                    ->setCcTransId($api->getTransactionId());
            }else{
               $e = $api->getError();
               $error = $e['short_message'].': '.$e['long_message'];
            }
        }else{
            $payment->setStatus('ERROR');
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
          if($payment->getRefundTransactionId() && $amount>0){
              $api = $this->getApi();
              $api->setPayment($payment);
              //we can refund the amount full or partial so it is good to set up as partial refund
              $api->setTransactionId($payment->getRefundTransactionId())
                ->setRefundType(Mage_Paypal_Model_Api_Nvp::REFUND_TYPE_PARTIAL)
                ->setAmount($amount);

             if ($api->callRefundTransaction()!==false){
                 if ($this->canManageFraud($payment)) {
                     $this->updateGatewayStatus($payment, Mage_Paypal_Model_Api_Abstract::ACTION_DENY);
                 }
                 $payment->setStatus('SUCCESS')
                    ->setCcTransId($api->getTransactionId());
             }else{
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
       * Process pending transaction, set status deny or approve
       * @param Varien_Object $payment
       * @param string $action
       * @return Mage_PayPal_Model_Direct
       */
      public function updateGatewayStatus(Varien_Object $payment, $action)
      {
          if ($payment && $action) {
              if ($payment->getCcTransId()) {
                  $transactionId = $payment->getCcTransId();
              } else {
                  $transactionId = $payment->getLastTransId();
              }
              $api = $this->getApi();
              $api->setAction($action)
                  ->setTransactionId($transactionId)
                  ->callManagePendingTransactionStatus();
          }
          return $this;
      }

    /**
    * cancel payment, if it has fraud status, need to update paypal status
    *
    * @param Varien_Object $payment
    * @return Mage_Paypal_Model_Direct
    */
    public function cancel(Varien_Object $payment)
    {
        if ($this->canManageFraud($payment)) {
            $this->updateGatewayStatus($payment, Mage_Paypal_Model_Api_Abstract::ACTION_DENY);
        }

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


   /**
     * Get config paypal action url
     * Used to universilize payment actions when processing payment place
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        $paymentAction = $this->getConfigData('payment_action');
        switch ($paymentAction){
            case Mage_Paypal_Model_Api_Abstract::PAYMENT_TYPE_SALE:
                $paymentAction = Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
                break;
            case Mage_Paypal_Model_Api_Abstract::PAYMENT_TYPE_AUTH:
            default:
                $paymentAction = Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
                break;
        }
        return $paymentAction;
    }

    /**
     * Add additional fields to Api
     *
     * @param $payment Varien_Object
     * @param $api Mage_PayPal_Model_Api_Nvp
     *
     * @return Mage_PayPal_Model_Direct
     */
    protected function _appendAdditionalToApi($payment, $api)
    {
        if (is_object($api) && is_object($payment)) {
            if ($this->getLineItemEnabled()) {
                $api->setLineItems($this->getQuote()->getAllItems())
                    ->setShippingAmount($payment->getOrder()->getBaseShippingAmount())
                    ->setDiscountAmount($payment->getOrder()->getBaseDiscountAmount())
                    ->setItemAmount($payment->getOrder()->getBaseSubtotal())
                    ->setItemTaxAmount($payment->getOrder()->getTaxAmount());
            }

            if ($this->get3DSecureEnabled()) {
                $api->setAuthStatus($this->getValidate()->getPaResStatus())
                    ->setMpiVendor($this->getValidate()->getEnrolled())
                    ->setCavv($this->getValidate()->getCavv())
                    ->setEci3d($this->getValidate()->getEciFlag())
                    ->setXid($this->getValidate()->getXid());
            }

            if ($this->getFraudFilterStatus()) {
                $api->setReturnFmfDetails(true);
            }
        }
        return $this;
    }
}
