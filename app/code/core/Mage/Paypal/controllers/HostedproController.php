<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Hosted Pro Checkout Controller
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_HostedproController extends Mage_Core_Controller_Front_Action
{
    /**
     * When a customer return to website from gateway.
     */
    public function returnAction()
    {
        $session = $this->_getCheckout();
        //TODO: some actions with order
        if ($session->getLastRealOrderId()) {
            $this->_redirect('checkout/onepage/success');
        }
    }

    /**
     * When a customer cancel payment from gateway.
     */
    public function cancelAction()
    {
        $gotoSection = $this->_cancelPayment();
        $redirectBlock = $this->_getIframeBlock()
            ->setGotoSection($gotoSection)
            ->setTemplate('paypal/hss/redirect.phtml');
        //TODO: clarify return logic whether customer will be returned in iframe or in parent window
        $this->getResponse()->setBody($redirectBlock->toHtml());
    }

    /**
     * Cancel order, return quote to customer
     *
     * @param string $errorMsg
     * @return false|string
     */
    protected function _cancelPayment($errorMsg = '')
    {
        $gotoSection = false;
        /** @var Mage_Paypal_Helper_Checkout $helper */
        $helper = Mage::helper('paypal/checkout');
        $helper->cancelCurrentOrder($errorMsg);
        if ($helper->restoreQuote()) {
            $gotoSection = 'payment';
        }

        return $gotoSection;
    }

    /**
     * Get frontend checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get iframe block
     *
     * @return Mage_Paypal_Block_Hosted_Pro_Iframe
     */
    protected function _getIframeBlock()
    {
        $this->loadLayout('paypal_hosted_pro_iframe');
        /** @var Mage_Paypal_Block_Hosted_Pro_Iframe $block */
        $block = $this->getLayout()->getBlock('hosted.pro.iframe');
        return $block;
    }
}
