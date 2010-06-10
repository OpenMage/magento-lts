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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product website resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Status extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Product atrribute cache
     *
     * @var array
     */
    protected $_productAttributes = array();

    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_enabled_index', 'product_id');
    }

    /**
     * Retrieve product attribute (public method for status model)
     *
     * @param string $attributeCode
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getProductAttribute($attributeCode)
    {
        return $this->_getProductAttribute($attributeCode);
    }

    /**
     * Retrieve product attribute
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected function _getProductAttribute($attribute)
    {
        if (empty($this->_productAttributes[$attribute])) {
            $this->_productAttributes[$attribute] = Mage::getSingleton('catalog/product')->getResource()->getAttribute($attribute);
        }
        return $this->_productAttributes[$attribute];
    }

    /**
     * Refresh enabled index cache
     *
     * @param int $productId
     * @param int $storeId
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Status
     */
    public function refreshEnabledIndex($productId, $storeId)
    {
        $statusAttributeId      = $this->_getProductAttribute('status')->getId();
        $visibilityAttributeId  = $this->_getProductAttribute('visibility')->getId();
        $statusTable            = $this->_getProductAttribute('status')->getBackend()->getTable();
        $visibilityTable        = $this->_getProductAttribute('visibility')->getBackend()->getTable();

        $indexTable = $this->getTable('catalog/product_enabled_index');

        if ($storeId == 0) {
            foreach (Mage::app()->getStores() as $store) {
                $this->refreshEnabledIndex($productId, $store->getId());
            }

            return $this;
        }

        $this->_getWriteAdapter()->delete($indexTable, array(
            $this->_getWriteAdapter()->quoteInto('product_id=?', $productId),
            $this->_getWriteAdapter()->quoteInto('store_id=?', $storeId)
        ));

        $query = "INSERT INTO $indexTable
            SELECT
                {$productId}, {$storeId}, IF(t_v.value_id>0, t_v.value, t_v_default.value)
            FROM
                {$visibilityTable} AS t_v_default
            LEFT JOIN {$visibilityTable} AS `t_v`
                ON (t_v.entity_id = t_v_default.entity_id) AND (t_v.attribute_id='{$visibilityAttributeId}') AND (t_v.store_id='{$storeId}')
            INNER JOIN {$statusTable} AS `t_s_default`
                ON (t_s_default.entity_id = t_v_default.entity_id) AND (t_s_default.attribute_id='{$statusAttributeId}') AND t_s_default.store_id=0
            LEFT JOIN {$statusTable} AS `t_s`
                ON (t_s.entity_id = t_v_default.entity_id) AND (t_s.attribute_id='{$statusAttributeId}') AND (t_s.store_id='{$storeId}')
            WHERE
                t_v_default.entity_id={$productId}
                AND t_v_default.attribute_id='{$visibilityAttributeId}' AND t_v_default.store_id=0
                AND (IF(t_s.value_id>0, t_s.value, t_s_default.value)=".Mage_Catalog_Model_Product_Status::STATUS_ENABLED.")";
        $this->_getWriteAdapter()->query($query);

        return $this;
    }

    /**
     * Update product status for store
     *
     * @param int $productId
     * @param int $storId
     * @param int $value
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Status
     */
    public function updateProductStatus($productId, $storId, $value)
    {
        $statusAttributeId  = $this->_getProductAttribute('status')->getId();
        $statusEntityTypeId = $this->_getProductAttribute('status')->getEntityTypeId();
        $statusTable        = $this->_getProductAttribute('status')->getBackend()->getTable();
        $refreshIndex       = true;

        $prop = array(
            'entity_type_id' => $statusEntityTypeId,
            'attribute_id'   => $statusAttributeId,
            'store_id'       => $storId,
            'entity_id'      => $productId,
            'value'          => $value
        );

        $select = $this->_getWriteAdapter()->select()
            ->from($statusTable)
            ->where('attribute_id=?', $statusAttributeId)
            ->where('store_id=?', $storId)
            ->where('entity_id=?', $productId);
        $row = $this->_getWriteAdapter()->fetchRow($select);

        if ($row) {
            if ($row['value'] == $value) {
                $refreshIndex = false;
            }
            else {
                $this->_getWriteAdapter()->update($statusTable, $prop, $this->_getWriteAdapter()->quoteInto('value_id=?', $row['value_id']));
            }
        }
        else {
            $this->_getWriteAdapter()->insert($statusTable, $prop);
        }

        if ($refreshIndex) {
            $this->refreshEnabledIndex($productId, $storId);
        }

        return $this;
    }

    /**
     * Retrieve Product(s) status for store
     * Return array where key is a product_id, value - status
     *
     * @param array|int $productIds
     * @param int $storeId
     * @return array
     */
    public function getProductStatus($productIds, $storeId = null)
    {
        $statuses = array();

        $attribute = $this->_getProductAttribute('status');
        $attributeTable = $attribute->getBackend()->getTable();

        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }

        if (is_null($storeId) || $storeId == 0) {
            $select = $this->_getReadAdapter()->select()
                ->from($attributeTable, array('entity_id', 'value'))
                ->where('entity_id IN(?)', $productIds)
                ->where('attribute_id=?', $attribute->getAttributeId())
                ->where('store_id=?', 0);
            $rows = $this->_getWriteAdapter()->fetchPairs($select);
        }
        else {
            $select = $this->_getReadAdapter()->select()
                ->from(
                    array('t1' => $attributeTable),
                    array('entity_id', 'IF(t2.value_id>0, t2.value, t1.value) as value'))
                ->joinLeft(
                    array('t2' => $attributeTable),
                    $this->_getReadAdapter()->quoteInto('t1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id=?', $storeId),
                    array()
                )
                ->where('t1.store_id = ?', 0)
                ->where('t1.attribute_id = ?', $attribute->getAttributeId())
                ->where('t1.entity_id IN(?)', $productIds);
            $rows = $this->_getWriteAdapter()->fetchPairs($select);
        }

        foreach ($productIds as $productId) {
            if (isset($rows[$productId])) {
                $statuses[$productId] = $rows[$productId];
            }
            else {
                $statuses[$productId] = -1;
            }
        }

        return $statuses;
    }
}
