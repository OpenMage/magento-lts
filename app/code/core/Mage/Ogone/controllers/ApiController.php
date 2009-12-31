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
 * @package     Mage_Ogone
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Ogone Api Controller
 */
class Mage_Ogone_ApiController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get singleton with Checkout by Ogone Api
     *
     * @return Mage_Ogone_Model_Api
     */
    protected function _getApi()
    {
        return Mage::getSingleton('ogone/api');
    }

    /**
     * Return order instance loaded by increment id'
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if (empty($this->_order)) {
            $orderId = $this->getRequest()->getParam('orderID');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($orderId);
        }
        return $this->_order;
    }

    /**
     * Validation of incoming Ogone data
     *
     * @return bool
     */
    protected function _validateOgoneData()
    {
        if ($this->_getApi()->getDebug()) {
            $debug = Mage::getModel('ogone/api_debug')
                ->setDir('in')
                ->setUrl($this->getRequest()->getPathInfo())
                ->setData('data',http_build_query($this->getRequest()->getParams()))
                ->save();
        }

        $params = $this->getRequest()->getParams();
        $secureKey = $this->_getApi()->getConfig()->getShaInCode();
        $secureSet = $this->_getSHAInSet($params, $secureKey);

        if (Mage::helper('ogone')->shaCryptValidation($secureSet, $params['SHASIGN'])!=true) {
            $this->_getCheckout()->addError($this->__('Hash is not valid'));
            return false;
        }

        $order = $this->_getOrder();
        if (!$order->getId()){
            $this->_getCheckout()->addError($this->__('Order is not valid'));
            return false;
        }

        return true;
    }

    /**
     * Load place from layout to make POST on ogone
     */
    public function placeformAction()
    {
        $lastIncrementId = $this->_getCheckout()->getLastRealOrderId();
        if ($lastIncrementId) {
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($lastIncrementId);
            if ($order->getId()) {
                $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, Mage_Ogone_Model_Api::PENDING_OGONE_STATUS, Mage::helper('ogone')->__('Start ogone processing'));
                $order->save();

                if ($this->_getApi()->getDebug()) {
                    $debug = Mage::getModel('ogone/api_debug')
                        ->setDir('out')
                        ->setUrl($this->getRequest()->getPathInfo())
                        ->setData('data', http_build_query($this->_getApi()->getFormFields($order)))
                        ->save();
                }
            }
        }

        $this->_getCheckout()->getQuote()->setIsActive(false)->save();
        $this->_getCheckout()->setOgoneQuoteId($this->_getCheckout()->getQuoteId());
        $this->_getCheckout()->setOgoneLastSuccessQuoteId($this->_getCheckout()->getLastSuccessQuoteId());
        $this->_getCheckout()->clear();

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Display our pay page, need to ogone payment with external pay page mode     *
     */
    public function paypageAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Action to control postback data from ogone
     *
     */
    public function postBackAction()
    {
        if (!$this->_validateOgoneData()) {
            $this->getResponse()->setHeader("Status", "404 Not Found");
        }

        $this->_offlineProcess();
    }

    /**
     * Action to process ogone offline data
     *
     */
    public function offlineProcessAction()
    {
        if (!$this->_validateOgoneData()) {
            $this->getResponse()->setHeader("Status","404 Not Found");
        }
        $this->_offlineProcess();
    }

    /**
     * Made offline ogone data processing, depending of incoming statuses
     */
    protected function _offlineProcess()
    {
        $status = $this->getRequest()->getParam('STATUS');
        switch ($status) {
            case Mage_Ogone_Model_Api::OGONE_AUTHORIZED :
            case Mage_Ogone_Model_Api::OGONE_AUTH_PROCESSING:
            case Mage_Ogone_Model_Api::OGONE_PAYMENT_REQUESTED_STATUS :
                $this->_acceptProcess();
                break;
            case Mage_Ogone_Model_Api::OGONE_AUTH_REFUZED:
            case Mage_Ogone_Model_Api::OGONE_PAYMENT_INCOMPLETE:
            case Mage_Ogone_Model_Api::OGONE_TECH_PROBLEM:
                $this->_declineProcess();
                break;
            case Mage_Ogone_Model_Api::OGONE_AUTH_UKNKOWN_STATUS:
            case Mage_Ogone_Model_Api::OGONE_PAYMENT_UNCERTAIN_STATUS:
                $this->_exceptionProcess();
                break;
            default:
                $this->_cancelProcess();
        }
    }

    /**
     * when payment gateway accept the payment, it will land to here
     * need to change order status as processed ogone
     * update transaction id
     *
     */
    public function acceptAction()
    {
        if (!$this->_validateOgoneData()) {
            $this->_redirect('checkout/cart');
            return;
        }
        $this->_acceptProcess();
    }

    /**
     * Process success action by accept url
     */
    protected function _acceptProcess()
    {
        $params = $this->getRequest()->getParams();
        $order = $this->_getOrder();

        $this->_getCheckout()->setLastSuccessQuoteId($this->_getCheckout()->getOgoneLastSuccessQuoteId());

        $this->_prepareCCInfo($order, $params);
        $order->getPayment()->setLastTransId($params['PAYID']);

        try{
            if ($this->_getApi()->getPaymentAction()==Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE) {
                $this->_processDirectSale();
            } else {
                $this->_processAuthorize();
            }
        }catch(Exception $e) {
            $this->_getCheckout()->addError(Mage::helper('ogone')->__('Order can\'t save'));
            $this->_redirect('checkout/cart');
            return;
        }
    }

    /**
     * Process Configured Payment Action: Direct Sale, create invoce if state is Pending
     *
     */
    protected function _processDirectSale()
    {
        $order = $this->_getOrder();
        $status = $this->getRequest()->getParam('STATUS');
        try{
            if ($status ==  Mage_Ogone_Model_Api::OGONE_AUTH_PROCESSING) {
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, Mage_Ogone_Model_Api::WAITING_AUTHORIZATION, Mage::helper('ogone')->__('Authorization Waiting from Ogone'));
                $order->save();
            }elseif ($order->getState()==Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
                if ($status ==  Mage_Ogone_Model_Api::OGONE_AUTHORIZED) {
                    if ($order->getStatus() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
                        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, Mage_Ogone_Model_Api::PROCESSING_OGONE_STATUS, Mage::helper('ogone')->__('Processed by Ogone'));
                    }
                } else {
                    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, Mage_Ogone_Model_Api::PROCESSED_OGONE_STATUS, Mage::helper('ogone')->__('Processed by Ogone'));
                }

                if (!$order->getInvoiceCollection()->getSize()) {
                    $invoice = $order->prepareInvoice();
                    $invoice->register();
                    $invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_PAID);
                    $invoice->getOrder()->setIsInProcess(true);

                    $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();
                    $order->sendNewOrderEmail();
                }
            } else {
                $order->save();
            }
            $this->_redirect('checkout/onepage/success');
            return;
        } catch (Exception $e) {
            $this->_getCheckout()->addError(Mage::helper('ogone')->__('Order can\'t save'));
            $this->_redirect('checkout/cart');
            return;
        }
    }

    /**
     * Process Configured Payment Actions: Authorized, Default operation
     * just place order
     */
    protected function _processAuthorize()
    {
        $order = $this->_getOrder();
        $status = $this->getRequest()->getParam('STATUS');
        try {
            if ($status ==  Mage_Ogone_Model_Api::OGONE_AUTH_PROCESSING) {
                $order->setState(Mage_Sales_Model_Order::STATE_NEW, Mage_Ogone_Model_Api::WAITING_AUTHORIZATION, Mage::helper('ogone')->__('Authorization Waiting from Ogone'));
            } else {
                $order->setState(Mage_Sales_Model_Order::STATE_NEW, Mage_Ogone_Model_Api::PROCESSED_OGONE_STATUS, Mage::helper('ogone')->__('Processed by Ogone'));
                $order->sendNewOrderEmail();
            }
            $order->save();
            $this->_redirect('checkout/onepage/success');
            return;
        } catch(Exception $e) {
            $this->_getCheckout()->addError(Mage::helper('ogone')->__('Order can\'t save'));
            $this->_redirect('checkout/cart');
            return;
        }
    }

    /**
     * We get some CC info from ogone, so we must save it
     *
     * @param Mage_Sales_Model_Order $order
     * @param array $ccInfo
     *
     * @return Mage_Ogone_ApiController
     */
    protected function _prepareCCInfo($order, $ccInfo)
    {
        $order->getPayment()->setCcOwner($ccInfo['CN']);
        $order->getPayment()->setCcNumberEnc($ccInfo['CARDNO']);
        $order->getPayment()->setCcLast4(substr($ccInfo['CARDNO'], -4));
        $order->getPayment()->setCcExpMonth(substr($ccInfo['ED'], 0, 2));
        $order->getPayment()->setCcExpYear(substr($ccInfo['ED'], 2, 2));
        return $this;
    }


    /**
     * the payment result is uncertain
     * exception status can be 52 or 92
     * need to change order status as processing ogone
     * update transaction id
     *
     */
    public function exceptionAction()
    {
        if (!$this->_validateOgoneData()) {
            $this->_redirect('checkout/cart');
            return;
        }
        $this->_exceptionProcess();
    }

    /**
     * Process exception action by ogone exception url
     */
    public function _exceptionProcess()
    {
        $params = $this->getRequest()->getParams();
        $order = $this->_getOrder();

        $exception = '';
        switch($params['STATUS']) {
            case Mage_Ogone_Model_Api::OGONE_PAYMENT_UNCERTAIN_STATUS :
                $exception = Mage::helper('ogone')->__('Payment uncertain: A technical problem arose during payment process, giving unpredictable result');
                break;
            case Mage_Ogone_Model_Api::OGONE_AUTH_UKNKOWN_STATUS :
                $exception = Mage::helper('ogone')->__('Authorisation not known: A technical problem arose during authorisation process, giving unpredictable result');
                break;
            default:
                $exception = '';
        }

        if (!empty($exception)) {
            try{
                $this->_prepareCCInfo($order, $params);
                $order->getPayment()->setLastTransId($params['PAYID']);
                $order->addStatusToHistory(Mage_Ogone_Model_Api::PROCESSING_OGONE_STATUS, $exception);
                $order->save();
                $this->_getCheckout()->addError($exception);
            }catch(Exception $e) {
                $this->_getCheckout()->addError(Mage::helper('ogone')->__('Order can not be save for system reason'));
            }
        } else {
            $this->_getCheckout()->addError(Mage::helper('ogone')->__('Exception not defined'));
        }

        $this->_redirect('checkout/onepage/success');
        return;
    }

    /**
     * when payment got decline
     * need to change order status to cancelled
     * take the user back to shopping cart
     *
     */
    public function declineAction()
    {
        if (!$this->_validateOgoneData()) {
            $this->_redirect('checkout/cart');
            return;
        }
        $this->_getCheckout()->setQuoteId($this->_getCheckout()->getOgoneQuoteId());
        $this->_declineProcess();
        return $this;
    }

    /**
     * Process decline action by ogone decline url
     */
    protected function _declineProcess()
    {
        $status     = Mage_Ogone_Model_Api::DECLINE_OGONE_STATUS;
        $comment    = Mage::helper('ogone')->__('Declined Order on ogone side');
        $this->_getCheckout()->addError(Mage::helper('ogone')->__('Payment transaction has been declined.'));
        $this->_cancelOrder($status, $comment);
    }

    /**
     * when user cancel the payment
     * change order status to cancelled
     * need to rediect user to shopping cart
     *
     * @return Mage_Ogone_ApiController
     */
    public function cancelAction()
    {
        if (!$this->_validateOgoneData()) {
            $this->_redirect('checkout/cart');
            return;
        }
        $this->_getCheckout()->setQuoteId($this->_getCheckout()->getOgoneQuoteId());
        $this->_cancelProcess();
        return $this;
    }

    /**
     * Process cancel action by cancel url
     *
     * @return Mage_Ogone_ApiController
     */
    public function _cancelProcess()
    {
        $status     = Mage_Ogone_Model_Api::CANCEL_OGONE_STATUS;
        $comment    = Mage::helper('ogone')->__('Order canceled on ogone side');
        $this->_cancelOrder($status, $comment);
        return $this;
    }

    /**
     * Cancel action, used for decline and cancel processes
     *
     * @return Mage_Ogone_ApiController
     */
    protected function _cancelOrder($status, $comment='')
    {
        $order = $this->_getOrder();
        try{
            $order->cancel();
            $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, $status, $comment);
            $order->save();
        }catch(Exception $e) {
            $this->_getCheckout()->addError(Mage::helper('ogone')->__('Order can not be canceled for system reason'));
        }

        $this->_redirect('checkout/cart');
        return $this;
    }

    /**
     * Return set of data which is ready for SHA crypt
     *
     * @param array $data
     * @param string $key
     *
     * @return string
     */
    protected function _getSHAInSet($params, $key)
    {
        return $this->getRequest()->getParam('orderID') .
               $this->getRequest()->getParam('currency') .
               $this->getRequest()->getParam('amount') .
               $this->getRequest()->getParam('PM') .
               $this->getRequest()->getParam('ACCEPTANCE') .
               $this->getRequest()->getParam('STATUS') .
               $this->getRequest()->getParam('CARDNO') .
               $this->getRequest()->getParam('PAYID') .
               $this->getRequest()->getParam('NCERROR') .
               $this->getRequest()->getParam('BRAND') . $key;
    }
}
