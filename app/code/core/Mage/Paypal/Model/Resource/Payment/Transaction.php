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
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal transaction resource model
 *
 * @deprecated since 1.6.2.0
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Resource_Payment_Transaction extends Mage_Core_Model_Resource_Db_Abstract
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
     * @param Mage_Paypal_Model_Payment_Transaction $transaction
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
     * @throws Mage_Core_Exception
     *
     * @param Mage_Core_Model_Abstract $transaction
     * @return Mage_Paypal_Model_Resource_Payment_Transaction
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $transaction)
    {
        $txnId       = $transaction->getData('txn_id');
        $idFieldName = $this->getIdFieldName();

        // make sure unique key won't cause trouble
        if ($transaction->isFailsafe()) {
            $autoincrementId = (int)$this->_lookupByTxnId($txnId, $idFieldName);
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
     * @param mixed (array|string|object) $columns
     * @param bool $isRow
     * @return mixed (array|string)
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
