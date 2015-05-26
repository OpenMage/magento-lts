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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect PayPal Mobile Express Checkout Library checkout controller
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Paypal_MeclController extends Mage_XmlConnect_Controller_Action
{
    /**
     * Config mode type
     *
     * @var string
     */
    protected $_configType = 'xmlconnect/payment_method_paypal_config';

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = Mage_XmlConnect_Model_Payment_Method_Paypal_Mecl::MECL_METHOD_CODE;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'xmlconnect/paypal_mecl_checkout';

    /**
     * Paypal Mobile Express Checkout Library
     *
     * @var Mage_XmlConnect_Model_Payment_Method_Paypal_Mecl
     */
    protected $_checkout = null;

    /**
     * PayPal Mobile Express Checkout Library config model
     *
     * @var Mage_XmlConnect_Model_Payment_Method_Paypal_Config
     */
    protected $_config = null;

    /**
     * Checkout Quote
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = false;

    /**
     * Instantiate config
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_config = Mage::getModel($this->_configType, array($this->_configMethod));
    }

    /**
     * Make sure customer is logged in
     *
     * @return null
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->isLoggedIn()
            && !Mage::getSingleton('checkout/session')->getQuote()->isAllowedGuestCheckout()
        ) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->_message(
                $this->__('Customer not logged in.'), self::MESSAGE_STATUS_ERROR, array('logged_in' => '0')
            );
            return;
        }
    }

    /**
     * Start Mobile Express Checkout by requesting initial token and dispatching customer to PayPal
     */
    public function startAction()
    {
        try {
            $this->_initCheckout();

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($customer && $customer->getId()) {
                $this->_checkout->setCustomerWithAddressChange(
                    $customer, null, $this->_getQuote()->getShippingAddress()
                );
            }

            $token = $this->_checkout->start(Mage::getUrl('*/*/return'), Mage::getUrl('*/*/cancel'));

            if ($token) {
                $this->_initToken($token);
                /** @var $message Mage_XmlConnect_Model_Simplexml_Element */
                $message = Mage::getModel('xmlconnect/simplexml_element', '<message></message>');
                $message->addChild('status', self::MESSAGE_STATUS_SUCCESS);
                $message->addChild('token', $token);
                $this->getResponse()->setBody($message->asNiceXml());
            } else {
                $this->_message($this->__('Token has not been set.'), self::MESSAGE_STATUS_ERROR);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to start Mobile Express Checkout.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Return from PayPal and dispatch customer to order review page
     * (GetExpressCheckoutDetails method call)
     */
    public function returnAction()
    {
        try {
            $this->_initCheckout();
            $this->_checkout->returnFromPaypal($this->_initToken());

            $this->_message($this->__('Mobile Express Checkout processed successfully.'), self::MESSAGE_STATUS_SUCCESS);
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_message($this->__('Unable to initialize return action.'), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Review order after returning from PayPal
     */
    public function reviewAction()
    {
        if ($this->_checkApiForward('orderReview', Mage_XmlConnect_Helper_Data::DEVICE_API_V_23)) {
            return;
        }
        try {
            $this->_initCheckout();
            $this->_checkout->prepareOrderReview($this->_initToken());
            $this->loadLayout(false);
            $this->_initLayoutMessages('paypal/session');

            $messages = $this->_getSession()->getMessages(true);
            $messageArray = array();
            foreach ($messages->getItems() as $message) {
                $messageArray[] = $message;
            }

            $detailsBlock = $this->getLayout()->getBlock('xmlconnect.cart.paypal.mecl.review');
            if (count($messageArray)) {
                $detailsBlock->setPaypalMessages($messageArray);
            }

            $detailsBlock->setQuote($this->_getQuote())->getChild('details')->setQuote($this->_getQuote())
                ->getChild('totals')->setQuote($this->_getQuote());
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to initialize express checkout review.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Order review after returning from PayPal
     */
    public function orderReviewAction()
    {
        try {
            $this->_initCheckout();
            $this->_checkout->prepareOrderReview($this->_initToken());
            $this->loadLayout(false);
            $this->_initLayoutMessages('paypal/session');

            $messages = $this->_getSession()->getMessages(true);
            $messageArray = array();
            foreach ($messages->getItems() as $message) {
                $messageArray[] = $message;
            }

            $detailsBlock = $this->getLayout()->getBlock('xmlconnect.cart.paypal.mecl.review');
            if (count($messageArray)) {
                $detailsBlock->setPaypalMessages($messageArray);
            }

            $detailsBlock->setQuote($this->_getQuote())->getChild('details')->setQuote($this->_getQuote())
                ->getChild('totals');

            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to initialize express checkout review.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Get shipping method list for PayPal
     */
    public function shippingMethodsAction()
    {
        try {
            $this->_initCheckout();
            $this->_checkout->prepareOrderReview($this->_initToken());
            $this->loadLayout(false);

            $this->getLayout()->getBlock('xmlconnect.cart.paypal.mecl.shippingmethods')->setQuote($this->_getQuote());
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message(
                $this->__('Unable to initialize express checkout shipping method list.'), self::MESSAGE_STATUS_ERROR
            );
            Mage::logException($e);
        }
    }

    /**
     * Update shipping method (combined action for ajax and regular request)
     */
    public function saveShippingMethodAction()
    {
        try {
            $this->_initCheckout();
            if ($this->getRequest()->getParam('shipping_method', false)) {
                $this->_checkout->updateShippingMethod($this->getRequest()->getParam('shipping_method'));
                $this->_getQuote()->save();
                $this->_message($this->__('Shipping method successfully updated'), self::MESSAGE_STATUS_SUCCESS);
            } else {
                $this->_message($this->__('Shipping method is required'), self::MESSAGE_STATUS_ERROR);
            }
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to update shipping method.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Submit the order
     */
    public function placeOrderAction()
    {
        try {
            $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
            if (!empty($requiredAgreements)) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if (array_diff($requiredAgreements, $postedAgreements)) {
                    $error = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->_message($error, self::MESSAGE_STATUS_ERROR);
                    return;
                }
            }

            $this->_initCheckout();
            $this->_checkout->place($this->_initToken());

            // prepare session to success or cancellation page
            $session = $this->_getCheckoutSession();
            $session->clearHelperData();

            // "last successful quote"
            $quoteId = $this->_getQuote()->getId();
            $session->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);

            // an order may be created
            $order = $this->_checkout->getOrder();
            if ($order) {
                $orderId = $order->getId();
                $realOrderId = $order->getIncrementId();
                $session->setLastOrderId($order->getId())->setLastRealOrderId($order->getIncrementId());
            }

            // recurring profiles may be created along with the order or without it
            $profiles = $this->_checkout->getRecurringPaymentProfiles();
            if ($profiles) {
                $ids = array();
                foreach($profiles as $profile) {
                    $ids[] = $profile->getId();
                }
                $session->setLastRecurringProfileIds($ids);
            }

            $this->_initToken(false); // no need in token anymore

            /** @var $message Mage_XmlConnect_Model_Simplexml_Element */
            $message = Mage::getModel('xmlconnect/simplexml_element', '<message></message>');
            $message->addChild('status', self::MESSAGE_STATUS_SUCCESS);

            $text = $this->__('Thank you for your purchase! ');
            $text .= $this->__('Your order # is: %s. ', $realOrderId);
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
     * Cancel Mobile Express Checkout
     */
    public function cancelAction()
    {
        try {
            // if there is an order - cancel it
            $orderId = $this->_getCheckoutSession()->getLastOrderId();
            $order = ($orderId) ? Mage::getModel('sales/order')->load($orderId) : false;

            if ($order && $order->getId() && $order->getQuoteId() == $this->_getCheckoutSession()->getQuoteId()) {
                $order->cancel()->save();
                $this->_getCheckoutSession()->unsLastQuoteId()->unsLastSuccessQuoteId()->unsLastOrderId()
                    ->unsLastRealOrderId();
            }
            $this->_initToken(false);
            $this->_message($this->__('Mobile Express Checkout has been canceled.'), self::MESSAGE_STATUS_SUCCESS);
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to cancel Mobile Express Checkout.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Instantiate quote and checkout
     *
     * @throws Mage_Core_Exception
     * @return null
     */
    protected function _initCheckout()
    {
        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            Mage::throwException($this->__('Unable to initialize PayPal Mobile Express Checkout.'));
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::throwException($error);
        }
        $this->_getCheckoutSession()->setCartWasUpdated(false);

        $this->_checkout = Mage::getSingleton($this->_checkoutType, array(
            'config' => $this->_config, 'quote'  => $quote
        ));
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

    /**
     * Search for proper checkout token in request or session or (un)set specified one
     * Combined getter/setter
     *
     * @param string $setToken
     * @return Mage_Paypal_ExpressController|string
     */
    protected function _initToken($setToken = null)
    {
        if (null !== $setToken) {
            if (false === $setToken) {
                if (!$this->_getSession()->getExpressCheckoutToken()) { // security measure for avoid unset token twice
                    Mage::throwException($this->__('PayPal Mobile Express Checkout Token does not exist.'));
                }
                $this->_getSession()->unsExpressCheckoutToken();
            } else {
                $this->_getSession()->setExpressCheckoutToken($setToken);
            }
            return $this;
        }

        $setToken = $this->getRequest()->getParam('token');
        if ($setToken) {
            if ($setToken !== $this->_getSession()->getExpressCheckoutToken()) {
                Mage::throwException($this->__('Wrong PayPal Mobile Express Checkout Token specified.'));
            }
        } else {
            $setToken = $this->_getSession()->getExpressCheckoutToken();
        }
        return $setToken;
    }

    /**
     * PayPal session instance getter
     *
     * @return Mage_PayPal_Model_Session
     */
    private function _getSession()
    {
        return Mage::getSingleton('paypal/session');
    }
}
