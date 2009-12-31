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
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * CatalogInventory Stock Status Indexer Resource Model
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Mysql4_Indexer_Stock
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Abstract
{
    /**
     * Stock Indexer models per product type
     * Sorted by priority
     *
     * @var array
     */
    protected $_indexers;

    /**
     * Default Stock Indexer resource model name
     *
     * @var string
     */
    protected $_defaultIndexer  = 'cataloginventory/indexer_stock_default';

    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('cataloginventory/stock_status', 'product_id');
    }

    /**
     * Process stock item save action
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_CatalogInventory_Model_Mysql4_Indexer_Stock
     */
    public function cataloginventoryStockItemSave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['product_id'])) {
            return $this;
        }
        $write = $this->_getWriteAdapter();
        $productId = $data['product_id'];
        $this->cloneIndexTable(true);

        $parentIds = $this->getRelationsByChild($productId);
        if ($parentIds) {
            $processIds = array_merge($parentIds, array($productId));
        } else {
            $processIds = array($productId);
        }
        $this->_copyRelationIndexData($processIds, array($productId));

        // retrieve product types by processIds
        $select = $write->select()
            ->from($this->getTable('catalog/product'), array('entity_id', 'type_id'))
            ->where('entity_id IN(?)', $processIds);
        $pairs  = $write->fetchPairs($select);

        $byType = array();
        foreach ($pairs as $productId => $typeId) {
            $byType[$typeId][$productId] = $productId;
        }

        $indexers = $this->_getTypeIndexers();
        foreach ($indexers as $indexer) {
            if (isset($byType[$indexer->getTypeId()])) {
                $indexer->reindexEntity($byType[$indexer->getTypeId()]);
            }
        }

        $this->_copyIndexDataToMainTable($processIds);
        return $this;
    }

    public function catalogProductDelete(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_stock_parent_ids'])) {
            return $this;
        }

        $this->cloneIndexTable(true);

        $processIds = array_keys($data['reindex_stock_parent_ids']);
        $parentIds  = array();
        foreach ($data['reindex_stock_parent_ids'] as $parentId => $parentType) {
            $parentIds[$parentType][$parentId] = $parentId;
        }

        $this->_copyRelationIndexData($processIds);
        foreach ($parentIds as $parentType => $entityIds) {
            $this->_getIndexer($parentType)->reindexEntity($entityIds);
        }

        $this->_copyIndexDataToMainTable($parentIds);

        return $this;
    }

    /**
     * Process product mass update action
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price
     */
    public function catalogProductMassAction(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_stock_product_ids'])) {
            return $this;
        }

        $processIds = $data['reindex_stock_product_ids'];

        $write  = $this->_getWriteAdapter();
        $select = $write->select()
            ->from($this->getTable('catalog/product'), 'COUNT(*)');
        $pCount = $write->fetchOne($select);

        // if affected more 30% of all products - run reindex all products
        if ($pCount * 0.3 < count($processIds)) {
            return $this->reindexAll();
        }

        // calculate relations
        $select = $write->select()
            ->from($this->getTable('catalog/product_relation'), 'COUNT(DISTINCT parent_id)')
            ->where('child_id IN(?)', $processIds);
        $aCount = $write->fetchOne($select);
        $select = $write->select()
            ->from($this->getTable('catalog/product_relation'), 'COUNT(DISTINCT child_id)')
            ->where('parent_id IN(?)', $processIds);
        $bCount = $write->fetchOne($select);

        // if affected with relations more 30% of all products - run reindex all products
        if ($pCount * 0.3 < count($processIds) + $aCount + $bCount) {
            return $this->reindexAll();
        }

        $this->cloneIndexTable(true);

        // retrieve products types
        $select = $write->select()
            ->from($this->getTable('catalog/product'), array('entity_id', 'type_id'))
            ->where('entity_id IN(?)', $processIds);
        $pairs  = $write->fetchPairs($select);
        $byType = array();
        foreach ($pairs as $productId => $productType) {
            $byType[$productType][$productId] = $productId;
        }

        $compositeIds    = array();
        $notCompositeIds = array();

        foreach ($byType as $productType => $entityIds) {
            $indexer = $this->_getIndexer($productType);
            if ($indexer->getIsComposite()) {
                $compositeIds += $entityIds;
            } else {
                $notCompositeIds += $entityIds;
            }
        }

        if (!empty($notCompositeIds)) {
            $select = $write->select()
                ->from(
                    array('l' => $this->getTable('catalog/product_relation')),
                    'parent_id')
                ->join(
                    array('e' => $this->getTable('catalog/product')),
                    'e.entity_id = l.parent_id',
                    array('type_id'))
                ->where('l.child_id IN(?)', $notCompositeIds);
            $pairs  = $write->fetchPairs($select);
            foreach ($pairs as $productId => $productType) {
                if (!in_array($productId, $processIds)) {
                    $processIds[] = $productId;
                    $byType[$productType][$productId] = $productId;
                    $compositeIds[$productId] = $productId;
                }
            }
        }

        if (!empty($compositeIds)) {
            $this->_copyRelationIndexData($compositeIds, $notCompositeIds);
        }

        $indexers = $this->_getTypeIndexers();
        foreach ($indexers as $indexer) {
            if (!empty($byType[$indexer->getTypeId()])) {
                $indexer->reindexEntity($byType[$indexer->getTypeId()]);
            }
        }

        $this->_copyIndexDataToMainTable($processIds);

        return $this;
    }

    /**
     * Rebuild all index data
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    public function reindexAll()
    {
        $this->cloneIndexTable(true);

        foreach ($this->_getTypeIndexers() as $indexer) {
            $indexer->reindexAll();
        }

        $this->syncData();
        return $this;
    }

    /**
     * Retrieve Stock Indexer Models per Product Type
     *
     * @return array
     */
    protected function _getTypeIndexers()
    {
        if (is_null($this->_indexers)) {
            $this->_indexers = array();
            $types = Mage::getSingleton('catalog/product_type')->getTypesByPriority();
            foreach ($types as $typeId => $typeInfo) {
                if (isset($typeInfo['stock_indexer'])) {
                    $modelName = $typeInfo['stock_indexer'];
                } else {
                    $modelName = $this->_defaultIndexer;
                }
                $isComposite = !empty($typeInfo['composite']);
                $indexer = Mage::getResourceModel($modelName)
                    ->setTypeId($typeId)
                    ->setIsComposite($isComposite);

                $this->_indexers[$typeId] = $indexer;
            }
        }
        return $this->_indexers;
    }

    /**
     * Retrieve Stock indexer by Product Type
     *
     * @param string $productTypeId
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Interface
     */
    protected function _getIndexer($productTypeId)
    {
        $types = $this->_getTypeIndexers();
        if (!isset($types[$productTypeId])) {
            Mage::throwException(Mage::helper('catalog')->__('Unsupported product type "%s"', $productTypeId));
        }
        return $types[$productTypeId];
    }

    /**
     * Retrieve parent ids and types by child id
     *
     * Return array with key product_id and value as product type id
     *
     * @param int $childId
     * @return array
     */
    public function getProductParentsByChild($childId)
    {
        $write = $this->_getWriteAdapter();
        $select = $write->select()
            ->from(array('l' => $this->getTable('catalog/product_relation')), array('parent_id'))
            ->join(
                array('e' => $this->getTable('catalog/product')),
                'l.parent_id=e.entity_id',
                array('e.type_id'))
            ->where('l.child_id=?', $childId);
        return $write->fetchPairs($select);
    }

    /**
     * Copy relations product index from primary index to temporary index table by parent entity
     *
     * @param array|int $parentIds
     * @package array|int $excludeIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price
     */
    protected function _copyRelationIndexData($parentIds, $excludeIds = null)
    {
        $write  = $this->_getWriteAdapter();
        $select = $write->select()
            ->from($this->getTable('catalog/product_relation'), array('child_id'))
            ->where('parent_id IN(?)', $parentIds);
        if (!is_null($excludeIds)) {
            $select->where('child_id NOT IN(?)', $excludeIds);
        }

        $children = $write->fetchCol($select);

        if ($children) {
            $select = $write->select()
                ->from($this->getMainTable())
                ->where('product_id IN(?)', $children);
            $query  = $select->insertFromSelect($this->getIdxTable());
            $write->query($query);
        }

        return $this;
    }

    /**
     * Copy data from temporary index table to main table by defined ids
     *
     * @param array $processIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price
     */
    protected function _copyIndexDataToMainTable($processIds)
    {
        $write = $this->_getWriteAdapter();
        $write->beginTransaction();
        try {
            // remove old index
            $where = $write->quoteInto('product_id IN(?)', $processIds);
            $write->delete($this->getMainTable(), $where);

            // remove additional data from index
            $where = $write->quoteInto('product_id NOT IN(?)', $processIds);
            $write->delete($this->getIdxTable(), $where);

            // insert new index
            $this->insertFromTable($this->getIdxTable(), $this->getMainTable());

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }
}
