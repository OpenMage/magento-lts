<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order payment information
 *
 * @category   Mage
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Payment _getResource()
 * @method Mage_Sales_Model_Resource_Order_Payment getResource()
 * @method Mage_Sales_Model_Resource_Order_Payment_Collection getCollection()
 *
 * @method string getAccountStatus()
 * @method $this setAccountStatus(string $value)
 * @method string getAdditionalData()
 * @method $this setAdditionalData(string $value)
 * @method string getAddressStatus()
 * @method $this setAddressStatus(string $value)
 * @method float getAmountAuthorized()
 * @method $this setAmountAuthorized(float $value)
 * @method float getAmountCanceled()
 * @method $this setAmountCanceled(float $value)
 * @method float getAmountOrdered()
 * @method $this setAmountOrdered(float $value)
 * @method float getAmountPaid()
 * @method $this setAmountPaid(float $value)
 * @method float getAmountRefunded()
 * @method $this setAmountRefunded(float $value)
 * @method string getAnetTransMethod()
 * @method $this setAnetTransMethod(string $value)
 *
 * @method float getBaseAmountAuthorized()
 * @method $this setBaseAmountAuthorized(float $value)
 * @method float getBaseAmountCanceled()
 * @method $this setBaseAmountCanceled(float $value)
 * @method float getBaseAmountOrdered()
 * @method $this setBaseAmountOrdered(float $value)
 * @method float getBaseAmountPaid()
 * @method $this setBaseAmountPaid(float $value)
 * @method float getBaseAmountPaidOnline()
 * @method $this setBaseAmountPaidOnline(float $value)
 * @method float getBaseAmountRefunded()
 * @method $this setBaseAmountRefunded(float $value)
 * @method float getBaseAmountRefundedOnline()
 * @method $this setBaseAmountRefundedOnline(float $value)
 * @method float getBaseShippingAmount()
 * @method $this setBaseShippingAmount(float $value)
 * @method float getBaseShippingCaptured()
 * @method $this setBaseShippingCaptured(float $value)
 * @method float getBaseShippingRefunded()
 * @method $this setBaseShippingRefunded(float $value)
 * @method array getBillingAgreementData()
 *
 * @method string getCcApproval()
 * @method $this setCcApproval(string $value)
 * @method string getCcAvsStatus()
 * @method $this setCcAvsStatus(string $value)
 * @method string getCcCidStatus()
 * @method $this setCcCidStatus(string $value)
 * @method string getCcDebugRequestBody()
 * @method $this setCcDebugRequestBody(string $value)
 * @method string getCcDebugResponseBody()
 * @method $this setCcDebugResponseBody(string $value)
 * @method string getCcDebugResponseSerialized()
 * @method $this setCcDebugResponseSerialized(string $value)
 * @method string getCcExpMonth()
 * @method $this setCcExpMonth(string $value)
 * @method string getCcExpYear()
 * @method $this setCcExpYear(string $value)
 * @method string getCcLast4()
 * @method $this setCcLast4(string $value)
 * @method string getCcNumberEnc()
 * @method $this setCcNumberEnc(string $value)
 * @method string getCcOwner()
 * @method $this setCcOwner(string $value)
 * @method string getCcSecureVerify()
 * @method $this setCcSecureVerify(string $value)
 * @method string getCcSsIssue()
 * @method $this setCcSsIssue(string $value)
 * @method string getCcSsStartMonth()
 * @method $this setCcSsStartMonth(string $value)
 * @method string getCcSsStartYear()
 * @method $this setCcSsStartYear(string $value)
 * @method string getCcStatus()
 * @method $this setCcStatus(string $value)
 * @method string getCcStatusDescription()
 * @method $this setCcStatusDescription(string $value)
 * @method string getCcTransId()
 * @method $this setCcTransId(string $value)
 * @method string getCcType()
 * @method $this setCcType(string $value)
 * @method $this setCreatedCreditmemo(Mage_Sales_Model_Order_Creditmemo $value)
 * @method $this setCreatedInvoice(Mage_Sales_Model_Order_Invoice $invoice)
 * @method $this setCreatedTransaction(Mage_Sales_Model_Order_Payment_Transaction $value)
 * @method $this setCreditmemo(Mage_Sales_Model_Order_Creditmemo $value)
 * @method string getCurrencyCode()
 * @method int getCustomerPaymentId()
 * @method $this setCustomerPaymentId(int $value)
 * @method string getCybersourceToken()
 * @method $this setCybersourceToken(string $value)
 *
 * @method string getEcheckAccountName()
 * @method $this setEcheckAccountName(string $value)
 * @method string getEcheckAccountType()
 * @method $this setEcheckAccountType(string $value)
 * @method string getEcheckBankName()
 * @method $this setEcheckBankName(string $value)
 * @method string getEcheckRoutingNumber()
 * @method $this setEcheckRoutingNumber(string $value)
 * @method string getEcheckType()
 * @method $this setEcheckType(string $value)
 *
 * @method string getFlo2cashAccountId()
 * @method $this setFlo2cashAccountId(string $value)
 * @method bool hasForcedState()
 * @method string getForcedState()
 * @method bool getIsFraudDetected()
 *
 * @method string getIdealIssuerId()
 * @method $this setIdealIssuerId(string $value)
 * @method string getIdealIssuerTitle()
 * @method $this setIdealIssuerTitle(string $value)
 * @method int getIdealTransactionChecked()
 * @method $this setIdealTransactionChecked(int $value)
 * @method int getIncrementId()
 * @method $this setIsFraudDetected(bool $value)
 * @method bool getIsTransactionApproved()
 * @method bool hasIsTransactionClosed()
 * @method bool getIsTransactionClosed()
 * @method bool getIsTransactionDenied()
 * @method bool getIsTransactionPending()
 *
 * @method string getLastTransId()
 * @method $this setLastTransId(string $value)
 *
 * @method bool hasMessage()
 * @method string getMessage()
 * @method $this setMessage(string $value)
 * @method string getMethod()
 * @method $this setMethod(string $value)
 *
 * @method bool getNotificationResult()
 *
 * @method int getParentId()
 * @method $this setParentId(int $value)
 * @method string getParentTransactionId()
 * @method $this setParentTransactionId(string $value)
 * @method string getPayboxQuestionNumber()
 * @method $this setPayboxQuestionNumber(string $value)
 * @method string getPayboxRequestNumber()
 * @method $this setPayboxRequestNumber(string $value)
 * @method string getPoNumber()
 * @method $this setPoNumber(string $value)
 * @method string getPreparedMessage()
 * @method string getProtectionEligibility()
 * @method $this setProtectionEligibility(string $value)
 *
 * @method int getQuotePaymentId()
 * @method $this setQuotePaymentId(int $value)
 *
 * @method $this setRefundTransactionId(string $value)
 *
 * @method float getShippingAmount()
 * @method $this setShippingAmount(float $value)
 * @method float getShippingCaptured()
 * @method $this setShippingCaptured(float $value)
 * @method float getShippingRefunded()
 * @method $this setShippingRefunded(float $value)
 * @method bool getShouldCloseParentTransaction()
 * @method $this setShouldCloseParentTransaction(bool $value)
 * @method bool getSkipOrderProcessing()
 * @method bool getSkipTransactionCreation()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 *
 * @method string getTransactionId()
 * @method $this setTransactionId(string $value)
 * @method $this unsTransactionId()
 *
 * @method $this setVoidTransactionId(string $value)
 */
