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
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payflow Advanced Checkout Controller
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_PayflowadvancedController extends Mage_Paypal_Controller_Express_Abstract
{
    /**
     * Config mode type
     *
     * @var string
     */
    protected $_configType = 'Mage_Paypal_Model_Config';

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = Mage_Paypal_Model_Config::METHOD_PAYFLOWADVANCED;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Mage_Paypal_Model_Payflowadvanced';


    /**
     * When a customer cancel payment from payflow gateway.
     *
     * @return void
     */
    public function cancelPaymentAction()
    {
        $gotoSection = $this->_cancelPayment();
        $redirectBlock = $this->_getIframeBlock()
            ->setGotoSection($gotoSection)
            ->setTemplate('paypal/payflowadvanced/redirect.phtml');
        $this->getResponse()->setBody($redirectBlock->toHtml());
    }

    /**
     * When a customer return to website from payflow gateway.
     *
     * @return void
     */
    public function returnUrlAction()
    {
        $redirectBlock = $this->_getIframeBlock()
            ->setTemplate('paypal/payflowadvanced/redirect.phtml');

        $session = $this->_getCheckout();
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());

            if ($order && $order->getIncrementId() == $session->getLastRealOrderId()) {
                $allowedOrderStates = array(
                    Mage_Sales_Model_Order::STATE_PROCESSING,
                    Mage_Sales_Model_Order::STATE_COMPLETE
                );
                if (in_array($order->getState(), $allowedOrderStates)) {
                    $session->unsLastRealOrderId();
                    $redirectBlock->setGotoSuccessPage(true);
                } else {
                    $gotoSection = $this->_cancelPayment(
                        Mage::helper('core')
                            ->stripTags(
                                strval($this->getRequest()->getParam('RESPMSG'))
                            )
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
     *
     * @return void
     */
    public function formAction()
    {
        $this->getResponse()
            ->setBody($this->_getIframeBlock()->toHtml());
    }

    /**
     * Get response from PayPal by silent post method
     *
     * @return void
     */
    public function silentPostAction()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['INVNUM'])) {
            /** @var $paymentModel Mage_Paypal_Model_Payflowadvanced */
            $paymentModel = Mage::getModel('paypal/payflowadvanced');
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
     * @param string $errorMsg
     * @return bool|string
     */
    protected function _cancelPayment($errorMsg = '')
    {
        $gotoSection = false;
        /* @var $helper Mage_Paypal_Helper_Checkout */
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
     * @return Mage_Paypal_Block_Payflow_Advanced_Iframe
     */
    protected function _getIframeBlock()
    {
        $this->loadLayout('paypal_payflow_advanced_iframe');
        return $this->getLayout()
            ->getBlock('payflow.advanced.iframe');
    }
}
