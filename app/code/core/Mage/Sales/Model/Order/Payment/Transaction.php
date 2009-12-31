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
 * Payment transaction model
 * Tracks transaction history, allows to build transactions hierarchy
 * By default transactions are saved as closed.
 */
class Mage_Sales_Model_Order_Payment_Transaction extends Mage_Core_Model_Abstract
{
    /**
     * Supported transaction types
     * @var string
     */
    const TYPE_PAYMENT = 'payment';
    const TYPE_AUTH    = 'authorization';
    const TYPE_CAPTURE = 'capture';
    const TYPE_VOID    = 'void';
    const TYPE_REFUND  = 'refund';

    /**
     * Payment instance. Required for most transaction writing and search operations
     * @var Mage_Sales_Model_Order_Payment
     */
    protected $_paymentObject = null;

    /**
     * Parent transaction instance
     * @var Mage_Sales_Model_Order_Payment_Transaction
     */
    protected $_parentTransaction = null;

    /**
     * Child transactions, assoc array of transaction_id => instance
     * @var array
     */
    protected $_children = null;

    /**
     * Child transactions, assoc array of txn_id => instance
     * Filled only in case when all child transactions have txn_id
     * Used for quicker search of child transactions using isset() as oposite to foreaching $_children
     * @var array
     */
    protected $_identifiedChildren = null;

    /**
     * Whether to perform automatic actions on transactions, such as auto-closing and putting as a parent
     * @var bool
     */
    protected $_transactionsAutoLinking = true;

    /**
     * Whether to throw exceptions on different operations
     *
     * @var bool
     */
    protected $_isFailsafe = false;

