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
 * @package     Mage_Chronopay
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Chronopay Standard Front Controller
 *
 * @category   Mage
 * @package    Mage_Chronopay
 * @name       Mage_Chronopay_StandardController
 * @author     Magento Core Team <core@magentocommerce.com>
*/
class Mage_Chronopay_StandardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Return debug flag
     *
     *  @return  boolean
     */
    public function getDebug ()
    {
        return Mage::getSingleton('chronopay/config')->getDebug();
    }

    /**
     *  Get order
     *
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder ()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }

    /**
     * When a customer chooses Chronopay on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setChronopayStandardQuoteId($session->getQuoteId());

        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('chronopay')->__('Customer was redirected to Chronopay')
        );
        $order->save();

        $this->getResponse()
            ->setBody($this->getLayout()
                ->createBlock('chronopay/standard_redirect')
                ->setOrder($order)
                ->toHtml());

        $session->unsQuoteId();
    }

    /**
     *  Success response from Chronopay
     */
    public function  successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getChronopayStandardQuoteId());
        $session->unsChronopayStandardQuoteId();

        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('chronopay')->__('Customer successfully returned from Chronopay')
        );

        $order->save();
        $this->_redirect('checkout/onepage/success');
    }


    /**
     *  Notification Action from ChronoPay
     */
    public function notifyAction ()
    {
        $postData = $this->getRequest()->getPost();

        if (!count($postData)) {
            $this->norouteAction();
            return;
        }

        if ($this->getDebug()) {
            $debug = Mage::getModel('chronopay/api_debug');
            if (isset($postData['cs2']) && $postData['cs2'] > 0) {
                $debug->setId($postData['cs2']);
            }
            $debug->setResponseBody(print_r($postData,1))->save();
        }

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId(Mage::helper('core')->decrypt($postData['cs1']));
        if ($order->getId()) {
            $result = $order->getPayment()->getMethodInstance()->setOrder($order)->validateResponse($postData);

            if ($result instanceof Exception) {
                if ($order->getId()) {
                    $order->addStatusToHistory(
                        $order->getStatus(),
                        $result->getMessage()
                    );
                    $order->cancel();
                }
                Mage::throwException($result->getMessage());
                return;
            }

            $order->sendNewOrderEmail();

            $order->getPayment()->getMethodInstance()->setTransactionId($postData['transaction_id']);

            if ($this->saveInvoice($order)) {
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            }
            $order->save();
        }
    }

    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
    protected function saveInvoice (Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice()) {
            $invoice = $order->prepareInvoice();
            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
               ->addObject($invoice)
               ->addObject($invoice->getOrder())
               ->save();
            return true;
        }

        return false;
    }

    /**
     *  Failure response from Chronopay
     */
    public function failureAction ()
    {
        $errorMsg = Mage::helper('chronopay')->__('There was an error occurred during paying process.');

        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }

        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            $order->addStatusToHistory($order->getStatus(), $errorMsg);
            $order->cancel();
            $order->save();
        }

        $this->loadLayout();
        $this->renderLayout();

        Mage::getSingleton('checkout/session')->unsLastRealOrderId();
    }

}
