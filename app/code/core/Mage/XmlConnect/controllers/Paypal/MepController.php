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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect checkout controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Paypal_MepController extends Mage_XmlConnect_Controller_Action
{
    /**
     * Store MEP checkout model instance
     *
     * @var Mage_XmlConnect_Model_Paypal_Mep_Checkout
     */
    protected $_checkout = null;

    /**
     * Store Quote mdoel instance
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = false;

    /**
     * Make sure customer is logged in
     *
     * @return void
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->isLoggedIn()
                && !Mage::getSingleton('checkout/session')->getQuote()->isAllowedGuestCheckout()) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->_message($this->__('Customer not logged in.'), self::MESSAGE_STATUS_ERROR);
            return ;
        }
    }

    /**
     * Start MEP Checkout
     *
     * @return void
     */
    public function indexAction()
    {
        try {
            $this->_initCheckout();
            $reservedOrderId = $this->_checkout->initCheckout();
            $this->_message($this->__('Checkout has been initialized.'), self::MESSAGE_STATUS_SUCCESS);
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to start MEP Checkout.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Save shipping address to current quote using onepage model
     *
     * @return void
     */
    public function saveShippingAddressAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_message($this->__('Specified invalid data.'), self::MESSAGE_STATUS_ERROR);
            return;
        }
        try {
            $this->_initCheckout();
            $data = $this->getRequest()->getPost('shipping', array());
            $result = $this->_checkout->saveShipping($data);
            if (!isset($result['error'])) {
                $this->_message($this->__('Shipping address has been set.'), self::MESSAGE_STATUS_SUCCESS);
            } else {
                if (!is_array($result['message'])) {
                    $result['message'] = array($result['message']);
                }
                $this->_message(implode('. ', $result['message']), self::MESSAGE_STATUS_ERROR);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to save shipping address.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Get shipping methods for current quote
     *
     * @return void
     */
    public function shippingMethodsAction()
    {
        try {
            $this->_initCheckout();
            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to get shipping methods list.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Shipping method save action
     *
     * @return void
     */
    public function saveShippingMethodAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_message($this->__('Specified invalid data.'), self::MESSAGE_STATUS_ERROR);
            return;
        }
        try {
            $this->_initCheckout();
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->_checkout->saveShippingMethod($data);
            if (!isset($result['error'])) {
                $message = new Mage_XmlConnect_Model_Simplexml_Element('<message></message>');
                $message->addChild('status', self::MESSAGE_STATUS_SUCCESS);
                $message->addChild('text', $this->__('Shipping method has been set.'));
                if ($this->_getQuote()->isVirtual()) {
                    $quoteAddress = $this->_getQuote()->getBillingAddress();
                } else {
                    $quoteAddress = $this->_getQuote()->getShippingAddress();
                }
                $taxAmount = Mage::helper('core')->currency($quoteAddress->getBaseTaxAmount(), false, false);
                $message->addChild('tax_amount', Mage::helper('xmlconnect')->formatPriceForXml($taxAmount));
                $this->getResponse()->setBody($message->asNiceXml());
            } else {
                if (!is_array($result['message'])) {
                    $result['message'] = array($result['message']);
                }
                $this->_message(implode('. ', $result['message']), self::MESSAGE_STATUS_ERROR);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to save shipping method.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Shopping cart totals
     *
     * @return void
     */
    public function cartTotalsAction()
    {
        try {
            $this->_initCheckout();
            $this->loadLayout(false);
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to collect cart totals.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Submit the order
     *
     * @return void
     */
    public function saveOrderAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_message($this->__('Specified invalid data.'), self::MESSAGE_STATUS_ERROR);
            return;
        }
        try {
            /**
             * Init checkout
             */
            $this->_initCheckout();

            /**
             * Set payment data
             */
            $data = $this->getRequest()->getPost('payment', array());
            $this->_checkout->savePayment($data);

            /**
             * Place order
             */
            $this->_checkout->saveOrder();

            /**
             * Format success report
             */
            $message = new Mage_XmlConnect_Model_Simplexml_Element('<message></message>');
            $message->addChild('status', self::MESSAGE_STATUS_SUCCESS);

            $orderId = $this->_checkout->getLastOrderId();

            $text = $this->__('Thank you for your purchase! ');
            $text .= $this->__('Your order # is: %s. ', $orderId);
            $text .= $this->__('You will receive an order confirmation email with details of your order and a link to track its progress.');
            $message->addChild('text', $text);

            $message->addChild('order_id', $orderId);
            $this->getResponse()->setBody($message->asNiceXml());
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to place the order.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Instantiate quote and checkout
     *
     * @throws Mage_Core_Exception
     * @return void
     */
    protected function _initCheckout()
    {

        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            Mage::throwException($this->__('Unable to initialize MEP Checkout.'));
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::throwException($error);
        }
        $this->_getCheckoutSession()->setCartWasUpdated(false);

        $this->_checkout = Mage::getSingleton('xmlconnect/paypal_mep_checkout', array('quote'  => $quote));
    }

    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return checkout quote object
     *
     * @return Mage_Sale_Model_Quote
     */
    protected function _getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }
}
