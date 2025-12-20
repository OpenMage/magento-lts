<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Payflow Checkout Controller
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_PayflowController extends Mage_Core_Controller_Front_Action
{
    /**
     * When a customer cancel payment from payflow gateway.
     */
    public function cancelPaymentAction()
    {
        $gotoSection = $this->_cancelPayment();
        $redirectBlock = $this->_getIframeBlock()
            ->setGotoSection($gotoSection)
            ->setTemplate('paypal/payflowlink/redirect.phtml');
        $this->getResponse()->setBody($redirectBlock->toHtml());
    }

    /**
     * When a customer return to website from payflow gateway.
     */
    public function returnUrlAction()
    {
        $redirectBlock = $this->_getIframeBlock()
            ->setTemplate('paypal/payflowlink/redirect.phtml');

        $session = $this->_getCheckout();
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());

            if ($order && $order->getIncrementId() == $session->getLastRealOrderId()) {
                $allowedOrderStates = [
                    Mage_Sales_Model_Order::STATE_PROCESSING,
                    Mage_Sales_Model_Order::STATE_COMPLETE,
                ];
                if (in_array($order->getState(), $allowedOrderStates)) {
                    $session->unsLastRealOrderId();
                    $redirectBlock->setGotoSuccessPage(true);
                } else {
                    $gotoSection = $this->_cancelPayment(
                        Mage::helper('core')
                            ->stripTags(
                                (string) $this->getRequest()->getParam('RESPMSG'),
                            ),
                    );
                    $redirectBlock->setGotoSection($gotoSection);
                    $redirectBlock->setErrorMsg($this->__('Payment has been declined. Please try again.'));
                }
            }
        }

        $this->getResponse()->setBody($redirectBlock->toHtml());
    }

    /**
     * Submit transaction to Payflow getaway into iframe
     */
    public function formAction()
    {
        $this->getResponse()->setHeader('P3P', 'CP="CAO PSA OUR"');
        $this->getResponse()
            ->setBody($this->_getIframeBlock()->toHtml());
    }

    /**
     * Get response from PayPal by silent post method
     */
    public function silentPostAction()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['INVNUM'])) {
            /** @var Mage_Paypal_Model_Payflowlink $paymentModel */
            $paymentModel = Mage::getModel('paypal/payflowlink');
            try {
                $paymentModel->process($data);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    /**
     * Cancel order, return quote to customer
     *
     * @param  string       $errorMsg
     * @return false|string
     */
    protected function _cancelPayment($errorMsg = '')
    {
        $gotoSection = false;
        /** @var Mage_Paypal_Helper_Checkout $helper */
        $helper = Mage::helper('paypal/checkout');
        $helper->cancelCurrentOrder($errorMsg);
        if ($helper->restoreQuote()) {
            return 'payment';
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
     * @return Mage_Paypal_Block_Payflow_Link_Iframe
     */
    protected function _getIframeBlock()
    {
        $this->loadLayout('paypal_payflow_link_iframe');
        /** @var Mage_Paypal_Block_Payflow_Link_Iframe $block */
        $block = $this->getLayout()->getBlock('payflow.link.iframe');
        return $block;
    }
}