class Mage_Sales_Model_Order_Payment extends Mage_Payment_Model_Info
{
    /**
     * Actions for payment when it triggered review state:
     *
     * Accept action
     */
    public const REVIEW_ACTION_ACCEPT = 'accept';

    /**
     * Deny action
     */
    public const REVIEW_ACTION_DENY   = 'deny';

    /**
     * Update action
     */
    public const REVIEW_ACTION_UPDATE = 'update';

    /**
     * Order model object
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Billing agreement instance that may be created during payment processing
     *
     * @var Mage_Sales_Model_Billing_Agreement
     */
    protected $_billingAgreement = null;

    /**
     * Whether can void
     * @var string
     */
    protected $_canVoidLookup = null;

    /**
     * Transactions registry to spare resource calls
     * array(txn_id => sales/order_payment_transaction)
     * @var array
     */
    protected $_transactionsLookup = [];

    /**
     * @var string
     */
    protected $_eventPrefix = 'sales_order_payment';

    /**
     * @var string
     */
    protected $_eventObject = 'payment';

    /**
     * Transaction addditional information container
     *
     * @var array
     */
    protected $_transactionAdditionalInfo = [];

    protected function _construct()
    {
        $this->_init('sales/order_payment');
    }

    /**
     * Declare order model object
     *
     * @return  $this
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
     * @return bool
     */
    public function canCapture()
    {
        if (!$this->getMethodInstance()->canCapture()) {
            return false;
        }
        // Check Authoriztion transaction state
        $authTransaction = $this->getAuthorizationTransaction();
        if ($authTransaction && $authTransaction->getIsClosed()) {
            $orderTransaction = $this->_lookupTransaction(null, Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER);
            if (!$orderTransaction) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check whether refund could be done
     *
     * @return bool
     */
    public function canRefund()
    {
        return $this->getMethodInstance()->canRefund();
    }

    /**
     * Check whether partial refund could be done
     *
     * @return bool
     */
    public function canRefundPartialPerInvoice()
    {
        return $this->getMethodInstance()->canRefundPartialPerInvoice();
    }

    /**
     * Check whether partial capture could be done
     *
     * @return bool
     */
    public function canCapturePartial()
    {
        return $this->getMethodInstance()->canCapturePartial();
    }

    /**
     * Authorize or authorize and capture payment on gateway, if applicable
     * This method is supposed to be called only when order is placed
     *
     * @return $this
     */
    public function place()
    {
        Mage::dispatchEvent('sales_order_payment_place_start', ['payment' => $this]);
        $order = $this->getOrder();

        $this->setAmountOrdered($order->getTotalDue());
        $this->setBaseAmountOrdered($order->getBaseTotalDue());
        $this->setShippingAmount($order->getShippingAmount());
        $this->setBaseShippingAmount($order->getBaseShippingAmount());

        $methodInstance = $this->getMethodInstance();
        $methodInstance->setStore($order->getStoreId());
        $orderState = Mage_Sales_Model_Order::STATE_NEW;
        $stateObject = new Varien_Object();

        /**
         * Do order payment validation on payment method level
         */
        $methodInstance->validate();
        $action = $methodInstance->getConfigPaymentAction();
        if ($action) {
            if ($methodInstance->isInitializeNeeded()) {
                /**
                 * For method initialization we have to use original config value for payment action
                 */
                $methodInstance->initialize($methodInstance->getConfigData('payment_action'), $stateObject);
            } else {
                $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                switch ($action) {
                    case Mage_Payment_Model_Method_Abstract::ACTION_ORDER:
                        $this->_order($order->getBaseTotalDue());
                        break;
                    case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE:
                        $this->_authorize(true, $order->getBaseTotalDue()); // base amount will be set inside
                        $this->setAmountAuthorized($order->getTotalDue());
                        break;
                    case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE:
                        $this->setAmountAuthorized($order->getTotalDue());
                        $this->setBaseAmountAuthorized($order->getBaseTotalDue());
                        $this->capture(null);
                        break;
                    default:
                        break;
                }
            }
        }

        $this->_createBillingAgreement();

        $orderIsNotified = null;
        if ($stateObject->getState() && $stateObject->getStatus()) {
            $orderState      = $stateObject->getState();
            $orderStatus     = $stateObject->getStatus();
            $orderIsNotified = $stateObject->getIsNotified();
        } else {
            $orderStatus = $methodInstance->getConfigData('order_status');
            if (!$orderStatus) {
                $orderStatus = $order->getConfig()->getStateDefaultStatus($orderState);
            } else {
                // check if $orderStatus has assigned a state
                $states = $order->getConfig()->getStatusStates($orderStatus);
                if (count($states) == 0) {
                    $orderStatus = $order->getConfig()->getStateDefaultStatus($orderState);
                }
            }
        }
        $isCustomerNotified = $orderIsNotified ?? $order->getCustomerNoteNotify();
        $message = $order->getCustomerNote();

        // add message if order was put into review during authorization or capture
        if ($order->getState() == Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW) {
            if ($message) {
                $order->addStatusToHistory($order->getStatus(), $message, $isCustomerNotified);
            }
        } elseif ($order->getState() && ($orderStatus !== $order->getStatus() || $message)) {
            // add message to history if order state already declared
            $order->setState($orderState, $orderStatus, $message, $isCustomerNotified);
        } elseif (($order->getState() != $orderState) || ($order->getStatus() != $orderStatus) || $message) {
            // set order state
            $order->setState($orderState, $orderStatus, $message, $isCustomerNotified);
        }

        Mage::dispatchEvent('sales_order_payment_place_end', ['payment' => $this]);

        return $this;
    }

    /**
     * Capture the payment online
     * Requires an invoice. If there is no invoice specified, will automatically prepare an invoice for order
     * Updates transactions hierarchy, if required
     * Updates payment totals, updates order status and adds proper comments
     *
     * TODO: eliminate logic duplication with registerCaptureNotification()
     *
     * @param Mage_Sales_Model_Order_Invoice|null $invoice
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function capture($invoice)
    {
        if (is_null($invoice)) {
            $invoice = $this->_invoice();
            $this->setCreatedInvoice($invoice);
            return $this; // @see Mage_Sales_Model_Order_Invoice::capture()
        }
        $amountToCapture = $this->_formatAmount($invoice->getBaseGrandTotal());
        $order = $this->getOrder();

        // prepare parent transaction and its amount
        $paidWorkaround = 0;
        if (!$invoice->wasPayCalled()) {
            $paidWorkaround = (float) $amountToCapture;
        }
        $this->_isCaptureFinal($paidWorkaround);

        $this->_generateTransactionId(
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE,
            $this->getAuthorizationTransaction(),
        );

        Mage::dispatchEvent('sales_order_payment_capture', ['payment' => $this, 'invoice' => $invoice]);

        /**
         * Fetch an update about existing transaction. It can determine whether the transaction can be paid
         * Capture attempt will happen only when invoice is not yet paid and the transaction can be paid
         */
        if ($invoice->getTransactionId()) {
            $this->getMethodInstance()
                ->setStore($order->getStoreId())
                ->fetchTransactionInfo($this, $invoice->getTransactionId());
        }
        $status = true;
        if (!$invoice->getIsPaid() && !$this->getIsTransactionPending()) {
            // attempt to capture: this can trigger "is_transaction_pending"
            $this->getMethodInstance()->setStore($order->getStoreId())->capture($this, $amountToCapture);

            $transaction = $this->_addTransaction(
                Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE,
                $invoice,
                true,
            );

            if ($this->getIsTransactionPending()) {
                $message = Mage::helper('sales')->__('Capturing amount of %s is pending approval on gateway.', $this->_formatPrice($amountToCapture));
                $state = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
                if ($this->getIsFraudDetected()) {
                    $status = Mage_Sales_Model_Order::STATUS_FRAUD;
                }
                $invoice->setIsPaid(false);
            } else { // normal online capture: invoice is marked as "paid"
                $message = Mage::helper('sales')->__('Captured amount of %s online.', $this->_formatPrice($amountToCapture));
                $state = Mage_Sales_Model_Order::STATE_PROCESSING;
                $invoice->setIsPaid(true);
                $this->_updateTotals(['base_amount_paid_online' => $amountToCapture]);
            }
            if ($order->isNominal()) {
                $message = $this->_prependMessage(Mage::helper('sales')->__('Nominal order registered.'));
            } else {
                $message = $this->_prependMessage($message);
                $message = $this->_appendTransactionToMessage($transaction, $message);
            }
            $order->setState($state, $status, $message);
            $this->getMethodInstance()->processInvoice($invoice, $this); // should be deprecated
            return $this;
        }
        Mage::throwException(
            Mage::helper('sales')->__('The transaction "%s" cannot be captured yet.', $invoice->getTransactionId()),
        );
    }

    /**
     * Process a capture notification from a payment gateway for specified amount
     * Creates an invoice automatically if the amount covers the order base grand total completely
     * Updates transactions hierarchy, if required
     * Prevents transaction double processing
     * Updates payment totals, updates order status and adds proper comments
     *
     * TODO: eliminate logic duplication with capture()
     *
     * @param float $amount
     * @param bool $skipFraudDetection
     * @return $this
     */
    public function registerCaptureNotification($amount, $skipFraudDetection = false)
    {
        $this->_generateTransactionId(
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE,
            $this->getAuthorizationTransaction(),
        );

        $order   = $this->getOrder();
        $amount  = (float) $amount;
        $invoice = $this->_getInvoiceForTransactionId($this->getTransactionId());

        // register new capture
        if (!$invoice) {
            $isSameCurrency = $this->_isSameCurrency();
            if ($isSameCurrency && $this->_isCaptureFinal($amount)) {
                $invoice = $order->prepareInvoice()->register();
                $order->addRelatedObject($invoice);
                $this->setCreatedInvoice($invoice);
            } else {
                if (!$skipFraudDetection || !$isSameCurrency) {
                    $this->setIsFraudDetected(true);
                }
                $this->_updateTotals(['base_amount_paid_online' => $amount]);
            }
        }

        $status = true;
        if ($this->getIsTransactionPending()) {
            $message = Mage::helper('sales')->__('Capturing amount of %s is pending approval on gateway.', $this->_formatPrice($amount));
            $state = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
            if ($this->getIsFraudDetected()) {
                $message = Mage::helper('sales')->__('Order is suspended as its capture amount %s is suspected to be fraudulent.', $this->_formatPrice($amount, $this->getCurrencyCode()));
                $status = Mage_Sales_Model_Order::STATUS_FRAUD;
            }
        } else {
            $message = Mage::helper('sales')->__('Registered notification about captured amount of %s.', $this->_formatPrice($amount));
            $state = Mage_Sales_Model_Order::STATE_PROCESSING;
            if ($this->getIsFraudDetected()) {
                $state = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
                $message = Mage::helper('sales')->__('Order is suspended as its capture amount %s is suspected to be fraudulent.', $this->_formatPrice($amount, $this->getCurrencyCode()));
                $status = Mage_Sales_Model_Order::STATUS_FRAUD;
            }
            // register capture for an existing invoice
            if ($invoice && Mage_Sales_Model_Order_Invoice::STATE_OPEN == $invoice->getState()) {
                $invoice->pay();
                $this->_updateTotals(['base_amount_paid_online' => $amount]);
                $order->addRelatedObject($invoice);
            }
        }

        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE, $invoice, true);
        $message = $this->_prependMessage($message);
        $message = $this->_appendTransactionToMessage($transaction, $message);
        $order->setState($state, $status, $message);
        return $this;
    }

