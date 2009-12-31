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
 * @package     Mage_Cybermut
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cybermut Payment Front Controller
 *
 * @category   Mage
 * @package    Mage_Cybermut
 * @author	   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cybermut_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Get order
     *
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }

    /**
     * When a customer chooses Cybermut on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setCybermutPaymentQuoteId($session->getQuoteId());

        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('cybermut')->__('Customer was redirected to Cybermut')
        );
        $order->save();

        $this->getResponse()
            ->setBody($this->getLayout()
                ->createBlock('cybermut/redirect')
                ->setOrder($order)
                ->toHtml());

        $session->unsQuoteId();
    }

    /**
     *  Cybermut response router
     *
     */
    public function notifyAction()
    {
        $model = Mage::getModel('cybermut/payment');

        if (!$this->getRequest()->isPost()) {
            $model->generateErrorResponse();
        }

        $postData = $this->getRequest()->getPost();
        $returnedMAC = $this->getRequest()->getPost('MAC');
        $correctMAC = $model->getResponseMAC($postData);

        if ($model->getConfigData('debug_flag')) {
            Mage::getModel('cybermut/api_debug')
                ->setResponseBody(print_r($postData ,1))
                ->save();
        }

        $order = Mage::getModel('sales/order')
            ->loadByIncrementId($this->getRequest()->getPost('reference'));

        if (!$order->getId()) {
            $model->generateErrorResponse();
        }

        if ($returnedMAC == $correctMAC && $model->isSuccessfulPayment($this->getRequest()->getPost('code-retour'))) {
            $order->addStatusToHistory($model->getConfigData('order_status'));
            $order->sendNewOrderEmail();
            if ($this->saveInvoice($order)) {
//                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            }
            $order->save();
            $model->generateSuccessResponse();
        } else {
            $order->addStatusToHistory(
                $order->getStatus(),
                Mage::helper('cybermut')->__('Returned MAC is invalid. Order cancelled.')
            );
            $order->cancel();
            $order->save();
            $model->generateErrorResponse();
        }
    }

    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
    protected function saveInvoice(Mage_Sales_Model_Order $order)
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
     *  Success payment page
     */
    public function successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getCybermutPaymentQuoteId());
        $session->unsCybermutPaymentQuoteId();

        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('cybermut')->__('Customer successfully returned from Cybermut')
        );

        $order->save();
        $this->_redirect('checkout/onepage/success');
    }

    /**
     *  Failure payment page
     */
    public function errorAction()
    {
        $errorMsg = Mage::helper('cybermut')->__(' There was an error occurred during paying process.');

        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }
        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            $order->addStatusToHistory(
                $order->getStatus(),
                Mage::helper('cybermut')->__('Customer returned from Cybermut.') . $errorMsg
            );
            $order->cancel();
            $order->save();
        }

        $this->loadLayout();
        $this->renderLayout();
        Mage::getSingleton('checkout/session')->unsLastRealOrderId();
    }
}
