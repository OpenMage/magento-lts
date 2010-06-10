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
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle Option Resource Model
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Mysql4_Option extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize connection and define resource
     *
     */
    protected function _construct()
    {
        $this->_init('bundle/option', 'option_id');
    }

    /**
     * After save process
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Bundle_Model_Mysql4_Option
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);

        $condition = $this->_getWriteAdapter()->quoteInto('option_id = ?', $object->getId());
        $condition .= ' and (' . $this->_getWriteAdapter()->quoteInto('store_id = ?', $object->getStoreId());
        $condition .= ' or store_id = 0)';

        $this->_getWriteAdapter()->delete($this->getTable('option_value'), $condition);

        $data = new Varien_Object();
        $data->setOptionId($object->getId())
            ->setStoreId($object->getStoreId())
            ->setTitle($object->getTitle());

        $this->_getWriteAdapter()->insert($this->getTable('option_value'), $data->getData());

        /**
         * also saving default value if this store view scope
         */

        if ($object->getStoreId()) {
            $data->setStoreId('0');
            $data->setTitle($object->getDefaultTitle());
            $this->_getWriteAdapter()->insert($this->getTable('option_value'), $data->getData());
        }

        return $this;
    }

    /**
     * After delete process
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Bundle_Model_Mysql4_Option
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_afterDelete($object);

        $condition = $this->_getWriteAdapter()->quoteInto('option_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('option_value'), $condition);

        return $this;
    }

/**
     * Retrieve options searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('option' => $this->getMainTable()), null)
            ->join(
                array('option_title_default' => $this->getTable('bundle/option_value')),
                'option_title_default.option_id=option.option_id AND option_title_default.store_id=0',
                array())
            ->joinLeft(
                array('option_title_store' => $this->getTable('bundle/option_value')),
                'option_title_store.option_id=option.option_id AND option_title_store.store_id=' . intval($storeId),
                array('title' => 'IFNULL(option_title_store.title, option_title_default.title)'))
            ->where('option.parent_id=?', $productId);
        if (!$searchData = $this->_getReadAdapter()->fetchCol($select)) {
            $searchData = array();
        }
        return $searchData;
    }
}
