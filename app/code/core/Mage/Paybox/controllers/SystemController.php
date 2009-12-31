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
 * @package     Mage_Paybox
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paybox System Checkout Controller
 *
 * @category   Mage
 * @package    Mage_Paybox
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paybox_SystemController extends Mage_Core_Controller_Front_Action
{
    protected $_payboxResponse = null;

    protected $_responseStatus = false;

    /**
     * seting response after returning from paybox
     *
     * @param array $response
     * @return object $this
     */
    protected function setPayboxResponse($response)
    {
        if (count($response)) {
            $this->_payboxResponse = $response;
        }
        return $this;
    }

    /**
     * Get System Model
     *
     * @return Mage_Paybox_Model_System
     */
    public function getModel()
    {
        return Mage::getSingleton('paybox/system');
    }

    /**
     * Get Checkout Singleton
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Redirect action. Redirect customer to Paybox
     *
     */
    public function redirectAction()
    {
        $session = $this->getCheckout();
        $session->setPayboxQuoteId($session->getQuoteId());

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $order->addStatusToHistory($order->getStatus(), $this->__('Customer was redirected to Paybox'));
        $order->save();

        $session->setPayboxOrderId(Mage::helper('core')->encrypt($session->getLastRealOrderId()));
        $session->setPayboxPaymentAction(
            $order->getPayment()->getMethodInstance()->getPaymentAction()
        );

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('paybox/system_redirect')
                ->setOrder($order)
                ->toHtml()
        );

        $session->unsQuoteId();
    }

    /**
     * Customer returning to this action if payment was successe
     */
    public function successAction()
    {
        $this->setPayboxResponse($this->getRequest()->getParams());
        if ($this->_checkResponse()) {

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($this->_payboxResponse['ref']);

            if (!$order->getId()) {
                Mage::throwException($this->__('There are no order.'));
            }

            if (Mage::helper('core')->decrypt($this->getCheckout()->getPayboxOrderId()) != $this->_payboxResponse['ref']) {
                Mage::throwException($this->__('Order is not match.'));
            }
            $this->getCheckout()->unsPayboxOrderId();

            if (($order->getBaseGrandTotal()*100) != $this->_payboxResponse['amount']) {
                Mage::throwException($this->__('Amount is not match.'));
            }

            if ($this->_payboxResponse['error'] == '00000') {
                $order->addStatusToHistory($order->getStatus(), $this->__('Customer successfully returned from Paybox'));

                $redirectTo = 'checkout/onepage/success';
                if ($this->getCheckout()->getPayboxPaymentAction() == Mage_Paybox_Model_System::PBX_PAYMENT_ACTION_ATHORIZE_CAPTURE) {
                    $this->getCheckout()->unsPayboxPaymentAction();
                    $order->getPayment()
                        ->getMethodInstance()
                        ->setTransactionId($this->_payboxResponse['trans']);
                    if ($this->_createInvoice($order)) {
                        $order->addStatusToHistory($order->getStatus(), $this->__('Invoice was create successfully'));
                    } else {
                        $order->addStatusToHistory($order->getStatus(), $this->__('Cann\'t create invoice'));
                        $redirectTo = '*/*/failure';
                    }
                }

                $session = $this->getCheckout();
                $session->setQuoteId($session->getPayboxQuoteId(true));
                $session->getQuote()->setIsActive(false)->save();
                $session->unsPayboxQuoteId();
            } else {
                $redirectTo = '*/*/failure';
                $order->cancel();
                $order->addStatusToHistory($order->getStatus(), $this->__('Customer was rejected by Paybox'));
            }

            $order->sendNewOrderEmail();
            $order->save();

            $this->_redirect($redirectTo);
        } else {
            $this->norouteAction();
            return;
        }
    }

    /**
     * Action when payment was declined by Paybox
     */
    public function refuseAction()
    {
        $this->setPayboxResponse($this->getRequest()->getParams());
        if ($this->_checkResponse()) {
            $this->getCheckout()->unsPayboxQuoteId();
            $this->getCheckout()->setPayboxErrorMessage('Order was canceled by Paybox');

            $order = Mage::getModel('sales/order')
                ->loadByIncrementId($this->_payboxResponse['ref']);
            $order->cancel();
            $order->addStatusToHistory($order->getStatus(), $this->__('Customer was refuse by Paybox'));
            $order->save();

            $this->_redirect('*/*/failure');
        } else {
            $this->norouteAction();
            return;
        }
    }

    /**
     * Action when customer cancele payment or press button to back to shop
     */
    public function declineAction()
    {
        $this->setPayboxResponse($this->getRequest()->getParams());
        if ($this->_checkResponse()) {

            $order = Mage::getModel('sales/order')
                ->loadByIncrementId($this->_payboxResponse['ref']);
            $order->cancel();
            $order->addStatusToHistory($order->getStatus(), $this->__('Order was canceled by customer'));
            $order->save();

            $session = $this->getCheckout();
            $session->setQuoteId($session->getPayboxQuoteId(true));
            $session->getQuote()->setIsActive(false)->save();
            $session->unsPayboxQuoteId();

            $this->_redirect('checkout/cart');
        } else {
            $this->norouteAction();
            return;
        }
    }

    /**
     * Redirect action. Redirect to Paybox using commandline mode
     *
     */
    public function commandlineAction()
    {
        $session = $this->getCheckout();
        $session->setPayboxQuoteId($session->getQuoteId());

        $order = Mage::getModel('sales/order')
            ->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        $order->addStatusToHistory(
            $order->getStatus(), $this->__('Customer was redirected to Paybox using \'command line\' mode')
        );
        $order->save();

        $session->setPayboxOrderId(Mage::helper('core')->encrypt($session->getLastRealOrderId()));
        $session->setPayboxPaymentAction(
            $order->getPayment()->getMethodInstance()->getPaymentAction()
        );

        $session->unsQuoteId();

        $payment = $order->getPayment()->getMethodInstance();
        $fieldsArr = $payment->getFormFields();
        $paramStr = '';
        foreach ($fieldsArr as $k => $v) {
            $paramStr .= $k.'='.$v.' ';
        }

        $paramStr = str_replace(';', '\;', $paramStr);
        $result = shell_exec(Mage::getBaseDir().'/'.$this->getModel()->getPayboxFile().' '.$paramStr);

        if (isset($fieldsArr['PBX_PING']) && $fieldsArr['PBX_PING'] == '1') {
            $fieldsArr['PBX_PING'] = '0';
            $fieldsArr['PBX_PAYBOX'] = trim(substr($result, strpos($result, 'http')));
            $paramStr = '';
            foreach ($fieldsArr as $k => $v) {
                $paramStr .= $k.'='.$v.' ';
            }

            $paramStr = str_replace(';', '\;', $paramStr);
            $result = shell_exec(Mage::getBaseDir().'/'.$this->getModel()->getPayboxFile().' '.$paramStr);
        }

        $this->loadLayout(false);
        $this->getResponse()->setBody($result);
        $this->renderLayout();
    }

    /**
     * Error action. If request params to Paybox has mistakes
     *
     */
    public function errorAction()
    {
        if (!$this->getCheckout()->getPayboxQuoteId()) {
            $this->norouteAction();
            return;
        }

        $session = $this->getCheckout();
        $session->setQuoteId($session->getPayboxQuoteId(true));
        $session->getQuote()->setIsActive(false)->save();
        $session->unsPayboxQuoteId();

        if (!$this->getRequest()->getParam('NUMERR')) {
            $this->norouteAction();
            return;
        }

        $this->loadLayout();

        $this->getCheckout()
            ->setPayboxErrorNumber(
                $this->getRequest()->getParam('NUMERR')
            );

        $this->renderLayout();
    }

    /**
     * Failure action.
     * Displaying information if customer was redirecting to cancel or decline actions
     *
     */
    public function failureAction()
    {
        if (!$this->getCheckout()->getPayboxErrorMessage()) {
            $this->norouteAction();
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Checking response and Paybox session variables
     *
     * @return unknown
     */
    protected function _checkResponse()
    {
        if (!$this->getCheckout()->getPayboxQuoteId()) {
            $this->norouteAction();
            return;
        }

        if (!$this->getCheckout()->getPayboxOrderId()) {
            $this->norouteAction();
            return;
        }

        if (!$this->getCheckout()->getPayboxPaymentAction()) {
            $this->norouteAction();
            return;
        }

        if (!$this->_payboxResponse) {
            return false;
        }

        //check for valid response
        if ($this->getModel()->checkResponse($this->_payboxResponse)) {
            return true;
        }

        return true;
    }

    /**
     * Creating invoice
     *
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    protected function _createInvoice(Mage_Sales_Model_Order $order)
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

}
