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
 * PayPal Standard Checkout Module
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = Mage_Paypal_Model_Config::METHOD_WPS;
    protected $_formBlockType = 'paypal/standard_form';
    protected $_infoBlockType = 'paypal/payment_info';
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;

    /**
     * Config instance
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

    /**
     * Whether method is available for specified currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->getConfig()->isCurrencyCodeSupported($currencyCode);
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

    /**
     * Create main block for standard form
     *
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('paypal/standard_form', $name)
            ->setMethod('paypal_standard')
            ->setPayment($this->getPayment())
            ->setTemplate('paypal/standard/form.phtml');

        return $block;
    }

    /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('paypal/standard/redirect', array('_secure' => true));
    }

    /**
     * Return form field array
     *
     * @return array
     */
    public function getStandardCheckoutFormFields()
    {
        $api = Mage::getModel('paypal/api_standard')->setConfigObject($this->getConfig());
        $quote = $this->getQuote();
        $api->setOrderId($this->getCheckout()->getLastRealOrderId()) // TODO reserved order id
            ->setCurrencyCode($quote->getBaseCurrencyCode())
            //->setPaymentAction()
            ->setNotifyUrl(Mage::getUrl('paypal/ipn/standard'))
            ->setReturnUrl(Mage::getUrl('paypal/standard/success'))
            ->setCancelUrl(Mage::getUrl('paypal/standard/cancel'))
        ;

        // export address
        $isQuoteVirtual = $quote->getIsVirtual();
        $address = $isQuoteVirtual ? $quote->getBillingAddress() : $quote->getShippingAddress();
        if ($isQuoteVirtual) {
            $api->setNoShipping(true);
        } elseif ($address->getEmail()) {
            $api->setAddress($address);
        }

        list($items, $totals, $discountAmount, $shippingAmount) = Mage::helper('paypal')->prepareLineItems($quote, false, true);
        // prepare line items if required in config
        if ($this->_config->lineItemsEnabled) {
            $api->setLineItems($items)->setLineItemTotals($totals)->setDiscountAmount($discountAmount);
        }
        // or values specific for aggregated order
        else {
            $grandTotal = $quote->getBaseGrandTotal();
            if (!$isQuoteVirtual) {
                $api->setShippingAmount($shippingAmount);
                $grandTotal -= $shippingAmount;
            }
            $api->setAmount($grandTotal)->setCartSummary($this->_getAggregatedCartSummary());
        }

        $result = $api->getStandardCheckoutRequest();
        return $result;
    }

    /**
     * Instantiate state and set it to state object
     * @param string $paymentAction
     * @param Varien_Object
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }

    /**
     * Config instance getter
     * @return Mage_Paypal_Model_Config
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $params = array($this->_code);
            if ($this->getStore()) {
                $params[] = (int)$this->getStore();
            }
            $this->_config = Mage::getModel('paypal/config', $params);
        }
        return $this->_config;
    }

    /**
     * Aggregated cart summary label getter
     *
     * @return string
     */
    private function _getAggregatedCartSummary()
    {
        if ($this->_config->lineItemsSummary) {
            return $this->_config->lineItemsSummary;
        }
        return Mage::app()->getStore($this->getStore())->getFrontendName();
    }
}
