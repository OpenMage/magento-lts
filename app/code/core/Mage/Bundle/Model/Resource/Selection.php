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
 * @package     Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Bundle Selection Resource Model
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Resource_Selection extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table and id field
     *
     */
    protected function _construct()
    {
        $this->_init('bundle/selection', 'selection_id');
    }

    /**
     * Retrieve Price From index
     *
     * @param int $productId
     * @param float $qty
     * @param int $storeId
     * @param int $groupId
     * @return mixed
     */
    public function getPriceFromIndex($productId, $qty, $storeId, $groupId)
    {
        $adapter = $this->_getReadAdapter();
        $select = clone $adapter->select();
        $select->reset();

        $attrPriceId = Mage::getSingleton('eav/entity_attribute')
            ->getIdByCode(Mage_Catalog_Model_Product::ENTITY, 'price');
        $attrTierPriceId = Mage::getSingleton('eav/entity_attribute')
            ->getIdByCode(Mage_Catalog_Model_Product::ENTITY, 'tier_price');

        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();

        $select->from(array("price_index" => $this->getTable('catalogindex/price')), array('price' => 'SUM(value)'))
            ->where('entity_id = :product_id')
            ->where('website_id = :website_id')
            ->where('customer_group_id = :customer_group')
            ->where('attribute_id = :price_attribute OR attribute_id = :tier_price_attribute')
            ->where('qty <= :qty')
            ->group('entity_id');

        $bind = array(
            'product_id' => $productId,
            'website_id' => $websiteId,
            'customer_group' => $groupId,
            'price_attribute' => $attrPriceId,
            'tier_price_attribute' => $attrTierPriceId,
            'qty'   => $qty
        );

        $price = $adapter->fetchCol($select, $bind);
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
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(
                array('tbl_selection' => $this->getMainTable()),
                array('product_id', 'parent_product_id', 'option_id')
            )
            ->join(
                array('e' => $this->getTable('catalog/product')),
                'e.entity_id = tbl_selection.product_id AND e.required_options=0',
                array()
            )
            ->join(
                array('tbl_option' => $this->getTable('bundle/option')),
                'tbl_option.option_id = tbl_selection.option_id',
                array('required')
            )
            ->where('tbl_selection.parent_product_id = :parent_id');
        foreach ($adapter->fetchAll($select, array('parent_id' => $parentId)) as $row) {
            if ($row['required']) {
                $childrenIds[$row['option_id']][$row['product_id']] = $row['product_id'];
            } else {
                $notRequired[$row['option_id']][$row['product_id']] = $row['product_id'];
            }
        }

        if (!$required) {
            $childrenIds = array_merge($childrenIds, $notRequired);
        } else {
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

    /**
     * Save bundle item price per website
     *
     * @param Mage_Bundle_Model_Selection $item
     */
    public function saveSelectionPrice($item)
    {
        $write = $this->_getWriteAdapter();
        if ($item->getDefaultPriceScope()) {
            $write->delete($this->getTable('bundle/selection_price'),
                array(
                    'selection_id = ?' => $item->getSelectionId(),
                    'website_id = ?'   => $item->getWebsiteId()
                )
            );
        } else {
             $values = array(
                'selection_id' => $item->getSelectionId(),
                'website_id'   => $item->getWebsiteId(),
                'selection_price_type' => $item->getSelectionPriceType(),
                'selection_price_value' => $item->getSelectionPriceValue()
            );
            $write->insertOnDuplicate(
                $this->getTable('bundle/selection_price'),
                $values,
                array('selection_price_type', 'selection_price_value')
            );
        }
    }
}
