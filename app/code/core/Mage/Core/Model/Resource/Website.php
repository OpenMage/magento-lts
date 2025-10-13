<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core Website Resource Model
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Website extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('core/website', 'website_id');
    }

    /**
     * Initialize unique fields
     *
     * @return $this
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = [[
            'field' => 'code',
            'title' => Mage::helper('core')->__('Website with the same code'),
        ]];
        return $this;
    }

    /**
     * Validate website code before object save
     *
     * @param Mage_Core_Model_Website $object
     * @inheritDoc
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!preg_match('/^[a-z]+[a-z0-9_]*$/', $object->getCode())) {
            Mage::throwException(Mage::helper('core')->__('Website code may only contain letters (a-z), numbers (0-9) or underscore(_), the first character must be a letter'));
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param Mage_Core_Model_Website $object
     * @inheritDoc
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->getIsDefault()) {
            $this->_getWriteAdapter()->update($this->getMainTable(), ['is_default' => 0]);
            $where = ['website_id = ?' => $object->getId()];
            $this->_getWriteAdapter()->update($this->getMainTable(), ['is_default' => 1], $where);
        }

        return parent::_afterSave($object);
    }

    /**
     * Remove core configuration data after delete website
     *
     * @param Mage_Core_Model_Website $model
     * @inheritDoc
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $model)
    {
        $where = [
            'scope = ?'    => 'websites',
            'scope_id = ?' => $model->getWebsiteId(),
        ];

        $this->_getWriteAdapter()->delete($this->getTable('core/config_data'), $where);

        return $this;
    }

    /**
     * Retrieve default stores select object
     * Select fields website_id, store_id
     *
     * @param bool $withDefault include/exclude default admin website
     * @return Varien_Db_Select
     */
    public function getDefaultStoresSelect($withDefault = false)
    {
        $ifNull  = $this->_getReadAdapter()
            ->getCheckSql('store_group_table.default_store_id IS NULL', '0', 'store_group_table.default_store_id');
        $select = $this->_getReadAdapter()->select()
            ->from(
                ['website_table' => $this->getTable('core/website')],
                ['website_id'],
            )
            ->joinLeft(
                ['store_group_table' => $this->getTable('core/store_group')],
                'website_table.website_id=store_group_table.website_id'
                    . ' AND website_table.default_group_id = store_group_table.group_id',
                ['store_id' => $ifNull],
            );
        if (!$withDefault) {
            $select->where('website_table.website_id <> ?', 0);
        }

        return $select;
    }
}
