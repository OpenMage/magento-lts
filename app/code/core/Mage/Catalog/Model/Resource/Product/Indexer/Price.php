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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Product Price Indexer Resource Model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product_Indexer_Price extends Mage_Index_Model_Resource_Abstract
{
    /**
     * Default Product Type Price indexer resource model
     *
     * @var string
     */
    protected $_defaultPriceIndexer    = 'catalog/product_indexer_price_default';

    /**
     * Product Type Price indexer resource models
     *
     * @var array
     */
    protected $_indexers;

    /**
     * Define main index table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_index_price', 'entity_id');
    }

    /**
     * Retrieve parent ids and types by child id
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
                'l.parent_id = e.entity_id',
                array('e.type_id'))
            ->where('l.child_id = ?', $childId);

        return $write->fetchPairs($select);
    }

    /**
     * Process produce delete
     * If the deleted product was found in a composite product(s) update it
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Price
     */
    public function catalogProductDelete(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_price_parent_ids'])) {
            return $this;
        }

        $this->clearTemporaryIndexTable();

        $processIds = array_keys($data['reindex_price_parent_ids']);
        $parentIds  = array();
        foreach ($data['reindex_price_parent_ids'] as $parentId => $parentType) {
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
     * Copy data from temporary index table to main table by defined ids
     *
     * @param array $processIds
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Price
     * @throws Exception
     */
    protected function _copyIndexDataToMainTable($processIds)
    {
        $write = $this->_getWriteAdapter();
        $this->beginTransaction();
        try {
            // remove old index
            $where = $write->quoteInto('entity_id IN(?)', $processIds);
            $write->delete($this->getMainTable(), $where);

            // remove additional data from index
            $where = $write->quoteInto('entity_id NOT IN(?)', $processIds);
            $write->delete($this->getIdxTable(), $where);

            // insert new index
            $this->useDisableKeys(false);
            $this->insertFromTable($this->getIdxTable(), $this->getMainTable());
            $this->useDisableKeys(true);

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Process product save.
     * Method is responsible for index support
     * when product was saved and changed attribute(s) has an effect on price.
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Price
     */
    public function catalogProductSave(Mage_Index_Model_Event $event)
    {
        $productId = $event->getEntityPk();
        $data = $event->getNewData();

        /**
         * Check if price attribute values were updated
         */
        if (!isset($data['reindex_price'])) {
            return $this;
        }

        $this->clearTemporaryIndexTable();
        $this->_prepareWebsiteDateTable();

        $indexer = $this->_getIndexer($data['product_type_id']);
        $processIds = array($productId);
        if ($indexer->getIsComposite()) {
            $this->_copyRelationIndexData($productId);
            $this->_prepareTierPriceIndex($productId);
            $this->_prepareGroupPriceIndex($productId);
            $indexer->reindexEntity($productId);
        } else {
            $parentIds = $this->getProductParentsByChild($productId);

            if ($parentIds) {
                $processIds = array_merge($processIds, array_keys($parentIds));
                $this->_copyRelationIndexData(array_keys($parentIds), $productId);
                $this->_prepareTierPriceIndex($processIds);
                $this->_prepareGroupPriceIndex($processIds);
                $indexer->reindexEntity($productId);

                $parentByType = array();
                foreach ($parentIds as $parentId => $parentType) {
                    $parentByType[$parentType][$parentId] = $parentId;
                }

                foreach ($parentByType as $parentType => $entityIds) {
                    $this->_getIndexer($parentType)->reindexEntity($entityIds);
                }
            } else {
                $this->_prepareTierPriceIndex($productId);
                $this->_prepareGroupPriceIndex($productId);
                $indexer->reindexEntity($productId);
            }
        }

        $this->_copyIndexDataToMainTable($processIds);

        return $this;
    }

    /**
     * Process product mass update action
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Price
     */
    public function catalogProductMassAction(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_price_product_ids'])) {
            return $this;
        }

        $processIds = $data['reindex_price_product_ids'];

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
        $this->reindexProductIds($processIds);
        return $this;
    }

    /**
     * Reindex product prices for specified product ids
     *
     * @param array | int $ids
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Price
     */
    public function reindexProductIds($ids)
    {
        if (empty($ids)) {
            return $this;
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $this->clearTemporaryIndexTable();
        $write  = $this->_getWriteAdapter();
        // retrieve products types
        $select = $write->select()
            ->from($this->getTable('catalog/product'), array('entity_id', 'type_id'))
            ->where('entity_id IN(?)', $ids);
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
                if (!in_array($productId, $ids)) {
                    $ids[] = $productId;
                    $byType[$productType][$productId] = $productId;
                    $compositeIds[$productId] = $productId;
                }
            }
        }

        if (!empty($compositeIds)) {
            $this->_copyRelationIndexData($compositeIds, $notCompositeIds);
        }

        $indexers = $this->getTypeIndexers();
        foreach ($indexers as $indexer) {
            if (!empty($byType[$indexer->getTypeId()])) {
                $indexer->reindexEntity($byType[$indexer->getTypeId()]);
            }
        }

        $this->_copyIndexDataToMainTable($ids);
        return $this;
    }

    /**
     * Retrieve Price indexer by Product Type
     *
     * @param string $productTypeId
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Price_Interface
     * @throws Mage_Core_Exception
     */
    protected function _getIndexer($productTypeId)
    {
        $types = $this->getTypeIndexers();
        if (!isset($types[$productTypeId])) {
            Mage::throwException(Mage::helper('catalog')->__('Unsupported product type "%s".', $productTypeId));
        }
        return $types[$productTypeId];
    }

    /**
     * Retrieve price indexers per product type
     *
     * @return array
     */
    public function getTypeIndexers()
    {
        if (is_null($this->_indexers)) {
            $this->_indexers = array();
            $types = Mage::getSingleton('catalog/product_type')->getTypesByPriority();
            foreach ($types as $typeId => $typeInfo) {
                if (isset($typeInfo['price_indexer'])) {
                    $modelName = $typeInfo['price_indexer'];
                } else {
                    $modelName = $this->_defaultPriceIndexer;
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
     * Rebuild all index data
     *
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Price
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            $this->clearTemporaryIndexTable();
            $this->_prepareWebsiteDateTable();
            $this->_prepareTierPriceIndex();
            $this->_prepareGroupPriceIndex();

            $indexers = $this->getTypeIndexers();
            foreach ($indexers as $indexer) {
                /** @var $indexer Mage_Catalog_Model_Resource_Product_Indexer_Price_Interface */
                $indexer->reindexAll();
            }

            $this->syncData();
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Retrieve table name for product tier price index
     *
     * @return string
     */
    protected function _getTierPriceIndexTable()
    {
        return $this->getTable('catalog/product_index_tier_price');
    }

    /**
     * Retrieve table name for product group price index
     *
     * @return string
     */
    protected function _getGroupPriceIndexTable()
    {
        return $this->getTable('catalog/product_index_group_price');
    }

    /**
     * Prepare tier price index table
     *
     * @param int|array $entityIds the entity ids limitation
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Price
     */
    protected function _prepareTierPriceIndex($entityIds = null)
    {
        $write = $this->_getWriteAdapter();
        $table = $this->_getTierPriceIndexTable();
        $write->delete($table);

        $websiteExpression = $write->getCheckSql('tp.website_id = 0', 'ROUND(tp.value * cwd.rate, 4)', 'tp.value');
        $select = $write->select()
            ->from(
                array('tp' => $this->getValueTable('catalog/product', 'tier_price')),
                array('entity_id'))
            ->join(
                array('cg' => $this->getTable('customer/customer_group')),
                'tp.all_groups = 1 OR (tp.all_groups = 0 AND tp.customer_group_id = cg.customer_group_id)',
                array('customer_group_id'))
            ->join(
                array('cw' => $this->getTable('core/website')),
                'tp.website_id = 0 OR tp.website_id = cw.website_id',
                array('website_id'))
            ->join(
                array('cwd' => $this->_getWebsiteDateTable()),
                'cw.website_id = cwd.website_id',
                array())
            ->where('cw.website_id != 0')
            ->columns(new Zend_Db_Expr("MIN({$websiteExpression})"))
            ->group(array('tp.entity_id', 'cg.customer_group_id', 'cw.website_id'));

        if (!empty($entityIds)) {
            $select->where('tp.entity_id IN(?)', $entityIds);
        }

        $query = $select->insertFromSelect($table);
        $write->query($query);

        return $this;
    }

    /**
     * Prepare group price index table
     *
     * @param int|array $entityIds the entity ids limitation
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Price
     */
    protected function _prepareGroupPriceIndex($entityIds = null)
    {
        $write = $this->_getWriteAdapter();
        $table = $this->_getGroupPriceIndexTable();
        $write->delete($table);

        $websiteExpression = $write->getCheckSql('gp.website_id = 0', 'ROUND(gp.value * cwd.rate, 4)', 'gp.value');
        $select = $write->select()
            ->from(
                array('gp' => $this->getValueTable('catalog/product', 'group_price')),
                array('entity_id'))
            ->join(
                array('cg' => $this->getTable('customer/customer_group')),
                'gp.all_groups = 1 OR (gp.all_groups = 0 AND gp.customer_group_id = cg.customer_group_id)',
                array('customer_group_id'))
            ->join(
                array('cw' => $this->getTable('core/website')),
                'gp.website_id = 0 OR gp.website_id = cw.website_id',
                array('website_id'))
            ->join(
                array('cwd' => $this->_getWebsiteDateTable()),
                'cw.website_id = cwd.website_id',
                array())
            ->where('cw.website_id != 0')
            ->columns(new Zend_Db_Expr("MIN({$websiteExpression})"))
            ->group(array('gp.entity_id', 'cg.customer_group_id', 'cw.website_id'));

        if (!empty($entityIds)) {
            $select->where('gp.entity_id IN(?)', $entityIds);
        }

        $query = $select->insertFromSelect($table);
        $write->query($query);

        return $this;
    }

    /**
     * Copy relations product index from primary index to temporary index table by parent entity
     *
     * @package array|int $excludeIds
     *
     * @param array|int $parentIds
     * @param unknown_type $excludeIds
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Price
     */
    protected function _copyRelationIndexData($parentIds, $excludeIds = null)
    {
        $write  = $this->_getWriteAdapter();
        $select = $write->select()
            ->from($this->getTable('catalog/product_relation'), array('child_id'))
            ->where('parent_id IN(?)', $parentIds);
        if (!empty($excludeIds)) {
            $select->where('child_id NOT IN(?)', $excludeIds);
        }

        $children = $write->fetchCol($select);

        if ($children) {
            $select = $write->select()
                ->from($this->getMainTable())
                ->where('entity_id IN(?)', $children);
            $query  = $select->insertFromSelect($this->getIdxTable(), array(), false);
            $write->query($query);
        }

        return $this;
    }

    /**
     * Retrieve website current dates table name
     *
     * @return string
     */
    protected function _getWebsiteDateTable()
    {
        return $this->getTable('catalog/product_index_website');
    }

    /**
     * Prepare website current dates table
     *
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Price
     */
    protected function _prepareWebsiteDateTable()
    {
        $write = $this->_getWriteAdapter();
        $baseCurrency = Mage::app()->getBaseCurrencyCode();

        $select = $write->select()
            ->from(
                array('cw' => $this->getTable('core/website')),
                array('website_id'))
            ->join(
                array('csg' => $this->getTable('core/store_group')),
                'cw.default_group_id = csg.group_id',
                array('store_id' => 'default_store_id'))
            ->where('cw.website_id != 0');


        $data = array();
        foreach ($write->fetchAll($select) as $item) {
            /** @var $website Mage_Core_Model_Website */
            $website = Mage::app()->getWebsite($item['website_id']);

            if ($website->getBaseCurrencyCode() != $baseCurrency) {
                $rate = Mage::getModel('directory/currency')
                    ->load($baseCurrency)
                    ->getRate($website->getBaseCurrencyCode());
                if (!$rate) {
                    $rate = 1;
                }
            } else {
                $rate = 1;
            }

            /** @var $store Mage_Core_Model_Store */
            $store = Mage::app()->getStore($item['store_id']);
            if ($store) {
                $timestamp = Mage::app()->getLocale()->storeTimeStamp($store);
                $data[] = array(
                    'website_id' => $website->getId(),
                    'website_date'       => $this->formatDate($timestamp, false),
                    'rate'       => $rate
                );
            }
        }

        $write->beginTransaction();
        $table = $this->_getWebsiteDateTable();
        $write->delete($table);

        if ($data) {
            $write->insertMultiple($table, $data);
        }
        $write->commit();

        return $this;
    }

    /**
     * Retrieve temporary index table name
     *
     * @param unknown_type $table
     * @return string
     */
    public function getIdxTable($table = null)
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/product_price_indexer_idx');
        }
        return $this->getTable('catalog/product_price_indexer_tmp');
    }
}