    /**
     * Whether transaction has children
     * @var bool
     */
    private $_hasChild = null;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order_payment_transaction');
        return parent::_construct();
    }

    /**
     * Payment instance setter
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Sales_Model_Order_Payment_Transaction
     */
    public function setOrderPaymentObject(Mage_Sales_Model_Order_Payment $payment)
    {
        $this->_paymentObject = $payment;
        return $this;
    }

    /**
     * Transaction ID setter
     * @param string $txnId
     * @return Mage_Sales_Model_Order_Payment_Transaction
     */
    public function setTxnId($txnId)
    {
        $this->_verifyTxnId($txnId);
        return $this->setData('txn_id', $txnId);
    }

    /**
     * Parent transaction ID setter
     * Can set the transaction id as well
     * @param string $parentTxnId
     * @param string $txnId
     * @return Mage_Sales_Model_Order_Payment_Transaction
     * @throws Mage_Core_Exception
     */
    public function setParentTxnId($parentTxnId, $txnId = null)
    {
        $this->_verifyTxnId($parentTxnId);
        if (empty($txnId)) {
            if ('' == $this->getTxnId()) {
                Mage::throwException(
                    Mage::helper('sales')->__('Parent transaction ID must have a transaction ID.')
                );
            }
        } else {
            $this->setTxnId($txnId);
        }
        return $this->setData('parent_txn_id', $parentTxnId);
    }

    /**
     * Transaction type setter
     *
     * @param $txnType
     * @return Mage_Sales_Model_Order_Payment_Transaction
     */
    public function setTxnType($txnType)
    {
        $this->_verifyTxnType($txnType);
        return $this->setData('txn_type', $txnType);
    }

    /**
     * Parent transaction getter
     * May attempt to load it.
     *
     * @param bool $shouldLoad
     * @return Mage_Sales_Model_Order_Payment_Transaction|false
     */
    public function getParentTransaction($shouldLoad = true)
    {
        if (null === $this->_parentTransaction) {
            $this->_verifyThisTransactionExists();
            $this->_parentTransaction = false;
            $parentId = $this->getParentId();
            if ($parentId) {
                $class = get_class($this);
                $this->_parentTransaction = new $class;
                if ($shouldLoad) {
                    $this->_parentTransaction->load($parentId);
                    if (!$this->_parentTransaction->getId()) {
                        $this->_parentTransaction = false;
                    } else {
                        $this->_parentTransaction
                            ->hasChildTransaction(true)
                            ->setOrderPaymentObject($this->_paymentObject);
                    }
                }
            }
        }
        return $this->_parentTransaction;
    }

    /**
     * Child transaction(s) getter
     * Will attempt to load them first
     * Can be filtered by types and/or transaction_id
     * Returns transaction object if transaction_id is specified, otherwise - array
     * TODO: $recursive is not implemented
     *
     * @param array|string $types
     * @param string $txnId
     * @param bool $recursive
     * @return Mage_Sales_Model_Order_Payment_Transaction|array|null
     */
    public function getChildTransactions($types = null, $txnId = null, $recursive = false)
    {
        $this->_loadChildren();

        // grab all transactions
        if (empty($types) && null === $txnId) {
            return $this->_children;
        } elseif ($types && !is_array($types)) {
            $types = array($types);
        }

        // get a specific transaction
        if ($txnId) {
            if (empty($this->_children)) {
                return;
            }
            $transaction = null;
            if ($this->_identifiedChildren) {
                if (isset($this->_identifiedChildren[$txnId])) {
                    $transaction = $this->_identifiedChildren[$txnId];
                }
            } else {
                foreach ($this->_children as $child) {
                    if ($child->getTxnId() === $tnxId) {
                        $transaction = $child;
                        break;
                    }
                }
            }
            // return transaction only if type matches
            if (!$transaction || $types && !in_array($transaction->getType(), $types, true)) {
                return;
            }
            return $transaction;
        }

        // filter transactions by types
        $result = array();
        foreach ($this->_children as $child) {
            if (in_array($child->getType(), $types, true)) {
                $result[$child->getId()] = $child;
            }
        }
        return $result;
    }

    /**
     * Close an authorization transaction
     * This method can be invoked from any child transaction of the transaction to be closed
     * Returns the authorization transaction on success. Otherwise false.
     * $dryRun = true prevents actual closing, it just allows to check whether this operation is possible
     *
     * @param bool $shouldSave
     * @param bool $dryRun
     * @return Mage_Sales_Model_Order_Payment_Transaction|false
     * @throws Exception
     */
    public function closeAuthorization($shouldSave = true, $dryRun = false)
    {
        try {
            $this->_verifyThisTransactionExists();
        } catch (Exception $e) {
            if ($dryRun) {
                return false;
            }
            throw $e;
        }
        $authTransaction = false;
        switch ($this->getTxnType()) {
            case self::TYPE_VOID:
                // break intentionally omitted
            case self::TYPE_CAPTURE:
                $authTransaction = $this->getParentTransaction();
                break;
            case self::TYPE_AUTH:
                $authTransaction = $this;
                break;
            // case self::TYPE_PAYMENT?
        }
        if ($authTransaction) {
            if (!$dryRun) {
                $authTransaction->close($shouldSave);
            }
        }
        return $authTransaction;
    }

    /**
     * Close a capture transaction
     * Logic is similar to closeAuthorization(), but for a capture transaction
     * @see self::closeAuthorization()
     * @para, bool $shouldSave
     * @param unknown_type $shouldSave
     */
    public function closeCapture($shouldSave = true)
    {
        $this->_verifyThisTransactionExists();
        $captureTransaction = false;
        switch ($this->getTxnType()) {
            case self::TYPE_CAPTURE:
                $captureTransaction = $this;
                break;
            case self::TYPE_REFUND:
                $captureTransaction = $this->getParentTransaction();
                break;
        }
        if ($captureTransaction) {
            $captureTransaction->close($shouldSave);
        }
        return $captureTransaction;
    }

    /**
     * Check whether authorization in current hierarchy can be voided completely
     * Basically checks whether the authorization exists and it is not affected by a capture or void
     * @return bool
     */
    public function canVoidAuthorizationCompletely()
    {
        try {
            $authTransaction = $this->closeAuthorization('', true);
            if ($authTransaction->hasChildTransaction() || $this->_children) {
                return false;
            }
            return true;
        } catch (Mage_Core_Exception $e) {
            // jam all logical exceptions, fallback to false
        }
        return false;
    }

    /**
     * Getter/Setter of whether current transaction has a child transaction
     * @param bool $whetherHasChild
     * @return bool|Mage_Sales_Model_Order_Payment_Transaction
     */
    public function hasChildTransaction($whetherHasChild = null)
    {
        if (null !== $whetherHasChild) {
            $this->_hasChild = (bool)$whetherHasChild;
            return $this;
        }
        elseif (null === $this->_hasChild) {
            if ($this->getChildTransactions()) {
                $this->_hasChild = true;
            } else {
                $this->_hasChild = false;
            }
        }
        return $this->_hasChild;
    }

    /**
     * Load self by specified transaction ID. Requires the valid payment object to be set
     * @param string $txnId
     * @return Mage_Sales_Model_Order_Payment_Transaction
     */
    public function loadByTxnId($txnId)
    {
        $this->_verifyPaymentObject();
        $this->getResource()->loadObjectByTxnId(
            $this, $this->getOrderId(), $this->_paymentObject->getId(), $txnId
        );
        return $this;
    }

    /**
     * Additional information setter
     * Updates data inside the 'additional_information' array
     * Doesn't allow to set arrays
     *
     * @param string $key
     * @param mixed $value
     * @return Mage_Sales_Model_Order_Payment_Transaction
     * @throws Mage_Core_Exception
     */
    public function setAdditionalInformation($key, $value)
    {
        if (is_object($value)) {
            Mage::throwException(Mage::helper('sales')->__('Payment transactions disallow storing objects.'));
        }
        $info = $this->_getData('additional_information');
        if (!$info) {
            $info = array();
        }
        $info[$key] = $value;
        return $this->setData('additional_information', $info);
    }

    /**
     * Getter for entire additional_information value or one of its element by key
     * @param string $key
     * @return array|null|mixed
     */
    public function getAdditionalInformation($key = null)
    {
        $info = $this->_getData('additional_information');
        if (!$info) {
            $info = array();
        }
        if ($key) {
            return (isset($info[$key]) ? $info[$key] : null);
        }
        return $info;
    }

    /**
     * Unsetter for entire additional_information value or one of its element by key
     * @param string $key
     * @return Mage_Sales_Model_Order_Payment_Transaction
     */
    public function unsAdditionalInformation($key = null)
    {
        if ($key) {
            $info = $this->_getData('additional_information');
            if (is_array($info)) {
                unset($info[$key]);
            }
        } else {
            $info = array();
        }
        return $this->setData('additional_information', $info);
    }

    /**
     * Close this transaction
     * @param bool $shouldSave
     * @return Mage_Sales_Model_Order_Payment_Transaction
     * @throws Mage_Core_Exception
     */
    public function close($shouldSave = true)
    {
        if (!$this->_isFailsafe) {
            $this->_verifyThisTransactionExists();
        }
        if (1 == $this->getIsClosed() && $this->_isFailsafe) {
            Mage::throwException(Mage::helper('sales')->__('The transaction "%s" (%s) is already closed.', $this->getTxnId(), $this->getTxnType()));
        }
        $this->setIsClosed(1);
        if ($shouldSave) {
            $this->save();
        }
        if ($this->_transactionsAutoLinking && self::TYPE_AUTH === $this->getTxnType()) {
            try {
                $paymentTransaction = $this->getParentTransaction();
                if ($paymentTransaction) {
                    $paymentTransaction->close($shouldSave);
                }
            } catch (Exception $e) {
                if (!$this->_isFailsafe) {
                    throw $e;
                }
            }
        }
        return $this;
    }

    /**
     * Order Payment instance getter
     * Will attempt to load by payment_id if it is set in data
     * @param bool $shouldLoad
     * @return Mage_Sales_Model_Order_Payment
     */
    public function getOrderPaymentObject($shouldLoad = true)
    {
        $this->_verifyThisTransactionExists();
        if (null === $this->_paymentObject && $shouldLoad) {
            $payment = Mage::getModel('sales/order_payment')->load($this->getPaymentId());
            if ($payment->getId()) {
                $this->setOrderPaymentObject($payment);
            }
        }
        return $this->_paymentObject;
    }

    /**
     * Order ID getter
     * Attempts to get ID from set order payment object, if any, or from data by key 'order_id'
     * @return int|null
     */
    public function getOrderId()
    {
        $orderId = $this->_getData('order_id');
        if ($orderId) {
            return $orderId;
        }
        if ($this->_paymentObject) {
            return $this->_paymentObject->getOrder()
                ? $this->_paymentObject->getOrder()->getId()
                : $this->_paymentObject->getParentId();
        }
    }

    /**
     * Setter/Getter whether transaction is supposed to prevent exceptions on saving
     *
     * @param bool $failsafe
     */
    public function isFailsafe($setFailsafe = null)
    {
        if (null === $setFailsafe) {
            return $this->_isFailsafe;
        }
        $this->_isFailsafe = (bool)$setFailsafe;
        return $this;
    }

    /**
     * Verify data required for saving
     * @return Mage_Sales_Model_Order_Payment_Transaction
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        // set parent id
        $this->_verifyPaymentObject();
        $this->setPaymentId($this->_paymentObject->getId())
            ->setOrderId($this->getOrderId());
        return parent::_beforeSave();
    }

    /**
     * Load child transactions
     * @throws Mage_Core_Exception
     */
    protected function _loadChildren()
    {
        if (null !== $this->_children) {
            return;
        }

        // make sure minimum required data is set
        $this->_verifyThisTransactionExists();
        $payment = $this->_verifyPaymentObject(true);
        $paymentId = $payment ? $payment->getId() : $this->_getData('payment_id');
        if (!$paymentId) {
            Mage::throwException(Mage::helper('sales')->__('At least a payment ID must be set.'));
        }

        // prepare children collection
        $children = $this->getResourceCollection()
            ->addPaymentIdFilter($paymentId)
            ->addParentIdFilter($this->getId());

        // set basic children array and attempt to map them per txn_id, if all of them have txn_id
        $this->_children = array();
        $this->_identifiedChildren = array();
        foreach ($children as $child) {
            if ($payment) {
                $child->setOrderPaymentObject($payment);
            }
            $this->_children[$child->getId()] = $child;
            if (false !== $this->_identifiedChildren) {
                $childTxnId = $child->getTxnId();
                if (!$childTxnId || '0' == $childTxnId) {
                    $this->_identifiedChildren = false;
                } else {
                    $this->_identifiedChildren[$child->getTxnId()] = $child;
                }
            }
        }
        if (false === $this->_identifiedChildren) {
            $this->_identifiedChildren = array();
        }
    }

    /**
     * Check whether this transaction is voided
     * TODO: implement that there should be only one void per authorization
     * @return bool
     */
    private function _isVoided()
    {
        $this->_verifyThisTransactionExists();
        return self::TYPE_AUTH === $this->getTxnType()
            && (bool)count($this->getChildTransactions(self::TYPE_VOID));
    }

    /**
     * Check whether specified or set transaction type is supported
     * @param string $txnType
     * @throws Mage_Core_Exception
     */
    private function _verifyTxnType($txnType = null)
    {
        if (null === $txnType) {
            $txnType = $this->getTxnType();
        }
        switch ($txnType) {
            case self::TYPE_PAYMENT:
            case self::TYPE_AUTH:
            case self::TYPE_CAPTURE:
            case self::TYPE_VOID:
            case self::TYPE_REFUND:
                break;
            default:
                Mage::throwException(Mage::helper('sales')->__('Non-supported Transaction Type "%s".', $txnType));
        }
    }

    /**
     * Check whether the payment object is set and it has order object or there is an order_id is set
     * $dryRun allows to not throw exception
     * @param bool $dryRun
     * @return Mage_Sales_Model_Order_Payment|null|false
     * @throws Mage_Core_Exception
     */
    private function _verifyPaymentObject($dryRun = false)
    {
        if (!$this->_paymentObject || !$this->getOrderId()) {
            if (!$dryRun) {
                Mage::throwException(Mage::helper('sales')->__('Proper payment object must be set.'));
            }
        }
        return $this->_paymentObject;
    }

    /**
     * Check whether specified transaction ID is valid
     * @param string $txnId
     * @throws Mage_Core_Exception
     */
    private function _verifyTxnId($txnId)
    {
        if (null !== $txnId && 0 == strlen($txnId)) {
            Mage::throwException(Mage::helper('sales')->__('Transaction ID must not be empty.'));
        }
    }

    /**
     * Make sure this object is a valid transaction
     * TODO for more restriction we can check for data consistency
     * @throws Mage_Core_Exception
     */
    private function _verifyThisTransactionExists()
    {
        if (!$this->getId()) {
            Mage::throwException(Mage::helper('sales')->__('This operation requires an existing transaction object.'));
        }
        $this->_verifyTxnType();
    }
}
