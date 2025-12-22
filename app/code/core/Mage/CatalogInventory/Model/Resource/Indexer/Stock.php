<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * CatalogInventory Stock Status Indexer Resource Model
 *
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Model_Resource_Indexer_Stock extends Mage_Catalog_Model_Resource_Product_Indexer_Abstract
{
    /**
     * Stock Indexer models per product type
     * Sorted by priority
     *
     * @var null|array
     */
    protected $_indexers;

    /**
     * Default Stock Indexer resource model name
     *
     * @var string
     */
    protected $_defaultIndexer   = 'cataloginventory/indexer_stock_default';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('cataloginventory/stock_status', 'product_id');
    }

    /**
     * Process stock item save action
     *
     * @return $this
     * @throws Exception
     */
    public function cataloginventoryStockItemSave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['product_id'])) {
            return $this;
        }

        $productId = $data['product_id'];
        $this->reindexProducts($productId);

        return $this;
    }

    /**
     * Refresh stock index for specific product ids
     *
     * @param  array     $productIds
     * @return $this
     * @throws Exception
     */
    public function reindexProducts($productIds)
    {
        $adapter = $this->_getWriteAdapter();
        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }

        $parentIds = $this->getRelationsByChild($productIds);
        if ($parentIds) {
            $processIds = array_merge($parentIds, $productIds);
        } else {
            $processIds = $productIds;
        }

        // retrieve product types by processIds
        $select = $adapter->select()
            ->from($this->getTable('catalog/product'), ['entity_id', 'type_id'])
            ->where('entity_id IN(?)', $processIds);
        $pairs  = $adapter->fetchPairs($select);

        $byType = [];
        foreach ($pairs as $productId => $typeId) {
            $byType[$typeId][$productId] = $productId;
        }

        $adapter->beginTransaction();
        try {
            $indexers = $this->_getTypeIndexers();
            foreach ($indexers as $indexer) {
                if (isset($byType[$indexer->getTypeId()])) {
                    $indexer->reindexEntity($byType[$indexer->getTypeId()]);
                }
            }

            $adapter->commit();
        } catch (Exception $exception) {
            $adapter->rollBack();
            throw $exception;
        }

        return $this;
    }

    /**
     * Processing parent products after child product deleted
     *
     * @return $this
     * @throws Exception
     */
    public function catalogProductDelete(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_stock_parent_ids'])) {
            return $this;
        }

        $adapter = $this->_getWriteAdapter();

        $parentIds  = [];
        foreach ($data['reindex_stock_parent_ids'] as $parentId => $parentType) {
            $parentIds[$parentType][$parentId] = $parentId;
        }

        $adapter->beginTransaction();
        try {
            foreach ($parentIds as $parentType => $entityIds) {
                $this->_getIndexer($parentType)->reindexEntity($entityIds);
            }

            $adapter->commit();
        } catch (Exception $exception) {
            $adapter->rollBack();
            throw $exception;
        }

        return $this;
    }

    /**
     * Process product mass update action
     *
     * @return $this
     * @throws Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function catalogProductMassAction(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_stock_product_ids'])) {
            return $this;
        }

        $adapter = $this->_getWriteAdapter();
        $processIds = $data['reindex_stock_product_ids'];
        $select = $adapter->select()
            ->from($this->getTable('catalog/product'), 'COUNT(*)');
        $pCount = $adapter->fetchOne($select);

        // if affected more 30% of all products - run reindex all products
        if ($pCount * 0.3 < count($processIds)) {
            return $this->reindexAll();
        }

        // calculate relations
        $select = $adapter->select()
            ->from($this->getTable('catalog/product_relation'), 'COUNT(DISTINCT parent_id)')
            ->where('child_id IN(?)', $processIds);
        $aCount = $adapter->fetchOne($select);
        $select = $adapter->select()
            ->from($this->getTable('catalog/product_relation'), 'COUNT(DISTINCT child_id)')
            ->where('parent_id IN(?)', $processIds);
        $bCount = $adapter->fetchOne($select);

        // if affected with relations more 30% of all products - run reindex all products
        if ($pCount * 0.3 < count($processIds) + $aCount + $bCount) {
            return $this->reindexAll();
        }

        // retrieve affected parent relation products
        $parentIds = $this->getRelationsByChild($processIds);
        if ($parentIds) {
            $processIds = array_merge($processIds, $parentIds);
        }

        // retrieve products types
        $select = $adapter->select()
            ->from($this->getTable('catalog/product'), ['entity_id', 'type_id'])
            ->where('entity_id IN(?)', $processIds);
        $query  = $select->query(Zend_Db::FETCH_ASSOC);
        $byType = [];
        while ($row = $query->fetch()) {
            $byType[$row['type_id']][] = $row['entity_id'];
        }

        $adapter->beginTransaction();
        try {
            $indexers = $this->_getTypeIndexers();
            foreach ($indexers as $indexer) {
                if (!empty($byType[$indexer->getTypeId()])) {
                    $indexer->reindexEntity($byType[$indexer->getTypeId()]);
                }
            }

            $adapter->commit();
        } catch (Exception $exception) {
            $adapter->rollBack();
            throw $exception;
        }

        return $this;
    }

    /**
     * Rebuild all index data
     *
     * @return $this
     * @throws Exception
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            $this->clearTemporaryIndexTable();

            foreach ($this->_getTypeIndexers() as $indexer) {
                $indexer->reindexAll();
            }

            $this->syncData();
            $this->commit();
        } catch (Exception $exception) {
            $this->rollBack();
            throw $exception;
        }

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
            $this->_indexers = [];
            $types = Mage::getSingleton('catalog/product_type')->getTypesByPriority();
            foreach ($types as $typeId => $typeInfo) {
                $modelName = $typeInfo['stock_indexer'] ?? $this->_defaultIndexer;
                $isComposite = !empty($typeInfo['composite']);
                /** @var Mage_CatalogInventory_Model_Resource_Indexer_Stock_Default $indexer */
                $indexer = Mage::getResourceModel($modelName);
                $indexer
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
     * @param  string                                                       $productTypeId
     * @return Mage_CatalogInventory_Model_Resource_Indexer_Stock_Interface
     * @throws Mage_Core_Exception
     */
    protected function _getIndexer($productTypeId)
    {
        $types = $this->_getTypeIndexers();
        if (!isset($types[$productTypeId])) {
            Mage::throwException(Mage::helper('catalog')->__('Unsupported product type "%s".', $productTypeId));
        }

        return $types[$productTypeId];
    }

    /**
     * Retrieve parent ids and types by child id
     * Return array with key product_id and value as product type id
     *
     * @param  int   $childId
     * @return array
     */
    public function getProductParentsByChild($childId)
    {
        $write = $this->_getWriteAdapter();
        $select = $write->select()
            ->from(['l' => $this->getTable('catalog/product_relation')], ['parent_id'])
            ->join(
                ['e' => $this->getTable('catalog/product')],
                'l.parent_id=e.entity_id',
                ['e.type_id'],
            )
            ->where('l.child_id = :child_id');
        return $write->fetchPairs($select, [':child_id' => $childId]);
    }

    /**
     * Retrieve temporary index table name
     *
     * @param  string $table
     * @return string
     */
    public function getIdxTable($table = null)
    {
        if ($this->useIdxTable()) {
            return $this->getTable('cataloginventory/stock_status_indexer_idx');
        }

        return $this->getTable('cataloginventory/stock_status_indexer_tmp');
    }
}
