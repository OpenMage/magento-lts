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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order payment information
 */
class Mage_Sales_Model_Order_Payment extends Mage_Payment_Model_Info
{
    /**
     * Order model object
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Whether can void
     * @var string
     */
    private $_canVoidLookup = null;

    /**
     * Transactions registry to spare resource calls
     * array(txn_id => sales/order_payment_transaction)
     * @var array
     */
    private $_transactionsLookup = array();

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order_payment');
    }

    /**
     * Declare order model object
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Order_Payment
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Check order payment capture action availability
     *
     * @return unknown
     */
    public function canCapture()
    {
        /**
         * @todo checking amounts
         */
        return $this->getMethodInstance()->canCapture();
    }

    public function canRefund()
    {
        return $this->getMethodInstance()->canRefund();
    }

    public function canRefundPartialPerInvoice()
    {
        return $this->getMethodInstance()->canRefundPartialPerInvoice();
    }

    public function canCapturePartial()
    {
        return $this->getMethodInstance()->canCapturePartial();
    }

    /**
     * Authorize or authorize and capture payment on gateway, if applicable
     * This method is supposed to be called only when order is placed
     *
     * @return Mage_Sales_Model_Order_Payment
     */
    public function place()
    {
        Mage::dispatchEvent('sales_order_payment_place_start', array('payment' => $this));

        $this->setAmountOrdered($this->getOrder()->getTotalDue());
        $this->setBaseAmountOrdered($this->getOrder()->getBaseTotalDue());

        $this->setShippingAmount($this->getOrder()->getShippingAmount());
        $this->setBaseShippingAmount($this->getOrder()->getBaseShippingAmount());

        $methodInstance = $this->getMethodInstance()->setStore($this->getOrder()->getStoreId());

        $orderState = Mage_Sales_Model_Order::STATE_NEW;
        $orderStatus= false;

        $stateObject = new Varien_Object();

        /**
         * validating payment method again
         */
        $methodInstance->validate();
        if ($action = $methodInstance->getConfigPaymentAction()) {
            /**
             * Run action declared for payment method in configuration
             */

            if ($methodInstance->isInitializeNeeded()) {
                /**
                 * For method initialization we have to use original config value for payment action
                 */
                $methodInstance->initialize($methodInstance->getConfigData('payment_action'), $stateObject);
            } else {
                $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                switch ($action) {
                    case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE:
                        $this->setAmountAuthorized($this->getOrder()->getTotalDue());
                        $this->_authorize(true, $this->getOrder()->getBaseTotalDue()); // base amount will be set inside
                        break;
                    case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE:
                        $this->setAmountAuthorized($this->getOrder()->getTotalDue());
                        $this->setBaseAmountAuthorized($this->getOrder()->getBaseTotalDue());
                        $this->capture(null);
                        break;
                    default:
                        break;
                }
            }
        }

        $orderIsNotified = null;
        if ($stateObject->getState() && $stateObject->getStatus()) {
            $orderState      = $stateObject->getState();
            $orderStatus     = $stateObject->getStatus();
            $orderIsNotified = $stateObject->getIsNotified();
        } else {
            /*
             * this flag will set if the order went to as authorization under fraud service for payflowpro
             */
            if ($this->getFraudFlag()) {
                $orderStatus = $methodInstance->getConfigData('fraud_order_status');
                $orderState = Mage_Sales_Model_Order::STATE_HOLDED;
            } else {
                /**
                 * Change order status if it specified
                 */
                $orderStatus = $methodInstance->getConfigData('order_status');
            }

            if (!$orderStatus || $this->getOrder()->getIsVirtual()) {
                $orderStatus = $this->getOrder()->getConfig()->getStateDefaultStatus($orderState);
            }
        }

        // set state and add to order history only if something was changed or there is a message
        $order = $this->getOrder();
        $message = $order->getCustomerNote();
        if ($orderState !== $order->getState() || $orderStatus !== false && $orderStatus !== $order->getStatus() || $message) {
            $order->setState($orderState);
            $order->addStatusToHistory($orderStatus, $message,
                (null !== $orderIsNotified ? $orderIsNotified : $this->getOrder()->getCustomerNoteNotify())
            );
        }

        Mage::dispatchEvent('sales_order_payment_place_end', array('payment' => $this));

        return $this;
    }

    /**
     * Capture the payment online
     * Requires an invoice. If there is no invoice specified, will automatically prepare an invoice for order
     * Updates transactions hierarchy, if required
     * Updates payment totals, updates order status and adds proper comments
     *
     * @return Mage_Sales_Model_Order_Payment
     */
    public function capture($invoice)
    {
        if (is_null($invoice)) {
            $invoice = $this->_invoice();
            $this->setCreatedInvoice($invoice);
            return $this; // @see Mage_Sales_Model_Order_Invoice::capture()
        }
        $amountToCapture = $this->_formatAmount($invoice->getBaseGrandTotal());

        $paidWorkaround = 0;
        if (!$invoice->wasPayCalled()) {
            $paidWorkaround = (float)$amountToCapture;
        }
        $this->_isCaptureFinal($paidWorkaround);
        $this->_generateTransactionId(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE,
            $this->getAuthorizationTransaction()
        );

        Mage::dispatchEvent('sales_order_payment_capture', array('payment' => $this, 'invoice' => $invoice));


        $this->getMethodInstance()->setStore($this->getOrder()->getStoreId())->capture($this, $amountToCapture);

        // update transactions, set order state (order will close itself if required)
        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE, $invoice, true);
        $message = $this->_appendTransactionToMessage($transaction,
            Mage::helper('sales')->__('Captured amount of %s online.', $amountToCapture)
        );
        $this->getOrder()->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);
        $this->getMethodInstance()->processInvoice($invoice, $this); // should be deprecated
        return $this;
    }

    /**
     * Process a capture notification from a payment gateway for specified amount
     * Creates an invoice automatically if the amount covers the order base grand total completely
     * Updates transactions hierarchy, if required
     * Prevents transaction double processing
     * Updates payment totals, updates order status and adds proper comments
     *
     * @param float $amount
     * @return Mage_Sales_Model_Order_Payment
     */
    public function registerCaptureNotification($amount)
    {
        $this->_generateTransactionId(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE,
            $this->getAuthorizationTransaction()
        );
        $this->_avoidDoubleTransactionProcessing();
        $order   = $this->getOrder();
        $invoice = null;
        $amount  = (float)$amount;

        // prepare invoice if total paid is going to be equal to order grand total
        // possible bug: we are not protected from case when order grand total != total authorized
        if ($this->_isCaptureFinal($amount)) {
            // ok, we may create an invoice
            if (!$order->canInvoice()) {
                Mage::throwException(Mage::helper('sales')->__('Order does not allow to create an invoice.'));
            }
            $invoice = $order->prepareInvoice()->register()->pay();
            $order->addRelatedObject($invoice);
            $this->setCreatedInvoice($invoice);
        } else {
            $this->_updateTotals(array('base_amount_paid' => $amount));
            // shipping captured amount should be updated with the invoice
        }

        // update transactions, set order state (order will close itself later if required)
        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE, $invoice);
        $message = Mage::helper('sales')->__('Registered notification about captured amount of %s.', $this->_formatAmount($amount));
        $message = $this->_prependMessage($message);
        $message = $this->_appendTransactionToMessage($transaction, $message);
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);
        return $this;
    }

    /**
     * Process authorization notification
     *
     * @see self::_authorize()
     * @param float $amount
     * @return Mage_Sales_Model_Order_Payment
     */
    public function registerAuthorizationNotification($amount)
    {
        $this->_avoidDoubleTransactionProcessing();
        return $this->_authorize(false, $amount);
    }

    /**
     * Register payment fact: update self totals from the invoice
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Mage_Sales_Model_Order_Payment
     */
    public function pay($invoice)
    {
        $this->_updateTotals(array(
            'amount_paid' => $invoice->getGrandTotal(),
            'base_amount_paid' => $invoice->getBaseGrandTotal(),
            'shipping_captured' => $invoice->getShippingAmount(),
            'base_shipping_captured' => $invoice->getBaseShippingAmount(),
        ));
        Mage::dispatchEvent('sales_order_payment_pay', array('payment' => $this, 'invoice' => $invoice));
        return $this;
    }

    /**
     * Cancel specified invoice: update self totals from it
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Mage_Sales_Model_Order_Payment
     */
    public function cancelInvoice($invoice)
    {
        $this->_updateTotals(array(
            'amount_paid' => -1 * $invoice->getGrandTotal(),
            'base_amount_paid' => -1 * $invoice->getBaseGrandTotal(),
            'shipping_captured' => -1 * $invoice->getShippingAmount(),
            'base_shipping_captured' => -1 * $invoice->getBaseShippingAmount(),
        ));
        Mage::dispatchEvent('sales_order_payment_cancel_invoice', array('payment' => $this, 'invoice' => $invoice));
        return $this;
    }

    /**
     * Create new invoice with maximum qty for invoice for each item
     * register this invoice and capture
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _invoice()
    {
        $invoice = $this->getOrder()->prepareInvoice();

        $invoice->register();
        if ($this->getMethodInstance()->canCapture()) {
            $invoice->capture();
        }

        $this->getOrder()->addRelatedObject($invoice);
        return $invoice;
    }

    /**
     * Check order payment void availability
     *
     * @return bool
     */
    public function canVoid(Varien_Object $document)
    {
        if (null === $this->_canVoidLookup) {
            $this->_canVoidLookup = (bool)$this->getMethodInstance()->canVoid($document);
            if ($this->_canVoidLookup) {
                $authTransaction = $this->getAuthorizationTransaction();
                $this->_canVoidLookup = (bool)$authTransaction && !(int)$authTransaction->getIsClosed();
            }
        }
        return $this->_canVoidLookup;
    }

    /**
     * Void payment online
     *
     * @see self::_void()
     * @param Varien_Object $document
     * @return Mage_Sales_Model_Order_Payment
     */
    public function void(Varien_Object $document)
    {
        $this->_void(true);
        Mage::dispatchEvent('sales_order_payment_void', array('payment' => $this, 'invoice' => $document));
        return $this;
    }

    /**
     * Process void notification
     *
     * @see self::_void()
     * @param float $amount
     * @return Mage_Sales_Model_Payment
     */
    public function registerVoidNotification($amount = null)
    {
        if (!$this->hasMessage()) {
            $this->setMessage(Mage::helper('sales')->__('Registered a Void notification.'));
        }
        return $this->_void(false, $amount);
    }

    /**
     * Refund payment online or offline, depending on whether there is invoice set in the creditmemo instance
     * Updates transactions hierarchy, if required
     * Updates payment totals, updates order status and adds proper comments
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return Mage_Sales_Model_Order_Payment
     */
    public function refund($creditmemo)
    {
        $baseAmountToRefund = $this->_formatAmount($creditmemo->getBaseGrandTotal());
        $order = $this->getOrder();

        $this->_generateTransactionId(Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND);

        // call refund from gateway if required
        $isOnline = false;
        $gateway = $this->getMethodInstance();
        $invoice = null;
        if ($gateway->canRefund() && $creditmemo->getDoTransaction()) {
            $this->setCreditmemo($creditmemo);
            $invoice = $creditmemo->getInvoice();
            if ($invoice) {
                $isOnline = true;
                $captureTxn = $this->_lookupTransaction($invoice->getTransactionId());
                if ($captureTxn) {
                    $this->setParentTransactionId($captureTxn->getTxnId());
                }
                $this->setShouldCloseParentTransaction(true); // TODO: implement multiple refunds per capture
                try {
                    $gateway->setStore($this->getOrder()->getStoreId())
                        ->processBeforeRefund($invoice, $this)
                        ->refund($this, $baseAmountToRefund)
                        ->processCreditmemo($creditmemo, $this)
                    ;
                } catch (Mage_Core_Exception $e) {
                    if (!$captureTxn) {
                        $e->setMessage(' ' . Mage::helper('sales')->__('If the invoice was created offline, try creating an offline creditmemo.'), true);
                    }
                    throw $e;
                }
            }
        }

        // update self totals from creditmemo
        $this->_updateTotals(array(
            'amount_refunded' => $creditmemo->getGrandTotal(),
            'base_amount_refunded' => $baseAmountToRefund,
            'shipping_refunded' => $creditmemo->getShippingAmount(),
            'base_shipping_refunded' => $creditmemo->getBaseShippingAmount()
        ));

        // update transactions and order state
        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND, $creditmemo, $isOnline);
        if ($invoice) {
            $message = Mage::helper('sales')->__('Refunded amount of %s online.', $baseAmountToRefund);
        } else {
            $message = $this->hasMessage() ? $this->getMessage()
                : Mage::helper('sales')->__('Refunded amount of %s offline.', $baseAmountToRefund);
        }
        $message = $this->_appendTransactionToMessage($transaction, $message);
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);

        Mage::dispatchEvent('sales_order_payment_refund', array('payment' => $this, 'creditmemo' => $creditmemo));
        return $this;
    }

    /**
     * Process payment refund notification
     * Updates transactions hierarchy, if required
     * Prevents transaction double processing
     * Updates payment totals, updates order status and adds proper comments
     * TODO: potentially a full capture can be refunded. In this case if there was only one invoice for that transaction
     *       then we should create a creditmemo from invoice and also refund it offline
     *
     * @param float $amount
     * @return Mage_Sales_Model_Order_Payment
     */
    public function registerRefundNotification($amount)
    {
        $this->_generateTransactionId(Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND,
            $this->_lookupTransaction($this->getParentTransactionId())
        );
        $this->_avoidDoubleTransactionProcessing();
        $order = $this->getOrder();

        // create an offline creditmemo (from order), if the entire grand total of order is covered by this refund
        $creditmemo = null;
        if ($amount == $order->getBaseGrandTotal()) {
            /*
            $creditmemo = $order->prepareCreditmemo()->register()->refund();
            $this->_updateTotals(array(
                'amount_refunded' => $creditmemo->getGrandTotal(),
                'shipping_refunded' => $creditmemo->getShippingRefunded(),
                'base_shipping_refunded' => $creditmemo->getBaseShippingRefunded()
            ));
            $order->addRelatedObject($creditmemo);
            $this->setCreatedCreditmemo($creditmemo);
            */
        }
        $this->_updateTotals(array('base_amount_refunded' => $amount));

        // update transactions and order state
        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND, $creditmemo);
        $message = $this->_prependMessage(
            Mage::helper('sales')->__('Registered notification about refunded amount of %s.', $this->_formatAmount($amount))
        );
        $message = $this->_appendTransactionToMessage($transaction, $message);
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);
        return $this;
    }

    /**
     * Cancel a creditmemo: substract its totals from the payment
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return Mage_Sales_Model_Order_Payment
     */
    public function cancelCreditmemo($creditmemo)
    {
        $this->_updateTotals(array(
            'amount_refunded' => -1 * $creditmemo->getGrandTotal(),
            'base_amount_refunded' => -1 * $creditmemo->getBaseGrandTotal(),
            'shipping_refunded' => -1 * $creditmemo->getShippingAmount(),
            'base_shipping_refunded' => -1 * $creditmemo->getBaseShippingAmount()
        ));
        Mage::dispatchEvent('sales_order_payment_cancel_creditmemo',
            array('payment' => $this, 'creditmemo' => $creditmemo)
        );
        return $this;
    }

    /**
     * Order cancellation hook for payment method instance
     * Adds void transaction if needed
     * @return Mage_Sales_Model_Order_Payment
     */
    public function cancel()
    {
        $isOnline = true;
        if (!$this->canVoid(new Varien_Object())) {
            $isOnline = false;
        }

        if (!$this->hasMessage()) {
            $this->setMessage($isOnline ? Mage::helper('sales')->__('Cancelled order online.')
                : Mage::helper('sales')->__('Cancelled order offline.')
            );
        }
        $this->_void($isOnline, null, 'cancel');

        Mage::dispatchEvent('sales_order_payment_cancel', array('payment' => $this));

        return $this;
    }

    /**
     * Authorize payment either online or offline (process auth notification)
     * Updates transactions hierarchy, if required
     * Prevents transaction double processing
     * Updates payment totals, updates order status and adds proper comments
     *
     * @param bool $isOnline
     * @param float $amount
     * @return Mage_Sales_Model_Order_Payment
     */
    protected function _authorize($isOnline, $amount)
    {
        // update totals
        $amount = $this->_formatAmount($amount, true);
        $this->setBaseAmountAuthorized($amount);

        // do authorization
        $order = $this->getOrder();
        if ($isOnline) {
            $this->getMethodInstance()->setStore($order->getStoreId())->authorize($this, $amount);
            $message = Mage::helper('sales')->__('Authorized amount of %s.', $amount);
        } else {
            $message = Mage::helper('sales')->__('Registered notification about authorized amount of %s.', $amount);
        }

        // update transactions, order state and add comments
        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
        $message = $this->_prependMessage($message);
        $message = $this->_appendTransactionToMessage($transaction, $message);
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);

        return $this;
    }

    /**
     * Void payment either online or offline (process void notification)
     * NOTE: that in some cases authorization can be voided after a capture. In such case it makes sense to use
     *       the amount void amount, for informational purposes.
     * Updates payment totals, updates order status and adds proper comments
     *
     * @param bool $isOnline
     * @param float $amount
     * @param string $gatewayCallback
     * @return Mage_Sales_Model_Order_Payment
     */
    protected function _void($isOnline, $amount = null, $gatewayCallback = 'void')
    {
        $order = $this->getOrder();
        $authTransaction = $this->getAuthorizationTransaction();
        $this->_generateTransactionId(Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID, $authTransaction);
        $this->setShouldCloseParentTransaction(true);

        // attempt to void
        if ($isOnline) {
            $this->getMethodInstance()->setStore($order->getStoreId())->$gatewayCallback($this);
        } else {
            $this->_avoidDoubleTransactionProcessing();
        }

        // if the authorization was untouched, we may update canceled amount of the order grand total
        // but only if the payment auth amount equals to order grand total
        if ($authTransaction && ($order->getBaseGrandTotal() == $this->getBaseAmountAuthorized())
            && (0 == $this->getBaseAmountCanceled())) {
            if ($authTransaction->canVoidAuthorizationCompletely()) {
                $amount = (float)$order->getBaseGrandTotal();
            }
        }

        if ($amount) {
            $this->_updateTotals(array('base_amount_canceled' => $amount));
            $amount = $this->_formatAmount($amount);
        }

        // update transactions, order state and add comments
        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID);
        $message = $this->hasMessage() ? $this->getMessage() : Mage::helper('sales')->__('Voided authorization.');
        $message = $this->_prependMessage($message);
        if ($amount) {
            $message .= ' ' . Mage::helper('sales')->__('Amount: %s.', $amount);
        }
        $message = $this->_appendTransactionToMessage($transaction, $message);
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);
        return $this;
    }

