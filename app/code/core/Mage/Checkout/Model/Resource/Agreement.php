<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Resource Model for Checkout Agreement
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Model_Resource_Agreement extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('checkout/agreement', 'agreement_id');
    }

    /**
     * Method to run before save
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        // format height
        $height = $object->getContentHeight();
        $height = Mage::helper('checkout')->stripTags($height);
        if (!$height) {
            $height = '';
        }

        if ($height && preg_match('/\d$/', $height)) {
            $height .= 'px';
        }

        $object->setContentHeight($height);
        return parent::_beforeSave($object);
    }

    /**
     * Method to run after save
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $condition = ['agreement_id = ?' => $object->getId()];
        $this->_getWriteAdapter()->delete($this->getTable('checkout/agreement_store'), $condition);

        foreach ((array) $object->getData('stores') as $store) {
            $storeArray = [];
            $storeArray['agreement_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('checkout/agreement_store'), $storeArray);
        }

        return parent::_afterSave($object);
    }

    /**
     * Method to run after load
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('checkout/agreement_store'), ['store_id'])
            ->where('agreement_id = :agreement_id');

        if ($stores = $this->_getReadAdapter()->fetchCol($select, [':agreement_id' => $object->getId()])) {
            $object->setData('store_id', $stores);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Get load select
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract|Mage_Checkout_Model_Agreement $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $select->join(
                ['cps' => $this->getTable('checkout/agreement_store')],
                $this->getMainTable() . '.agreement_id = cps.agreement_id',
            )
            ->where('is_active=1')
            ->where('cps.store_id IN (0, ?)', $object->getStoreId())
            ->order('store_id DESC')
            ->limit(1);
        }

        return $select;
    }
}
