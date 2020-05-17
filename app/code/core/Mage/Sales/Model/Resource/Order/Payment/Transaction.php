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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales transaction resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Payment_Transaction extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Serializeable field: additional_information
     *
     * @var array
     */
    protected $_serializableFields   = array(
        'additional_information' => array(null, array())
    );

    /**
     * Initialize main table and the primary key field name
     *
     */
    protected function _construct()
    {
        $this->_init('sales/payment_transaction', 'transaction_id');
    }

    /**
     * Unserialize Varien_Object field in an object
     *
     * @param Varien_Object $object
     * @param string $field
     * @param mixed $defaultValue
     */
    protected function _unserializeField(Varien_Object $object, $field, $defaultValue = null)
    {
        $value = $object->getData($field);
        if (empty($value)) {
            $object->setData($field, $defaultValue);
        } elseif (!is_array($value) && !is_object($value)) {
            $unserializedValue = false;
            try {
                $unserializedValue = Mage::helper('core/unserializeArray')
                ->unserialize($value);
            } catch (Exception $e) {
                Mage::logException($e);
            }
            $object->setData($field, $unserializedValue);
        }
    }

    /**
     * Update transactions in database using provided transaction as parent for them
     * have to repeat the business logic to avoid accidental injection of wrong transactions
     *
     * @param Mage_Sales_Model_Order_Payment_Transaction $transaction
     */
    public function injectAsParent(Mage_Sales_Model_Order_Payment_Transaction $transaction)
    {
        $txnId = $transaction->getTxnId();
        if ($txnId && Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT === $transaction->getTxnType()
            && $id = $transaction->getId()
        ) {
            $adapter = $this->_getWriteAdapter();

            // verify such transaction exists, determine payment and order id
            $verificationRow = $adapter->fetchRow(
                $adapter->select()->from($this->getMainTable(), array('payment_id', 'order_id'))
                    ->where("{$this->getIdFieldName()} = ?", (int)$id)
            );
            if (!$verificationRow) {
                return;
            }
            list($paymentId, $orderId) = array_values($verificationRow);

            // inject
            $where = array(
                $adapter->quoteIdentifier($this->getIdFieldName()) . '!=?' => $id,
                new Zend_Db_Expr('parent_id IS NULL'),
                'payment_id = ?'    => (int)$paymentId,
                'order_id = ?'      => (int)$orderId,
                'parent_txn_id = ?' => $txnId
            );
            $adapter->update(
                $this->getMainTable(),
                array('parent_id' => $id),
                $where
            );
        }
    }

    /**
     * Load the transaction object by specified txn_id
     *
     * @param Mage_Sales_Model_Order_Payment_Transaction $transaction
     * @param int $orderId
     * @param int $paymentId
     * @param string $txnId
     */
    public function loadObjectByTxnId(
        Mage_Sales_Model_Order_Payment_Transaction $transaction,
        $orderId,
        $paymentId,
        $txnId
    ) {
        $select = $this->_getLoadByUniqueKeySelect($orderId, $paymentId, $txnId);
        $data   = $this->_getWriteAdapter()->fetchRow($select);
        $transaction->setData($data);
        $this->unserializeFields($transaction);
        $this->_afterLoad($transaction);
    }

    /**
     * Retrieve order website id
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderWebsiteId($orderId)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array(':entity_id' => $orderId);
        $select  = $adapter->select()
            ->from(array('so' => $this->getTable('sales/order')), 'cs.website_id')
            ->joinInner(array('cs' => $this->getTable('core/store')), 'cs.store_id = so.store_id')
            ->where('so.entity_id = :entity_id');
        return $adapter->fetchOne($select, $bind);
    }

    /**
     * Lookup for parent_id in already saved transactions of this payment by the order_id
     * Also serialize additional information, if any
     *
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $transaction)
    {
        $parentTxnId = $transaction->getData('parent_txn_id');
        $txnId       = $transaction->getData('txn_id');
        $orderId     = $transaction->getData('order_id');
        $paymentId   = $transaction->getData('payment_id');
        $idFieldName = $this->getIdFieldName();

        if ($parentTxnId) {
            if (!$txnId || !$orderId || !$paymentId) {
                Mage::throwException(
                    Mage::helper('sales')->__('Not enough valid data to save the parent transaction ID.')
                );
            }
            $parentId = (int)$this->_lookupByTxnId($orderId, $paymentId, $parentTxnId, $idFieldName);
            if ($parentId) {
                $transaction->setData('parent_id', $parentId);
            }
        }

        // make sure unique key won't cause trouble
        if ($transaction->isFailsafe()) {
            $autoincrementId = (int)$this->_lookupByTxnId($orderId, $paymentId, $txnId, $idFieldName);
            if ($autoincrementId) {
                $transaction->setData($idFieldName, $autoincrementId)->isObjectNew(false);
            }
        }

        return parent::_beforeSave($transaction);
    }

    /**
     * Load cell/row by specified unique key parts
     *
     * @param int $orderId
     * @param int $paymentId
     * @param string $txnId
     * @param mixed (array|string|object) $columns
     * @param bool $isRow
     * @param string $txnType
     * @return array|string
     */
    private function _lookupByTxnId($orderId, $paymentId, $txnId, $columns, $isRow = false, $txnType = null)
    {
        $select = $this->_getLoadByUniqueKeySelect($orderId, $paymentId, $txnId, $columns);
        if ($txnType) {
            $select->where('txn_type = ?', $txnType);
        }
        if ($isRow) {
            return $this->_getWriteAdapter()->fetchRow($select);
        }
        return $this->_getWriteAdapter()->fetchOne($select);
    }

    /**
     * Get select object for loading transaction by the unique key of order_id, payment_id, txn_id
     *
     * @param int $orderId
     * @param int $paymentId
     * @param string $txnId
     * @param string|array|Zend_Db_Expr $columns
     * @return Varien_Db_Select
     */
    private function _getLoadByUniqueKeySelect($orderId, $paymentId, $txnId, $columns = '*')
    {
        return $this->_getWriteAdapter()->select()
            ->from($this->getMainTable(), $columns)
            ->where('order_id = ?', $orderId)
            ->where('payment_id = ?', $paymentId)
            ->where('txn_id = ?', $txnId);
    }
}
