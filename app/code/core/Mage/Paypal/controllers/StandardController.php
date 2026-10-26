<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Paypal Standard Checkout Controller
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_StandardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Get order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Send expire header to ajax response
     *
     * @SuppressWarnings("PHPMD.ExitExpression")
     */
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with PayPal standard order transaction information
     *
     * @return Mage_Paypal_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('paypal/standard');
    }

    /**
     * When a customer chooses Paypal on Checkout/Payment page
     * @return void
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setPaypalStandardQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('paypal/standard_redirect')->toHtml());
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }

    /**
     * When a customer cancel payment from paypal.
     * @return void
     */
    public function cancelAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getPaypalStandardQuoteId(true));

        /**
         * This URL is the cancel return handed to PayPal and is entered as a cross-site GET, so it
         * cannot carry a form key. Only the nonce minted for the last placed PayPal Standard order
         * may cancel that order, otherwise any page could cancel the customer's last order.
         */
        /** @var Mage_Paypal_Helper_Checkout $helper */
        $helper = Mage::helper('paypal/checkout');
        $nonce = $this->getRequest()->getParam(Mage_Paypal_Helper_Checkout::CANCEL_NONCE_PARAM);
        if ($session->getLastRealOrderId()
            && $helper->canCancelLastRealOrder($nonce, Mage_Paypal_Model_Config::METHOD_WPS)
        ) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->save();
            }

            $helper->restoreQuote();
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * when paypal returns
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get validation from the IPN.
     * @return void
     */
    public function successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getPaypalStandardQuoteId(true));
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
        $this->_redirect('checkout/onepage/success', ['_secure' => true]);
    }
}
