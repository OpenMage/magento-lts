<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Paypal transaction resource model
 *
 * @package    Mage_Paypal
 * @deprecated since 1.6.2.0
 */
class Mage_Paypal_Model_Resource_Payment_Transaction extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Serializeable field: additional_information
     *
     * @var array
     */
    protected $_serializableFields   = [
        'additional_information' => [null, []],
    ];

    /**
     * Initialize main table and the primary key field name
     *
     */
    protected function _construct()
    {
        $this->_init('paypal/payment_transaction', 'transaction_id');
    }

    /**
     * @see Mage_Core_Model_Resource_Abstract::_unserializeField()
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
     * Load the transaction object by specified txn_id
     *
     * @param string $txnId
     */
    public function loadObjectByTxnId(Mage_Paypal_Model_Payment_Transaction $transaction, $txnId)
    {
        $select = $this->_getLoadByUniqueKeySelect($txnId);
        $data   = $this->_getWriteAdapter()->fetchRow($select);
        $transaction->setData($data);
        $this->unserializeFields($transaction);
        $this->_afterLoad($transaction);
    }

    /**
     * Serialize additional information, if any
     *
     * @param Mage_Paypal_Model_Payment_Transaction $transaction
     * @return $this
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $transaction)
    {
        $txnId       = $transaction->getData('txn_id');
        $idFieldName = $this->getIdFieldName();

        // make sure unique key won't cause trouble
        if ($transaction->isFailsafe()) {
            $autoincrementId = (int) $this->_lookupByTxnId($txnId, $idFieldName);
            if ($autoincrementId) {
                $transaction->setData($idFieldName, $autoincrementId)->isObjectNew(false);
            }
        }

        return parent::_beforeSave($transaction);
    }

    /**
     * Load cell/row by specified unique key parts
     *
     * @param string $txnId
     * @param array|string|object $columns
     * @param bool $isRow
     * @return array|string
     */
    private function _lookupByTxnId($txnId, $columns, $isRow = false)
    {
        $select = $this->_getLoadByUniqueKeySelect($txnId, $columns);
        if ($isRow) {
            return $this->_getWriteAdapter()->fetchRow($select);
        }

        return $this->_getWriteAdapter()->fetchOne($select);
    }

    /**
     * Get select object for loading transaction by the unique key of order_id, payment_id, txn_id
     *
     * @param string $txnId
     * @param string|array|Zend_Db_Expr $columns
     * @return Varien_Db_Select
     */
    private function _getLoadByUniqueKeySelect($txnId, $columns = '*')
    {
        return $this->_getWriteAdapter()->select()
            ->from($this->getMainTable(), $columns)
            ->where('txn_id = ?', $txnId);
    }
}
