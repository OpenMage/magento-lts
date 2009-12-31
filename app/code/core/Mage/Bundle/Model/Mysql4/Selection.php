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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle Selection Resource Model
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Mysql4_Selection extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('bundle/selection', 'selection_id');
    }
/*
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);

        $condition = $this->_getWriteAdapter()->quoteInto('option_id = ?', $object->getId());
        $condition .= ' and ' . $this->_getWriteAdapter()->quoteInto('store_id = ?', $object->getStoreId());

        $this->_getWriteAdapter()->delete($this->getTable('option_value'), $condition);

        $data = new Varien_Object();
        $data->setOptionId($object->getId())
            ->setStoreId($object->getStoreId())
            ->setTitle($object->getTitle());

        $this->_getWriteAdapter()->insert($this->getTable('option_value'), $data->getData());

        return $this;
    }

    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_afterDelete($object);

        $condition = $this->_getWriteAdapter()->quoteInto('option_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('option_value'), $condition);

        return $this;
    }
*/

    /**
     * Retrieve Price From index
     *
     * @param int $productId
     * @param float $qty
     * @param int $storeId
     * @param int $groupId
     * @return mixed
     */
    public function getPriceFromIndex($productId, $qty, $storeId, $groupId) {
        $select = clone $this->_getReadAdapter()->select();
        $select->reset();

        $attrPriceId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'price');
        $attrTierPriceId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'tier_price');

        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();

        $select->from(array("price_index" => $this->getTable('catalogindex/price')), array('price' => 'SUM(value)'))
            ->where('entity_id in (?)', $productId)
            ->where('website_id = ?', $websiteId)
            ->where('customer_group_id = ?', $groupId)
            ->where('attribute_id in (?)', array($attrPriceId, $attrTierPriceId))
            ->where('qty <= ?', $qty)
            ->group('entity_id');

        $price = $this->_getReadAdapter()->fetchCol($select);
        if (!empty($price)) {
            return array_shift($price);
        } else {
            return 0;
        }
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param int $parentId
     * @param bool $required
     * @return array
     */
    public function getChildrenIds($parentId, $required = true)
    {
        $childrenIds = array();
        $notRequired = array();
        $select = $this->_getReadAdapter()->select()
            ->from(
                array('tbl_selection' => $this->getMainTable()),
                array('product_id', 'parent_product_id', 'option_id'))
            ->join(
                array('e' => $this->getTable('catalog/product')),
                'e.entity_id=tbl_selection.product_id AND e.required_options=0',
                array()
            )
            ->join(
                array('tbl_option' => $this->getTable('bundle/option')),
                '`tbl_option`.`option_id` = `tbl_selection`.`option_id`',
                array('required')
            )
            ->where('`tbl_selection`.`parent_product_id`=?', $parentId);
        foreach ($this->_getReadAdapter()->fetchAll($select) as $row) {
            if ($row['required']) {
                $childrenIds[$row['option_id']][$row['product_id']] = $row['product_id'];
            }
            else {
                $notRequired[$row['option_id']][$row['product_id']] = $row['product_id'];
            }
        }

        if (!$required) {
            $childrenIds = array_merge($childrenIds, $notRequired);
        }
        else {
            if (!$childrenIds) {
                foreach ($notRequired as $groupedChildrenIds) {
                    foreach ($groupedChildrenIds as $childId) {
                        $childrenIds[0][$childId] = $childId;
                    }
                }
            }
            if (!$childrenIds) {
                $childrenIds = array(array());
            }
        }

        return $childrenIds;
    }

    /**
     * Retrieve array of related bundle product ids by selection product id(s)
     *
     * @param int|array $childId
     * @return array
     */
    public function getParentIdsByChild($childId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->distinct(true)
            ->from($this->getMainTable(), 'parent_product_id')
            ->where('product_id IN(?)', $childId);

        return $adapter->fetchCol($select);
    }
}