//    /**
//     * TODO: implement this
//     * @param Mage_Sales_Model_Order_Invoice $invoice
//     * @return Mage_Sales_Model_Order_Payment
//     */
//    public function cancelCapture($invoice = null)
//    {
//    }

    /**
     * Create transaction, prepare its insertion into hierarchy and add its information to payment and comments
     *
     * To add transactions and related information, the following information should be set to payment before processing:
     * - transaction_id
     * - is_transaction_closed (optional) - whether transaction should be closed or open (closed by default)
     * - parent_transaction_id (optional)
     * - should_close_parent_transaction (optional) - whether to close parent transaction (closed by default)
     *
     * If the sales document is specified, it will be linked to the transaction as related for future usage.
     * Currently transaction ID is set into the sales object
     * This method writes the added transaction ID into last_trans_id field of the payment object
     *
     * To make sure transaction object won't cause trouble before saving, use $failsafe = true
     *
     * @param string $type
     * @param Mage_Sales_Model_Abstract $salesDocument
     * @param bool $failsafe
     * @return null|Mage_Sales_Model_Order_Payment_Transaction
     */
    protected function _addTransaction($type, $salesDocument = null, $failsafe = false)
    {
        // look for set transaction ids
        $transactionId = $this->getTransactionId();
        if (null !== $transactionId) {
            // set transaction parameters
            $transaction = Mage::getModel('sales/order_payment_transaction')
                ->setOrderPaymentObject($this)
                ->setTxnType($type)
                ->setTxnId($transactionId)
                ->isFailsafe($failsafe)
            ;
            if ($this->hasIsTransactionClosed()) {
                $transaction->setIsClosed((int)$this->getIsTransactionClosed());
            }

            // link with sales entities
            $this->setLastTransId($transactionId);
            $this->setCreatedTransaction($transaction);
            $this->getOrder()->addRelatedObject($transaction);
            if ($salesDocument && $salesDocument instanceof Mage_Sales_Model_Abstract) {
                $salesDocument->setTransactionId($transactionId);
                // TODO: linking transaction with the sales document
            }

            // link with parent transaction
            $parentTransactionId = $this->getParentTransactionId();
            if ($parentTransactionId) {
                $transaction->setParentTxnId($parentTransactionId);
                if ($this->getShouldCloseParentTransaction()) {
                    $parentTransaction = $this->_lookupTransaction($parentTransactionId);
                    if ($parentTransaction) {
                        $parentTransaction->isFailsafe($failsafe)->close(false);
                        $this->getOrder()->addRelatedObject($parentTransaction);
                    }
                }
            }
            return $transaction;
        }
    }

    /**
     * Totals updater utility method
     * Updates self totals by keys in data array('key' => $delta)
     *
     * @param array $data
     */
    private function _updateTotals($data)
    {
        foreach ($data as $key => $amount) {
            if (null !== $amount) {
                $was = $this->getDataUsingMethod($key);
                $this->setDataUsingMethod($key, $was + $amount);
            }
        }
    }

    /**
     * Prevent double processing of the same transaction by a payment notification
     * Uses either specified txn_id or the transaction id that was set before
     *
     * @param string $txnId
     * @throws Mage_Core_Exception
     */
    protected function _avoidDoubleTransactionProcessing($txnId = null)
    {
        if (null === $txnId) {
            $txnId = $this->getTransactionId();
        }
        if ($txnId) {
            $transaction = Mage::getModel('sales/order_payment_transaction')
                ->setOrderPaymentObject($this)
                ->loadByTxnId($txnId);
            if ($transaction->getId()) {
                Mage::throwException(
                    Mage::helper('sales')->__('Transaction "%s" was already processed.', $transaction->getTxnId())
                );
            }
        }
    }

    /**
     * Append transaction ID (if any) message to the specified message
     *
     * @param Mage_Sales_Model_Order_Payment_Transaction|null $transaction
     * @param string $message
     * @return string
     */
    private function _appendTransactionToMessage($transaction, $message)
    {
        if ($transaction) {
            $message .= ' ' . Mage::helper('sales')->__('Transaction ID: "%s".', $transaction->getTxnId());
        }
        return $message;
    }

    /**
     * Prepend a "prepared_message" that may be set to the payment instance before, to the specified message
     * Prepends value to the specified string or to the comment of specified order status history item instance
     *
     * @param string|Mage_Sales_Model_Order_Status_History $messagePrependTo
     * @return string|Mage_Sales_Model_Order_Status_History
     */
    private function _prependMessage($messagePrependTo)
    {
        $preparedMessage = $this->getPreparedMessage();
        if ($preparedMessage) {
            if (is_string($preparedMessage)) {
                return $preparedMessage . ' ' . $messagePrependTo;
            }
            elseif (is_object($preparedMessage) && ($preparedMessage instanceof Mage_Sales_Model_Order_Status_History)) {
                $comment = $preparedMessage->getComment() . ' ' . $messagePrependTo;
                $preparedMessage->setComment($comment);
                return $comment;
            }
        }
        return $messagePrependTo;
    }

    /**
     * Round up and cast specified amount to float or string
     *
     * @param string|float $amount
     * @param bool $asFloat
     * @return string|float
     */
    protected function _formatAmount($amount, $asFloat = false)
    {
        $amount = sprintf('%.2F', $amount); // "f" depends on locale, "F" doesn't
        return $asFloat ? (float)$amount : $amount;
    }

    /**
     * Find one transaction by ID or type
     * @param string $txnId
     * @param string $txnType
     * @return Mage_Sales_Model_Order_Payment_Transaction|false
     */
    protected function _lookupTransaction($txnId, $txnType = false)
    {
        if (!$txnId) {
            if ($txnType && $this->getId()) {
                $collection = Mage::getModel('sales/order_payment_transaction')->getCollection()
                    ->addPaymentIdFilter($this->getId())
                    ->addTxnTypeFilter($txnType);
                foreach ($collection as $txn) {
                    $txn->setOrderPaymentObject($this);
                    $this->_transactionsLookup[$txn->getTxnId()] = $txn;
                    return $txn;
                }
            }
            return false;
        }
        if (isset($this->_transactionsLookup[$txnId])) {
            return $this->_transactionsLookup[$txnId];
        }
        $txn = Mage::getModel('sales/order_payment_transaction')
            ->setOrderPaymentObject($this)
            ->loadByTxnId($txnId);
        if ($txn->getId()) {
            $this->_transactionsLookup[$txnId] = $txn;
        } else {
            $this->_transactionsLookup[$txnId] = false;
        }
        return $this->_transactionsLookup[$txnId];
    }

    /**
     * Lookup an authorization transaction using parent transaction id, if set
     * @return Mage_Sales_Model_Order_Payment_Transaction|false
     */
    public function getAuthorizationTransaction()
    {
        $txn = $this->_lookupTransaction($this->getParentTransactionId());
        if (!$txn) {
            $txn = $this->_lookupTransaction(false, Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
        }
        return $txn;
    }

    /**
     * Update transaction ids for further processing
     * If no transactions were set before invoking, may generate an "offline" transaction id
     *
     * @param string $type
     * @param Mage_Sales_Model_Order_Payment_Transaction $transactionBasedOn
     */
    protected function _generateTransactionId($type, $transactionBasedOn = false)
    {
        if (!$this->getParentTransactionId() && !$this->getTransactionId() && $transactionBasedOn) {
            $this->setParentTransactionId($transactionBasedOn->getTxnId());
        }
        // generate transaction id for an offline action or payment method that didn't set it
        if (($parentTxnId = $this->getParentTransactionId()) && !$this->getTransactionId()) {
            $this->setTransactionId("{$parentTxnId}-{$type}");
        }
    }

    /**
     * Decide whether authorization transaction may close (if the amount to capture will cover entire order)
     * @param float $amountToCapture
     * @return bool
     */
    protected function _isCaptureFinal($amountToCapture)
    {
        if ((float)$this->getOrder()->getBaseGrandTotal() ===
            ((float)$this->getBaseAmountPaid() + $amountToCapture)) {
            if (false !== $this->getShouldCloseParentTransaction()) {
                $this->setShouldCloseParentTransaction(true);
            }
            return true;
        }
        return false;
    }
}
