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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Mysql4_Website extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/website', 'website_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'code',
            'title' => Mage::helper('core')->__('Website with the same code')
        ));
        return $this;
    }

    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Mysql4_Website
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if(!preg_match('/^[a-z]+[a-z0-9_]*$/', $object->getCode())) {
            Mage::throwException(Mage::helper('core')->__('Website code may only contain letters (a-z), numbers (0-9) or underscore(_), the first character must be a letter'));
        }

        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Mysql4_Website
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->getIsDefault()) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('is_default' => 0),
                1
            );
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('is_default' => 1),
                $this->_getWriteAdapter()->quoteInto('website_id=?', $object->getId())
            );
        }
        return parent::_afterSave($object);
    }

    protected function _afterDelete(Mage_Core_Model_Abstract $model)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('core/config_data'),
            $this->_getWriteAdapter()->quoteInto("scope = 'websites' AND scope_id = ?", $model->getWebsiteId())
        );
        return $this;
    }

    /**
     * Retrieve default stores select object
     * Select fields website_id, store_id
     *
     * @param $withDefault include/exclude default admin website
     * @return Varien_Db_Select
     */
    public function getDefaultStoresSelect($withDefault = false) {
        $select = $this->_getReadAdapter()->select()
            ->from(array('website_table' => $this->getTable('core/website')), array('website_id'))
            ->joinLeft(
                array('store_group_table' => $this->getTable('core/store_group')),
                '`website_table`.`website_id`=`store_group_table`.`website_id`'
                    . ' AND `website_table`.`default_group_id`=`store_group_table`.`group_id`',
                array('store_id' => 'IFNULL(`store_group_table`.`default_store_id`, 0)')
            );
        if (!$withDefault) {
            $select->where('`website_table`.`website_id` <> ?', 0);
        }
        return $select;
    }

}
