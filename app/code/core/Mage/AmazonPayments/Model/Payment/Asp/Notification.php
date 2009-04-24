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
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AmazonPayments ASP notification Model
 *
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Model_Payment_Asp_Notification extends Varien_Object
{
    /*
     * payment Model
     */
    protected $_payment;

    /**
     * Set payment Model
     *
     * @param Mage_AmazonPayments_Model_Payment_Asp $payment
     * @return Mage_AmazonPayments_Model_Payment_Asp_Notification
     */
    public function setPayment($payment)
    {
        $this->_payment = $payment;
        return $this;
    }

    /**
     * Get payment Model
     *
     * @return Mage_AmazonPayments_Model_Payment_Asp
     */
    public function getPayment()
    {
        return $this->_payment;
    }

    /**
     * process notification request
     *
     * @param array $requestParams
     */
    public function process($requestParams)
    {
        $request = $this->getPayment()->getApi()->processNotification($requestParams);

        if ($request->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_CANCEL_TRANSACTION) {
            return true;
        }

        $order = $this->_getRequestOrder($request);
        switch ($request->getStatus()) {
            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_CANCEL_CUSTOMER:
                $this->_processCancel($request, $order);
                break;
            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_RESERVE_SUCCESSFUL:
                $this->_processReserveSuccess($request, $order);
                break;
            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_PAYMENT_INITIATED:
                $this->_processPaymetInitiated($request, $order);
                break;
            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_PAYMENT_SUCCESSFUL:
                $this->_processPaymentSuccessful($request, $order);
                break;
            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_PAYMENT_FAILED:
                $this->_processPaymentFailed($request, $order);
                break;
            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_REFUND_SUCCESSFUL:
                $this->_processRefundSuccessful($request, $order);
                break;
            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_REFUND_FAILED:
                $this->_processRefundFailed($request, $order);
                break;
            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_SYSTEM_ERROR:
                $this->_processSystemError($request, $order);
                break;
        }
        $order->save();
    }

    /**
     * When a customer cancel payment 
     */
    protected function _processCancel($request, $order)
    {
        if ($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED) {
            $order->addStatusToHistory(
               $order->getStatus(),
               Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed cancelation.')
            );
            return true;
        }

        if ($order->getState() == Mage_Sales_Model_Order::STATE_NEW) {
            $order->addStatusToHistory(
               $order->getStatus(),
               Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed cancelation.')
            )->cancel();
            return true;
        }

        $this->_errorViolationSequenceStates($request, $order);
    }

    /**
     * When a authorize payment 
     */
    protected function _processReserveSuccess($request, $order)
    {
        if ($order->getState() != Mage_Sales_Model_Order::STATE_NEW) {
            $this->_errorViolationSequenceStates($request, $order);
        }

        $order->getPayment()->setCcTransId($request->getTransactionId());

        $order->setState(
            Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
            'pending_amazon_asp',
            Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed amount authorization.'),
            $notified = false
        );

        return true;
    }

    /**
     * When a initiation capture payment  
     */
    protected function _processPaymetInitiated($request, $order)
    {
        if ($order->getState() != Mage_Sales_Model_Order::STATE_NEW &&
            $order->getState() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
            $this->_errorViolationSequenceStates($request, $order);
        }

        $order->setState(
            Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
            'pending_amazon_asp',
            Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed capture initiation.'),
            $notified = false
        );

        return true;
    }

    /**
     * When a capture payment  
     */
    protected function _processPaymentSuccessful($request, $order)
    {
        if ($order->getState() != Mage_Sales_Model_Order::STATE_NEW &&
            $order->getState() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT &&
            $order->getState() != Mage_Sales_Model_Order::STATE_PROCESSING) {
            $this->_errorViolationSequenceStates($request, $order);
        }

        $msg = '';

        if (!$invoice = $this->_getOrderInvoice($order)) {

            $orderAmount = Mage::app()->getStore()->roundPrice($order->getBaseGrandTotal());
            $requestAmount = Mage::app()->getStore()->roundPrice($request->getAmount());
            if ($orderAmount != $requestAmount) {
                $this->_error(
                    Mage::helper('amazonpayments')->__('Amazon Simple Pay service capture confirmation error: confirmation request amount not equal to the amount of order.'),
                    $request,
                    $order
                );
            }

            $invoice = $order->prepareInvoice();
            $invoice->register()->pay();
            $invoice->addComment(Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed payment capture. Invoice created automatically.'));
            $invoice->setTransactionId($request->getTransactionId());

            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();

            $msg = $msg . Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed payment capture. Invoice %s was automatically created after confirmation.', $invoice->getIncrementId());

        } else {

            if ($invoice->getTransactionId() != $request->getTransactionId()) {
                $this->_error(
                    Mage::helper('amazonpayments')->__('Amazon Simple Pay service capture confirmation error: existing transaction ID doesn\'t match transaction ID in the confirmation request.'),
                    $request,
                    $order
                );
            }

            $invoiceAmount = Mage::app()->getStore()->roundPrice($invoice->getGrandTotal());
            $requestAmount = Mage::app()->getStore()->roundPrice($request->getAmount());
            if ($invoiceAmount != $requestAmount) {
                $this->_error(
                    Mage::helper('amazonpayments')->__('Amazon Simple Pay service capture confirmation error: amount in the existing invoice doensn\'t match the amount in confirmation request.'),
                    $request,
                    $order
                );
            }

            switch ($invoice->getState())
            {
                case Mage_Sales_Model_Order_Invoice::STATE_OPEN:
                    $invoice->addComment(Mage::helper('amazonpayments')->__('Amazon Simple Pay service capture confirmation. Invoice was captured automatically.'));
                    $invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_PAID)->save();
                    $msg = $msg . Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed capture for invoice %s. Invoice automatically captured.', $invoice->getIncrementId());
                    break;

                case Mage_Sales_Model_Order_Invoice::STATE_PAID:
                    $invoice->addComment(Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed capture'));
                    $msg = $msg . Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed capture for invoice %s', $invoice->getIncrementId());
                    break;
            }

        }

        $order->getPayment()->getLastTransId($request->getTransactionId());
        $order->addStatusToHistory($order->getStatus(), $msg);
        $order->setState(
            Mage_Sales_Model_Order::STATE_PROCESSING,
            true,
            Mage::helper('amazonpayments')->__('Payment was authorized and captured successfully'),
            $notified = true
        );

        return true;
    }

    /**
     * When a failed capture payment  
     */
    protected function _processPaymentFailed($request, $order)
    {
        if ($order->getState() != Mage_Sales_Model_Order::STATE_NEW &&
            $order->getState() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
            $this->_errorViolationSequenceStates($request, $order);
        }

        $order->setState(
            Mage_Sales_Model_Order::STATE_CANCELED,
            true,
            Mage::helper('amazonpayments')->__('Amazon Simple Pay service payment confirmation failed'),
            $notified = false
        );

        return true;
    }

    /**
     * When a refund payment  
     */
    protected function _processRefundSuccessful($request, $order)
    {
        if ($order->getState() != Mage_Sales_Model_Order::STATE_PROCESSING &&
            $order->getState() != Mage_Sales_Model_Order::STATE_CLOSED &&
            $order->getState() != Mage_Sales_Model_Order::STATE_COMPLETE) {
            $this->_errorViolationSequenceStates($request, $order);
        }

        $msg = '';

        if (!$creditmemo = $this->_getOrderCreditmemo($order)) {

            $orderAmount = Mage::app()->getStore()->roundPrice($order->getBaseGrandTotal());
            $requestAmount = Mage::app()->getStore()->roundPrice($request->getAmount());
            if ($orderAmount != $requestAmount || $order->getBaseTotalRefunded() > 0) {
                $this->_error(
                    Mage::helper('amazonpayments')->__('Amazon Simple Pay service refund confirmation error: confirmation request amount doesn\'t match the order amount.'),
                    $request,
                    $order
                );
            }

            if ($creditmemo = $this->_initCreditmemo($order)) {
                $creditmemo->setTransactionId($request->getTransactionId());
                $creditmemo->addComment(Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed payment refund. Creditmemo %s was automatically created after confirmation.', $creditmemo->getIncrementId()));
                $creditmemo->register();

                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($creditmemo)
                    ->addObject($creditmemo->getOrder());
                if ($creditmemo->getInvoice()) {
                    $transactionSave->addObject($creditmemo->getInvoice());
                }
                $transactionSave->save();

               $msg = $msg . Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed payment refund. Credit memo created automatically.', $creditmemo->getIncrementId());
            }

        } else {

            if ($creditmemo->getTransactionId() != $request->getTransactionId()) {
                $this->_error(
                    Mage::helper('amazonpayments')->__('Amazon Simple Pay service refund confirmation error: transaction ID in the existing creditmemo doesn\'t match the transaction ID in the confirmation request.'),
                    $request,
                    $order
                );
            }

            $creditmemoAmount = Mage::app()->getStore()->roundPrice($creditmemo->getBaseGrandTotal());
            $requestAmount = Mage::app()->getStore()->roundPrice($request->getAmount());
            if ($creditmemoAmount != $requestAmount) {
                $this->_error(
                    Mage::helper('amazonpayments')->__('Amazon Simple Pay service refund confirmation error: amount in the existing creditmemo doensn\'t match the amount in confirmation request.'),
                    $request,
                    $order
                );
            }

            switch ($creditmemo->getState())
            {
                case Mage_Sales_Model_Order_Creditmemo::STATE_OPEN:
                    $creditmemo->addComment(Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed refund. Creditmemo processed automatically.'));
                    $creditmemo->setState(Mage_Sales_Model_Order_Creditmemo::STATE_REFUNDED)->save();
                    $msg = $msg . Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed refunded creditmemo %s. Creditmemo processed automatically.', $creditmemo->getIncrementId());
                    break;

                case Mage_Sales_Model_Order_Creditmemo::STATE_REFUNDED:
                    $creditmemo->addComment(Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed refund.'));
                    $msg = $msg . Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed refund for creditmemo %s.', $creditmemo->getIncrementId());
                    break;
            }

        }

        $order->addStatusToHistory($order->getStatus(), $msg);

        return true;
    }

    /**
     * When a failed refund payment  
     */
    protected function _processRefundFailed($request, $order)
    {
        $order->setState(
            $order->getState(),
            true,
            Mage::helper('amazonpayments')->__('Amazon Simple Pay service payment confirmation failed'),
            $notified = false
        );

        return true;
    }

    /**
     * When a Amazon Simple Pay system error  
     */
    protected function _processSystemError($request, $order)
    {
        $order->setState(
            Mage_Sales_Model_Order::STATE_CANCELED,
            true,
            Mage::helper('amazonpayments')->__('Amazon Simple Pay service is not available. Payment was not processed.'),
            $notified = false
        );

        return true;
    }

    /**
     * Return order model for current request
     *
     * @param array $request
     * @return Mage_Sales_Model_Order
     */
    protected function _getRequestOrder($request)
    {
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($request->getReferenceId());

        if ($order->isEmpty()) {
            $this->_error(
               Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmation error: order specified in the IPN request can not be found'),
               $request
            );
        }

        if ($order->getPayment()->getMethodInstance()->getCode() != $this->getPayment()->getCode()) {
            $this->_error(
               Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmation error: payment method in the order is not Amazon Simple Pay'),
               $request
            );
        }

        if ($order->getBaseCurrency()->getCurrencyCode() != $request->getCurrencyCode()) {
            $this->_error(
               Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmation error: order currency does not match the currency of the IPN request'),
               $request
            );
        }

        return $order;
    }

    /**
     * Return invoice model for current order
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _getOrderInvoice($order)
    {
        foreach ($order->getInvoiceCollection() as $orderInvoice) {
            if ($orderInvoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_PAID ||
                $orderInvoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_OPEN) {
                return $orderInvoice;
            }
        }

        return false;
    }

    /**
     * Create and return creditmemo for current order
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    protected function _initCreditMemo($order)
    {
        $invoice = $this->_getOrderInvoice($order);

        if (!$invoice) {
            return false;
        }

        $convertor  = Mage::getModel('sales/convert_order');
        $creditmemo = $convertor->toCreditmemo($order)->setInvoice($invoice);

        foreach ($invoice->getAllItems() as $invoiceItem) {
            $orderItem = $invoiceItem->getOrderItem();
            $item = $convertor->itemToCreditmemoItem($orderItem);
            $item->setQty($orderItem->getQtyToRefund());
            $creditmemo->addItem($item);
        }

        $creditmemo->setShippingAmount($invoice->getShippingAmount());
        $creditmemo->collectTotals();
        Mage::register('current_creditmemo', $creditmemo);

        return $creditmemo;
    }

    /**
     * Return creditmemo model for current order
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    protected function _getOrderCreditmemo($order)
    {
        foreach ($order->getCreditmemosCollection() as $orderCreditmemo) {
            if ($orderCreditmemo->getState() == Mage_Sales_Model_Order_Creditmemo::STATE_REFUNDED ||
                $orderCreditmemo->getState() == Mage_Sales_Model_Order_Creditmemo::STATE_OPEN) {
                return $orderCreditmemo;
            }
        }

        return false;
    }

    /**
     * Process error: order states sequence violation
     *
     * @param array $request
     * @param Mage_Sales_Model_Order $order
     */
    protected function _errorViolationSequenceStates($request, $order)
    {
        $this->_error(
            Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmation error: order states sequence violation'),
            $request,
            $order
        );
    }

    /**
     * Process error
     *
     * @param string $comment
     * @param array $request
     * @param Mage_Sales_Model_Order $order
     */
    protected function _error($comment, $request, $order = null)
    {
        $message = $comment . Mage::helper('amazonpayments')->__('<br/>Trace confirmation request:<br/>%s', $request->toString());

        if (!is_null($order)) {
            $order->addStatusToHistory(
                $order->getStatus(),
                $message
            )->save();
        }

        Mage::throwException($comment);
    }
}