    /**
     * Process authorization notification
     *
     * @see self::_authorize()
     * @param float $amount
     * @return $this
     */
    public function registerAuthorizationNotification($amount)
    {
        return ($this->_isTransactionExists()) ? $this : $this->_authorize(false, $amount);
    }

    /**
     * Register payment fact: update self totals from the invoice
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return $this
     */
    public function pay($invoice)
    {
        $this->_updateTotals([
            'amount_paid' => $invoice->getGrandTotal(),
            'base_amount_paid' => $invoice->getBaseGrandTotal(),
            'shipping_captured' => $invoice->getShippingAmount(),
            'base_shipping_captured' => $invoice->getBaseShippingAmount(),
        ]);
        Mage::dispatchEvent('sales_order_payment_pay', ['payment' => $this, 'invoice' => $invoice]);
        return $this;
    }

    /**
     * Cancel specified invoice: update self totals from it
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return $this
     */
    public function cancelInvoice($invoice)
    {
        $this->_updateTotals([
            'amount_paid' => -1 * $invoice->getGrandTotal(),
            'base_amount_paid' => -1 * $invoice->getBaseGrandTotal(),
            'shipping_captured' => -1 * $invoice->getShippingAmount(),
            'base_shipping_captured' => -1 * $invoice->getBaseShippingAmount(),
        ]);
        Mage::dispatchEvent('sales_order_payment_cancel_invoice', ['payment' => $this, 'invoice' => $invoice]);
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
     * @throws Mage_Core_Exception
     */
    public function canVoid(Varien_Object $document)
    {
        if ($this->_canVoidLookup === null) {
            $this->_canVoidLookup = (bool) $this->getMethodInstance()->canVoid($document);
            if ($this->_canVoidLookup) {
                $authTransaction = $this->getAuthorizationTransaction();
                $this->_canVoidLookup = (bool) $authTransaction && !(int) $authTransaction->getIsClosed();
            }
        }
        return $this->_canVoidLookup;
    }

    /**
     * Void payment online
     *
     * @see self::_void()
     * @return $this
     */
    public function void(Varien_Object $document)
    {
        $this->_void(true);
        Mage::dispatchEvent('sales_order_payment_void', ['payment' => $this, 'invoice' => $document]);
        return $this;
    }

    /**
     * Process void notification
     *
     * @param float $amount
     * @return $this
     * @see self::_void()
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
     * @return $this
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
        $this->_updateTotals([
            'amount_refunded' => $creditmemo->getGrandTotal(),
            'base_amount_refunded' => $baseAmountToRefund,
            'base_amount_refunded_online' => $isOnline ? $baseAmountToRefund : null,
            'shipping_refunded' => $creditmemo->getShippingAmount(),
            'base_shipping_refunded' => $creditmemo->getBaseShippingAmount(),
        ]);

        // update transactions and order state
        $transaction = $this->_addTransaction(
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND,
            $creditmemo,
            $isOnline,
        );
        if ($invoice) {
            $message = Mage::helper('sales')->__('Refunded amount of %s online.', $this->_formatPrice($baseAmountToRefund));
        } else {
            $message = $this->hasMessage() ? $this->getMessage()
                : Mage::helper('sales')->__('Refunded amount of %s offline.', $this->_formatPrice($baseAmountToRefund));
        }
        $message = $this->_prependMessage($message);
        $message = $this->_appendTransactionToMessage($transaction, $message);
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);

        Mage::dispatchEvent('sales_order_payment_refund', ['payment' => $this, 'creditmemo' => $creditmemo]);
        return $this;
    }

    /**
     * Process payment refund notification
     * Updates transactions hierarchy, if required
     * Prevents transaction double processing
     * Updates payment totals, updates order status and adds proper comments
     * TODO: potentially a full capture can be refunded. In this case if there was only one invoice for that transaction
     *       then we should create a creditmemo from invoice and also refund it offline
     * TODO: implement logic of chargebacks reimbursements (via negative amount)
     *
     * @param float $amount
     * @return $this
     */
    public function registerRefundNotification($amount)
    {
        $notificationAmount = $amount;
        $this->_generateTransactionId(
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND,
            $this->_lookupTransaction($this->getParentTransactionId()),
        );
        if ($this->_isTransactionExists()) {
            return $this;
        }
        $order = $this->getOrder();
        $invoice = $this->_getInvoiceForTransactionId($this->getParentTransactionId());

        if ($invoice) {
            $baseGrandTotal = $invoice->getBaseGrandTotal();
            $amountRefundLeft = $baseGrandTotal - $invoice->getBaseTotalRefunded();
        } else {
            $baseGrandTotal = $order->getBaseGrandTotal();
            $amountRefundLeft = $baseGrandTotal - $order->getBaseTotalRefunded();
        }

        if ($amountRefundLeft < $amount) {
            $amount = $amountRefundLeft;
        }

        if (Mage::helper('core')->getExactDivision($amount, $baseGrandTotal) != 0) {
            $transaction = new Varien_Object(['txn_id' => $this->getTransactionId()]);
            Mage::dispatchEvent('sales_html_txn_id', ['transaction' => $transaction, 'payment' => $this]);
            $transactionId = $transaction->getHtmlTxnId() ? $transaction->getHtmlTxnId() : $transaction->getTxnId();
            $order->addStatusHistoryComment(Mage::helper('sales')->__(
                'IPN "Refunded". Refund issued by merchant. Registered notification about refunded amount of %s. Transaction ID: "%s". Credit Memo has not been created. Please create offline Credit Memo.',
                $this->_formatPrice($notificationAmount),
                $transactionId,
            ), false);
            return $this;
        }

        /** @var Mage_Sales_Model_Service_Order $serviceModel */
        $serviceModel = Mage::getModel('sales/service_order', $order);
        if ($invoice) {
            if ($invoice->getBaseTotalRefunded() > 0) {
                $adjustment = ['adjustment_positive' => $amount];
            } else {
                $adjustment = ['adjustment_negative' => $baseGrandTotal - $amount];
            }
            $creditmemo = $serviceModel->prepareInvoiceCreditmemo($invoice, $adjustment);
            if ($creditmemo) {
                $totalRefunded = $invoice->getBaseTotalRefunded() + $creditmemo->getBaseGrandTotal();
                $this->setShouldCloseParentTransaction($invoice->getBaseGrandTotal() <= $totalRefunded);
            }
        } else {
            if ($order->getBaseTotalRefunded() > 0) {
                $adjustment = ['adjustment_positive' => $amount];
            } else {
                $adjustment = ['adjustment_negative' => $baseGrandTotal - $amount];
            }
            $creditmemo = $serviceModel->prepareCreditmemo($adjustment);
            if ($creditmemo) {
                $totalRefunded = $order->getBaseTotalRefunded() + $creditmemo->getBaseGrandTotal();
                $this->setShouldCloseParentTransaction($order->getBaseGrandTotal() <= $totalRefunded);
            }
        }

        $creditmemo->setPaymentRefundDisallowed(true)
            ->setAutomaticallyCreated(true)
            ->register()
            ->addComment(Mage::helper('sales')->__('Credit memo has been created automatically'))
            ->save();

        $this->_updateTotals([
            'amount_refunded' => $creditmemo->getGrandTotal(),
            'base_amount_refunded_online' => $amount,
        ]);

        $this->setCreatedCreditmemo($creditmemo);
        // update transactions and order state
        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND, $creditmemo);
        $message = $this->_prependMessage(
            Mage::helper('sales')->__('Registered notification about refunded amount of %s.', $this->_formatPrice($amount)),
        );
        $message = $this->_appendTransactionToMessage($transaction, $message);
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);
        return $this;
    }

    /**
     * Cancel a credit memo: subtract its totals from the payment
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return $this
     */
    public function cancelCreditmemo($creditmemo)
    {
        $this->_updateTotals([
            'amount_refunded' => -1 * $creditmemo->getGrandTotal(),
            'base_amount_refunded' => -1 * $creditmemo->getBaseGrandTotal(),
            'shipping_refunded' => -1 * $creditmemo->getShippingAmount(),
            'base_shipping_refunded' => -1 * $creditmemo->getBaseShippingAmount(),
        ]);
        Mage::dispatchEvent(
            'sales_order_payment_cancel_creditmemo',
            ['payment' => $this, 'creditmemo' => $creditmemo],
        );
        return $this;
    }

    /**
     * Order cancellation hook for payment method instance
     * Adds void transaction if needed
     * @return $this
     */
    public function cancel()
    {
        $isOnline = true;
        if (!$this->canVoid($this)) {
            $isOnline = false;
        }

        if (!$this->hasMessage()) {
            $this->setMessage($isOnline ? Mage::helper('sales')->__('Canceled order online.')
                : Mage::helper('sales')->__('Canceled order offline.'));
        }

        if ($isOnline) {
            $this->_void($isOnline, null, 'cancel');
        }

        Mage::dispatchEvent('sales_order_payment_cancel', ['payment' => $this]);

        return $this;
    }

    /**
     * Check order payment review availability
     *
     * @return bool
     */
    public function canReviewPayment()
    {
        return (bool) $this->getMethodInstance()->canReviewPayment($this);
    }

    /**
     * Check whether fetching info of transaction could be done
     *
     * @return bool
     */
    public function canFetchTransactionInfo()
    {
        return (bool) $this->getMethodInstance()->canFetchTransactionInfo();
    }

    /**
     * Accept online a payment that is in review state
     *
     * @return $this
     */
    public function accept()
    {
        $this->registerPaymentReviewAction(self::REVIEW_ACTION_ACCEPT, true);
        return $this;
    }

    /**
     * Accept order with payment method instance
     *
     * @return $this
     */
    public function deny()
    {
        $this->registerPaymentReviewAction(self::REVIEW_ACTION_DENY, true);
        return $this;
    }

    /**
     * Perform the payment review action: either initiated by merchant or by a notification
     *
     * Sets order to processing state and optionally approves invoice or cancels the order
     *
     * @param string $action
     * @param bool $isOnline
     * @return $this
     */
    public function registerPaymentReviewAction($action, $isOnline)
    {
        $order = $this->getOrder();

        $transactionId = $isOnline ? $this->getLastTransId() : $this->getTransactionId();
        $invoice = $this->_getInvoiceForTransactionId($transactionId);

        // invoke the payment method to determine what to do with the transaction
        $result = null;
        $message = null;
        switch ($action) {
            case self::REVIEW_ACTION_ACCEPT:
                if ($isOnline) {
                    if ($this->getMethodInstance()->setStore($order->getStoreId())->acceptPayment($this)) {
                        $result = true;
                        $message = Mage::helper('sales')->__('Approved the payment online.');
                    } else {
                        $result = -1;
                        $message = Mage::helper('sales')->__('There is no need to approve this payment.');
                    }
                } else {
                    $result = (bool) $this->getNotificationResult() ? true : -1;
                    $message = Mage::helper('sales')->__('Registered notification about approved payment.');
                }
                break;
            case self::REVIEW_ACTION_DENY:
                if ($isOnline) {
                    if ($this->getMethodInstance()->setStore($order->getStoreId())->denyPayment($this)) {
                        $result = false;
                        $message = Mage::helper('sales')->__('Denied the payment online.');
                    } else {
                        $result = -1;
                        $message = Mage::helper('sales')->__('There is no need to deny this payment.');
                    }
                } else {
                    $result = (bool) $this->getNotificationResult() ? false : -1;
                    $message = Mage::helper('sales')->__('Registered notification about denied payment.');
                }
                break;
            case self::REVIEW_ACTION_UPDATE:
                if ($isOnline) {
                    $this->getMethodInstance()
                        ->setStore($order->getStoreId())
                        ->fetchTransactionInfo($this, $transactionId);
                } else {
                    // notification mechanism is responsible to update the payment object first
                }
                if ($this->getIsTransactionApproved()) {
                    $result = true;
                    $message = Mage::helper('sales')->__('Registered update about approved payment.');
                } elseif ($this->getIsTransactionDenied()) {
                    $result = false;
                    $message = Mage::helper('sales')->__('Registered update about denied payment.');
                } else {
                    $result = -1;
                    $message = Mage::helper('sales')->__('There is no update for the payment.');
                }
                break;
            default:
                throw new Exception('Not implemented.');
        }
        $message = $this->_prependMessage($message);
        if ($transactionId) {
            $message = $this->_appendTransactionToMessage($transactionId, $message);
        }

        // process payment in case of positive or negative result, or add a comment
        if ($result === -1) { // switch won't work with such $result!
            if ($order -> getState() != Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW) {
                $status = $this->getIsFraudDetected() ? Mage_Sales_Model_Order::STATUS_FRAUD : false;
                $order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, $status, $message);
                if ($transactionId) {
                    $this->setLastTransId($transactionId);
                }
            } else {
                $order->addStatusHistoryComment($message);
            }
        } elseif ($result === true) {
            if ($invoice) {
                $invoice->pay();
                $this->_updateTotals(['base_amount_paid_online' => $invoice->getBaseGrandTotal()]);
                $order->addRelatedObject($invoice);
            }
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);
        } elseif ($result === false) {
            if ($invoice) {
                $invoice->cancel();
                $order->addRelatedObject($invoice);
            }
            $order->registerCancellation($message, false);
        }
        return $this;
    }

    /**
     * Order payment either online
     * Updates transactions hierarchy, if required
     * Prevents transaction double processing
     * Updates payment totals, updates order status and adds proper comments
     *
     * @param float $amount
     * @return $this
     */
    protected function _order($amount)
    {
        // update totals
        $amount = $this->_formatAmount($amount, true);

        // do ordering
        $order  = $this->getOrder();
        $state  = Mage_Sales_Model_Order::STATE_PROCESSING;
        $status = true;
        $this->getMethodInstance()->setStore($order->getStoreId())->order($this, $amount);

        if ($this->getSkipOrderProcessing()) {
            return $this;
        }

        // similar logic of "payment review" order as in capturing
        if ($this->getIsTransactionPending()) {
            $message = Mage::helper('sales')->__('Ordering amount of %s is pending approval on gateway.', $this->_formatPrice($amount));
            $state = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
            if ($this->getIsFraudDetected()) {
                $status = Mage_Sales_Model_Order::STATUS_FRAUD;
            }
        } else {
            $message = Mage::helper('sales')->__('Ordered amount of %s.', $this->_formatPrice($amount));
        }

        // update transactions, order state and add comments
        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER);
        $message = $this->_prependMessage($message);
        $message = $this->_appendTransactionToMessage($transaction, $message);
        $order->setState($state, $status, $message);
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
     * @return $this
     */
    protected function _authorize($isOnline, $amount)
    {
        // check for authorization amount to be equal to grand total
        $this->setShouldCloseParentTransaction(false);
        $isSameCurrency = $this->_isSameCurrency();
        if (!$isSameCurrency || !$this->_isCaptureFinal($amount)) {
            $this->setIsFraudDetected(true);
        }

        // update totals
        $amount = $this->_formatAmount($amount, true);
        $this->setBaseAmountAuthorized($amount);

        // do authorization
        $order  = $this->getOrder();
        $state  = Mage_Sales_Model_Order::STATE_PROCESSING;
        $status = true;
        if ($isOnline) {
            // invoke authorization on gateway
            $this->getMethodInstance()->setStore($order->getStoreId())->authorize($this, $amount);
        }

        // similar logic of "payment review" order as in capturing
        if ($this->getIsTransactionPending()) {
            $message = Mage::helper('sales')->__('Authorizing amount of %s is pending approval on gateway.', $this->_formatPrice($amount));
            $state = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
            if ($this->getIsFraudDetected()) {
                $status = Mage_Sales_Model_Order::STATUS_FRAUD;
            }
        } else {
            if ($this->getIsFraudDetected()) {
                $state = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
                $message = Mage::helper('sales')->__('Order is suspended as its authorizing amount %s is suspected to be fraudulent.', $this->_formatPrice($amount, $this->getCurrencyCode()));
                $status = Mage_Sales_Model_Order::STATUS_FRAUD;
            } else {
                $message = Mage::helper('sales')->__('Authorized amount of %s.', $this->_formatPrice($amount));
            }
        }

        // update transactions, order state and add comments
        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
        if ($order->isNominal()) {
            $message = $this->_prependMessage(Mage::helper('sales')->__('Nominal order registered.'));
        } else {
            $message = $this->_prependMessage($message);
            $message = $this->_appendTransactionToMessage($transaction, $message);
        }
        $order->setState($state, $status, $message);

        return $this;
    }

    /**
     * Public access to _authorize method
     * @param bool $isOnline
     * @param float $amount
     * @return $this
     */
    public function authorize($isOnline, $amount)
    {
        return $this->_authorize($isOnline, $amount);
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
     * @return $this
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
        }
        if ($this->_isTransactionExists()) {
            return $this;
        }

        // if the authorization was untouched, we may assume voided amount = order grand total
        // but only if the payment auth amount equals to order grand total
        if ($authTransaction && ($order->getBaseGrandTotal() == $this->getBaseAmountAuthorized())
            && ($this->getBaseAmountCanceled() == 0)
        ) {
            if ($authTransaction->canVoidAuthorizationCompletely()) {
                $amount = (float) $order->getBaseGrandTotal();
            }
        }

        if ($amount) {
            $amount = $this->_formatAmount($amount);
        }

        // update transactions, order state and add comments
        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID, null, true);
        $message = $this->hasMessage() ? $this->getMessage() : Mage::helper('sales')->__('Voided authorization.');
        $message = $this->_prependMessage($message);
        if ($amount) {
            $message .= ' ' . Mage::helper('sales')->__('Amount: %s.', $this->_formatPrice($amount));
        }
        $message = $this->_appendTransactionToMessage($transaction, $message);
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);
        return $this;
    }

    //    /**
    //     * TODO: implement this
    //     * @param Mage_Sales_Model_Order_Invoice $invoice
    //     * @return $this
    //     */
    //    public function cancelCapture($invoice = null)
    //    {
    //    }

    /**
     * Create transaction,
     * prepare its insertion into hierarchy and add its information to payment and comments
     *
     * To add transactions and related information,
     * the following information should be set to payment before processing:
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
     * @return null|Mage_Sales_Model_Order_Payment_Transaction|void
     */
    protected function _addTransaction($type, $salesDocument = null, $failsafe = false)
    {
        if ($this->getSkipTransactionCreation()) {
            $this->unsTransactionId();
            return null;
        }

        // look for set transaction ids
        $transactionId = $this->getTransactionId();
        if ($transactionId !== null) {
            // set transaction parameters
            $transaction = false;
            if ($this->getOrder()->getId()) {
                $transaction = $this->_lookupTransaction($transactionId);
            }
            if (!$transaction) {
                $transaction = Mage::getModel('sales/order_payment_transaction')->setTxnId($transactionId);
            }
            $transaction
                ->setOrderPaymentObject($this)
                ->setTxnType($type)
                ->isFailsafe($failsafe);

            if ($this->hasIsTransactionClosed()) {
                $transaction->setIsClosed((int) $this->getIsTransactionClosed());
            }

            //set transaction addition information
            if ($this->_transactionAdditionalInfo) {
                foreach ($this->_transactionAdditionalInfo as $key => $value) {
                    $transaction->setAdditionalInformation($key, $value);
                }
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
                        if (!$parentTransaction->getIsClosed()) {
                            $parentTransaction->isFailsafe($failsafe)->close(false);
                        }
                        $this->getOrder()->addRelatedObject($parentTransaction);
                    }
                }
            }
            return $transaction;
        }
    }

    /**
     * Public access to _addTransaction method
     *
     * @param string $type
     * @param Mage_Sales_Model_Abstract $salesDocument
     * @param bool $failsafe
     * @param string|false $message
     * @return null|Mage_Sales_Model_Order_Payment_Transaction
     */
    public function addTransaction($type, $salesDocument = null, $failsafe = false, $message = false)
    {
        $transaction = $this->_addTransaction($type, $salesDocument, $failsafe);

        if ($message) {
            $order = $this->getOrder();
            $message = $this->_appendTransactionToMessage($transaction, $message);
            $order->addStatusHistoryComment($message);
        }

        return $transaction;
    }

    /**
     * Import details data of specified transaction
     *
     * @return $this
     */
    public function importTransactionInfo(Mage_Sales_Model_Order_Payment_Transaction $transactionTo)
    {
        $data = $this->getMethodInstance()
            ->setStore($this->getOrder()->getStoreId())
            ->fetchTransactionInfo($this, $transactionTo->getTxnId());
        if ($data) {
            $transactionTo->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $data);
        }
        return $this;
    }

    /**
     * Get the billing agreement, if any
     *
     * @return Mage_Sales_Model_Billing_Agreement|null
     */
    public function getBillingAgreement()
    {
        return $this->_billingAgreement;
    }

    /**
     * Totals updater utility method
     * Updates self totals by keys in data array('key' => $delta)
     *
     * @param array $data
     */
    protected function _updateTotals($data)
    {
        foreach ($data as $key => $amount) {
            if ($amount !== null) {
                $was = $this->getDataUsingMethod($key);
                $this->setDataUsingMethod($key, $was + $amount);
            }
        }
    }

    /**
     * Prevent double processing of the same transaction by a payment notification
     * Uses either specified txn_id or the transaction id that was set before
     *
     * @deprecated after 1.4.0.1
     * @param string $txnId
     * @throws Mage_Core_Exception
     */
    protected function _avoidDoubleTransactionProcessing($txnId = null)
    {
        if ($this->_isTransactionExists($txnId)) {
            Mage::throwException(
                Mage::helper('sales')->__('Transaction "%s" was already processed.', $txnId),
            );
        }
    }

    /**
     * Check transaction existence by specified transaction id
     *
     * @param string $txnId
     * @return bool
     */
    protected function _isTransactionExists($txnId = null)
    {
        if ($txnId === null) {
            $txnId = $this->getTransactionId();
        }
        return $txnId && $this->_lookupTransaction($txnId);
    }

    /**
     * Append transaction ID (if any) message to the specified message
     *
     * @param Mage_Sales_Model_Order_Payment_Transaction|string|null $transaction
     * @param string $message
     * @return string
     */
    protected function _appendTransactionToMessage($transaction, $message)
    {
        if ($transaction) {
            $txnId = is_object($transaction) ? $transaction->getHtmlTxnId() : $transaction;
            $message .= ' ' . Mage::helper('sales')->__('Transaction ID: "%s".', $txnId);
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
    protected function _prependMessage($messagePrependTo)
    {
        $preparedMessage = $this->getPreparedMessage();
        if ($preparedMessage) {
            if (is_string($preparedMessage)) {
                return $preparedMessage . ' ' . $messagePrependTo;
            } elseif (is_object($preparedMessage)
                && ($preparedMessage instanceof Mage_Sales_Model_Order_Status_History)
            ) {
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
        $amount = Mage::app()->getStore()->roundPrice($amount);
        return !$asFloat ? (string) $amount : $amount;
    }

    /**
     * Format price with currency sign
     * @param float $amount
     * @param null|string $currency
     * @return string
     */
    protected function _formatPrice($amount, $currency = null)
    {
        return $this->getOrder()->getBaseCurrency()->formatTxt(
            $amount,
            $currency ? ['currency' => $currency] : [],
        );
    }

    /**
     * Find one transaction by ID or type
     * @param string|false|null $txnId
     * @param string|false $txnType
     * @return Mage_Sales_Model_Order_Payment_Transaction|false
     */
    protected function _lookupTransaction($txnId, $txnType = false)
    {
        if (!$txnId) {
            if ($txnType && $this->getId()) {
                $collection = Mage::getModel('sales/order_payment_transaction')->getCollection()
                    ->setOrderFilter($this->getOrder())
                    ->addPaymentIdFilter($this->getId())
                    ->addTxnTypeFilter($txnType)
                    ->setOrder('created_at', Varien_Data_Collection::SORT_ORDER_DESC)
                    ->setOrder('transaction_id', Varien_Data_Collection::SORT_ORDER_DESC);
                /** @var Mage_Sales_Model_Order_Payment_Transaction $txn */
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
     * Find one transaction by ID or type
     * @param string|false $txnId
     * @param string|false $txnType
     * @return Mage_Sales_Model_Order_Payment_Transaction|false
     */
    public function lookupTransaction($txnId, $txnType = false)
    {
        return $this->_lookupTransaction($txnId, $txnType);
    }

    /**
     * Lookup an authorization transaction using parent transaction id, if set
     * @return Mage_Sales_Model_Order_Payment_Transaction|false
     */
    public function getAuthorizationTransaction()
    {
        if ($this->getParentTransactionId()) {
            $txn = $this->_lookupTransaction($this->getParentTransactionId());
        } else {
            $txn = false;
        }

        if (!$txn) {
            $txn = $this->_lookupTransaction(false, Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
        }
        return $txn;
    }

    /**
     * Lookup the transaction by id
     * @param string $transactionId
     * @return Mage_Sales_Model_Order_Payment_Transaction|false
     */
    public function getTransaction($transactionId)
    {
        return $this->_lookupTransaction($transactionId);
    }

    /**
     * Update transaction ids for further processing
     * If no transactions were set before invoking, may generate an "offline" transaction id
     *
     * @param string $type
     * @param Mage_Sales_Model_Order_Payment_Transaction|false $transactionBasedOn
     */
    protected function _generateTransactionId($type, $transactionBasedOn = false)
    {
        if (!$this->getParentTransactionId() && !$this->getTransactionId() && $transactionBasedOn) {
            $this->setParentTransactionId($transactionBasedOn->getTxnId());
        }
        // generate transaction id for an offline action or payment method that didn't set it
        $parentTxnId = $this->getParentTransactionId();
        if ($parentTxnId && !$this->getTransactionId()) {
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
        $amountToCapture = $this->_formatAmount($amountToCapture, true);
        $orderGrandTotal = $this->_formatAmount($this->getOrder()->getBaseGrandTotal(), true);
        if ($orderGrandTotal == $this->_formatAmount($this->getBaseAmountPaid(), true) + $amountToCapture) {
            if ($this->getShouldCloseParentTransaction() !== false) {
                $this->setShouldCloseParentTransaction(true);
            }
            return true;
        }
        return false;
    }

    /**
     * Check whether payment currency corresponds to order currency
     *
     * @return bool
     */
    protected function _isSameCurrency()
    {
        return !$this->getCurrencyCode() || $this->getCurrencyCode() == $this->getOrder()->getBaseCurrencyCode();
    }

    /**
     * Before object save manipulations
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getOrder()) {
            $this->setParentId($this->getOrder()->getId());
        }

        return $this;
    }

    /**
     * Generate billing agreement object if there is billing agreement data
     * Adds it to order as related object
     */
    protected function _createBillingAgreement()
    {
        if ($this->getBillingAgreementData()) {
            $order = $this->getOrder();
            $agreement = Mage::getModel('sales/billing_agreement')->importOrderPayment($this);
            if ($agreement->isValid()) {
                $message = Mage::helper('sales')->__('Created billing agreement #%s.', $agreement->getReferenceId());
                $order->addRelatedObject($agreement);
                $this->_billingAgreement = $agreement;
            } else {
                $message = Mage::helper('sales')->__('Failed to create billing agreement for this order.');
            }
            $comment = $order->addStatusHistoryComment($message);
            $order->addRelatedObject($comment);
        }
    }

    /**
     * Additional transaction info setter
     *
     * @param string $key
     * @param string $value
     */
    public function setTransactionAdditionalInfo($key, $value)
    {
        if (is_array($key)) {
            $this->_transactionAdditionalInfo = $key;
        } else {
            $this->_transactionAdditionalInfo[$key] = $value;
        }
    }

    /**
     * Additional transaction info getter
     *
     * @param string $key
     * @return mixed
     */
    public function getTransactionAdditionalInfo($key = null)
    {
        if (is_null($key)) {
            return $this->_transactionAdditionalInfo;
        }
        return $this->_transactionAdditionalInfo[$key] ?? null;
    }

    /**
     * Reset transaction additional info property
     *
     * @return $this
     */
    public function resetTransactionAdditionalInfo()
    {
        $this->_transactionAdditionalInfo = [];
        return $this;
    }

    /**
     * Return invoice model for transaction
     *
     * @param string $transactionId
     * @return Mage_Sales_Model_Order_Invoice|false
     */
    protected function _getInvoiceForTransactionId($transactionId)
    {
        foreach ($this->getOrder()->getInvoiceCollection() as $invoice) {
            if ($invoice->getTransactionId() == $transactionId) {
                $invoice->load($invoice->getId()); // to make sure all data will properly load (maybe not required)
                return $invoice;
            }
        }
        foreach ($this->getOrder()->getInvoiceCollection() as $invoice) {
            if ($invoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_OPEN
                && $invoice->load($invoice->getId())
            ) {
                $invoice->setTransactionId($transactionId);
                return $invoice;
            }
        }
        return false;
    }
}
