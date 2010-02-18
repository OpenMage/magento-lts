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
    protected $_code  = Mage_Paypal_Model_Config::METHOD_WPP_DIRECT;
    protected $_infoBlockType = 'paypal/payment_info';

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;

    /**
     * Website Payments Pro instance
     *
     * @var Mage_Paypal_Model_Pro
     */
    protected $_pro = null;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'paypal/pro';

    /**
     * Info instance type
     *
     * @var $_proType string
     */
    protected $_infoType = 'paypal/info';

    /**
     * Ipn notify action
     *
     * @var string
     */
    protected $_notifyAction = 'paypal/ipn/direct';

    public function __construct($params = array())
    {
        $proInstance = array_shift($params);
        if ($proInstance && ($proInstance instanceof Mage_Paypal_Model_Pro)) {
            $this->_pro = $proInstance;
        } else {
            $this->_pro = Mage::getModel($this->_proType);
        }
        $this->_pro->setMethod($this->_code);
    }

    /**
     * Store setter
     * Also updates store ID in config object
     *
     * @param Mage_Core_Model_Store|int $store
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        $this->_pro->getConfig()->setStoreId(is_object($store) ? $store->getId() : $store);
        return $this;
    }

    /**
     * Whether method is available for specified currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->_pro->getConfig()->isCurrencyCodeSupported($currencyCode);
    }

    /**
     * Payment action getter compatible with payment model
     *
     * @see Mage_Sales_Model_Payment::place()
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return $this->_pro->getConfig()->getPaymentAction();
    }

    /**
     * Authorize payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Direct
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        return $this->_placeOrder($payment, $amount);
    }

    /**
     * Void payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Direct
     */
    public function void(Varien_Object $payment)
    {
        $this->_pro->void($payment);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Direct
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if (false === $this->_pro->capture($payment, $amount)) {
            $this->_placeOrder($payment, $amount);
        }
        return $this;
    }

    /**
     * Refund capture
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Direct
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $this->_pro->refund($payment, $amount);
        return $this;
    }

    /**
     * Cancel payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Direct
     */
    public function cancel(Varien_Object $payment)
    {
        $this->_pro->cancel($payment);
        return $this;
    }

    /**
     * Set fallback API URL if not defined in configuration
     *
     * @return Mage_Centinel_Model_Service
     */
    public function getCentinelValidator()
    {
        $validator = parent::getCentinelValidator();
        if (!$validator->getCustomApiEndpointUrl()) {
            $validator->setCustomApiEndpointUrl($this->_pro->getConfig()->centinelDefaultApiUrl);
        }
        return $validator;
    }

    /**
     * Place an order with authorization or capture action
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Mage_Paypal_Model_Direct
     */
    protected function _placeOrder(Mage_Sales_Model_Order_Payment $payment, $amount)
    {
        $order = $payment->getOrder();
        $api = $this->_pro->getApi()
            ->setPaymentAction($this->_pro->getConfig()->paymentAction)
            ->setIpAddress(Mage::app()->getRequest()->getClientIp(false))
            ->setAmount($amount)
            ->setCurrencyCode($order->getBaseCurrencyCode())
            ->setInvNum($order->getIncrementId())
            ->setEmail($order->getCustomerEmail())
            ->setNotifyUrl(Mage::getUrl($this->_notifyAction))
            ->setCreditCardType($payment->getCcType())
            ->setCreditCardNumber($payment->getCcNumber())
            ->setCreditCardExpirationDate(sprintf('%02d%02d', $payment->getCcExpMonth(), $payment->getCcExpYear()))
            ->setCreditCardCvv2($payment->getCcCid())
            ->setMaestroSoloIssueNumber($payment->getCcSsIssue())
        ;
        if ($payment->getCcSsStartMonth() && $payment->getCcSsStartYear()) {
            $api->setMaestroSoloIssueDate(sprintf('%02d%02d', $payment->getCcSsStartMonth(), preg_replace('~\d\d(\d\d)~','$1', $payment->getCcSsStartYear())));
        }
        if ($this->getIsCentinelValidationEnabled()) {
            $this->getCentinelValidator()->exportCmpiData($api);
        }

        // add shipping address
        if ($order->getIsVirtual()) {
            $api->setAddress($order->getBillingAddress())->setSuppressShipping(true);
        } else {
            $api->setAddress($order->getShippingAddress());
        }

        // add line items
        if ($this->_pro->getConfig()->lineItemsEnabled && Mage::helper('paypal')->doLineItemsMatchAmount($order, $amount)) {//For transfering line items order amount must be equal to cart total amount
            list($items, $totals) = Mage::helper('paypal')->prepareLineItems($order);
            $api->setLineItems($items)->setLineItemTotals($totals);
        }

        // call api and import transaction and other payment information
        $api->callDoDirectPayment();
        $this->_importResultToPayment($api, $payment);
        return $this;
    }

    /**
     * Import direct payment results to payment
     *
     * @param Mage_Paypal_Model_Api_Nvp
     * @param Mage_Sales_Model_Order_Payment
     */
    protected function _importResultToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getTransactionId())->setIsTransactionClosed(0)
            ->setIsTransactionPending($api->getIsPaymentPending());
        Mage::getModel($this->_infoType)->importToPayment($api, $payment);
    }
}
