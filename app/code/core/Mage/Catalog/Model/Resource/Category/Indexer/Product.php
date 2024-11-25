<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Resource model for category product indexer
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Category_Indexer_Product extends Mage_Index_Model_Resource_Abstract
{
    /**
     * Category table
     *
     * @var string
     */
    protected $_categoryTable;

    /**
     * Category product table
     *
     * @var string
     */
    protected $_categoryProductTable;

    /**
     * Product website table
     *
     * @var string
     */
    protected $_productWebsiteTable;

    /**
     * Store table
     *
     * @var string
     */
    protected $_storeTable;

    /**
     * Group table
     *
     * @var string
     */
    protected $_groupTable;

    /**
     * Array of info about stores
     *
     * @var array|null
     */
    protected $_storesInfo;

    protected function _construct()
    {
        $this->_init('catalog/category_product_index', 'category_id');
        $this->_categoryTable        = $this->getTable('catalog/category');
        $this->_categoryProductTable = $this->getTable('catalog/category_product');
        $this->_productWebsiteTable  = $this->getTable('catalog/product_website');
        $this->_storeTable           = $this->getTable('core/store');
        $this->_groupTable           = $this->getTable('core/store_group');
    }

    /**
     * Process product save.
     * Method is responsible for index support
     * when product was saved and assigned categories was changed.
     *
     * @return $this
     */
    public function catalogProductSave(Mage_Index_Model_Event $event)
    {
        $productId = $event->getEntityPk();
        $data      = $event->getNewData();

        /**
         * Check if category ids were updated
         */
        if (!isset($data['category_ids'])) {
            return $this;
        }

        /**
         * Select relations to categories
         */
        $select = $this->_getWriteAdapter()->select()
            ->from(['cp' => $this->_categoryProductTable], 'category_id')
            ->joinInner(['ce' => $this->_categoryTable], 'ce.entity_id=cp.category_id', 'path')
            ->where('cp.product_id=:product_id');

        /**
         * Get information about product categories
         */
        $categories = $this->_getWriteAdapter()->fetchPairs($select, ['product_id' => $productId]);
        $categoryIds = [];
        $allCategoryIds = [];

        foreach ($categories as $id => $path) {
            $categoryIds[]  = $id;
            $allCategoryIds = array_merge($allCategoryIds, explode('/', $path));
        }
        $allCategoryIds = array_unique($allCategoryIds);
        $allCategoryIds = array_diff($allCategoryIds, $categoryIds);

        /**
         * Delete previous index data
         */
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            ['product_id = ?' => $productId],
        );

        $this->_refreshAnchorRelations($allCategoryIds, $productId);
        $this->_refreshDirectRelations($categoryIds, $productId);
        $this->_refreshRootRelations($productId);
        return $this;
    }

    /**
     * Process Catalog Product mass action
     *
     * @return $this
     */
    public function catalogProductMassAction(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        /**
         * check is product ids were updated
         */
        if (!isset($data['product_ids'])) {
            return $this;
        }
        $productIds     = $data['product_ids'];
        $categoryIds    = [];
        $allCategoryIds = [];

        /**
         * Select relations to categories
         */
        $adapter = $this->_getWriteAdapter();
        $select  = $adapter->select()
            ->distinct()
            ->from(['cp' => $this->_categoryProductTable], ['category_id'])
            ->join(
                ['ce' => $this->_categoryTable],
                'ce.entity_id=cp.category_id',
                ['path'],
            )
            ->where('cp.product_id IN(?)', $productIds);
        $pairs   = $adapter->fetchPairs($select);
        foreach ($pairs as $categoryId => $categoryPath) {
            $categoryIds[] = $categoryId;
            $allCategoryIds = array_merge($allCategoryIds, explode('/', $categoryPath));
        }

        $allCategoryIds = array_unique($allCategoryIds);
        $allCategoryIds = array_diff($allCategoryIds, $categoryIds);

        /**
         * Delete previous index data
         */
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            ['product_id IN(?)' => $productIds],
        );

        $this->_refreshAnchorRelations($allCategoryIds, $productIds);
        $this->_refreshDirectRelations($categoryIds, $productIds);
        $this->_refreshRootRelations($productIds);
        return $this;
    }

    /**
     * Return array of used root category id - path pairs
     *
     * @return array
     */
    protected function _getRootCategories()
    {
        $rootCategories = [];
        $stores = $this->_getStoresInfo();
        foreach ($stores as $storeInfo) {
            if ($storeInfo['root_id']) {
                $rootCategories[$storeInfo['root_id']] = $storeInfo['root_path'];
            }
        }

        return $rootCategories;
    }

    /**
     * Process category index after category save
     */
    public function catalogCategorySave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        $checkRootCategories        = false;
        $processRootCategories      = false;
        $affectedRootCategoryIds    = [];
        $rootCategories             = $this->_getRootCategories();

        /**
         * Check if we have reindex category move results
         */
        if (isset($data['affected_category_ids'])) {
            $categoryIds = $data['affected_category_ids'];
            $checkRootCategories = true;
        } elseif (isset($data['products_was_changed'])) {
            $categoryIds = [$event->getEntityPk()];

            if (isset($rootCategories[$event->getEntityPk()])) {
                $processRootCategories = true;
                $affectedRootCategoryIds[] = $event->getEntityPk();
            }
        } else {
            return;
        }

        $select = $this->_getWriteAdapter()->select()
            ->from($this->_categoryTable, 'path')
            ->where('entity_id IN (?)', $categoryIds);
        $paths = $this->_getWriteAdapter()->fetchCol($select);
        $allCategoryIds = [];
        foreach ($paths as $path) {
            if ($checkRootCategories) {
                foreach ($rootCategories as $rootCategoryId => $rootCategoryPath) {
                    if (strpos($path, sprintf('%d/', $rootCategoryPath)) === 0 || $path == $rootCategoryPath) {
                        $affectedRootCategoryIds[$rootCategoryId] = $rootCategoryId;
                    }
                }
            }
            $allCategoryIds = array_merge($allCategoryIds, explode('/', $path));
        }
        $allCategoryIds = array_unique($allCategoryIds);

        if ($checkRootCategories && count($affectedRootCategoryIds) > 1) {
            $processRootCategories = true;
        }

        /**
         * retrieve anchor category id
         */
        $anchorInfo = $this->_getAnchorAttributeInfo();
        $bind = [
            'attribute_id' => $anchorInfo['id'],
            'store_id'     => Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID,
            'e_value'      => 1,
        ];
        $select = $this->_getReadAdapter()->select()
            ->distinct()
            ->from(['ce' => $this->_categoryTable], ['entity_id'])
            ->joinInner(
                ['dca' => $anchorInfo['table']],
                'dca.entity_id=ce.entity_id AND dca.attribute_id=:attribute_id AND dca.store_id=:store_id',
                [],
            )
             ->where('dca.value=:e_value')
             ->where('ce.entity_id IN (?)', $allCategoryIds);
        $anchorIds = $this->_getWriteAdapter()->fetchCol($select, $bind);
        /**
         * delete only anchor id and category ids
         */
        $deleteCategoryIds = array_merge($anchorIds, $categoryIds);

        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('category_id IN(?)', $deleteCategoryIds),
        );

        $directIds = array_diff($categoryIds, $anchorIds);
        if ($anchorIds) {
            $this->_refreshAnchorRelations($anchorIds);
        }
        if ($directIds) {
            $this->_refreshDirectRelations($directIds);
        }

        /**
         * Need to re-index affected root category ids when its are not anchor
         */
        if ($processRootCategories) {
            $reindexRootCategoryIds = array_diff($affectedRootCategoryIds, $anchorIds);
            if ($reindexRootCategoryIds) {
                $this->_refreshNotAnchorRootCategories($reindexRootCategoryIds);
            }
        }
    }

    /**
     * Reindex not anchor root categories
     *
     * @return $this
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _refreshNotAnchorRootCategories(?array $categoryIds = null)
    {
        if (empty($categoryIds)) {
            return $this;
        }

        $adapter = $this->_getWriteAdapter();

        // remove anchor relations
        $where = [
            'category_id IN(?)' => $categoryIds,
            'is_parent=?'       => 0,
        ];
        $adapter->delete($this->getMainTable(), $where);

        $stores = $this->_getStoresInfo();
        /**
         * Build index for each store
         */
        foreach ($stores as $storeData) {
            $storeId    = $storeData['store_id'];
            $websiteId  = $storeData['website_id'];
            $rootPath   = $storeData['root_path'];
            $rootId     = $storeData['root_id'];
            if (!in_array($rootId, $categoryIds)) {
                continue;
            }

            $select = $adapter->select()
                ->distinct()
                ->from(['cc' => $this->getTable('catalog/category')], null)
                ->join(
                    ['i' => $this->getMainTable()],
                    'i.category_id = cc.entity_id and i.store_id = 1',
                    [],
                )
                ->joinLeft(
                    ['ie' => $this->getMainTable()],
                    'ie.category_id = ' . (int) $rootId
                        . ' AND ie.product_id=i.product_id AND ie.store_id = ' . (int) $storeId,
                    [],
                )
                ->where('cc.path LIKE ?', $rootPath . '/%')
                ->where('ie.category_id IS NULL')
                ->columns([
                    'category_id'   => new Zend_Db_Expr($rootId),
                    'product_id'    => 'i.product_id',
                    'position'      => new Zend_Db_Expr('0'),
                    'is_parent'     => new Zend_Db_Expr('0'),
                    'store_id'      => new Zend_Db_Expr($storeId),
                    'visibility'    => 'i.visibility',
                ]);
            $query = $select->insertFromSelect($this->getMainTable());
            $adapter->query($query);

            $visibilityInfo = $this->_getVisibilityAttributeInfo();
            $statusInfo     = $this->_getStatusAttributeInfo();

            $select = $this->_getReadAdapter()->select()
                ->from(['pw' => $this->_productWebsiteTable], [])
                ->joinLeft(
                    ['i' => $this->getMainTable()],
                    'i.product_id = pw.product_id AND i.category_id = ' . (int) $rootId
                        . ' AND i.store_id = ' . (int) $storeId,
                    [],
                )
                ->join(
                    ['dv' => $visibilityInfo['table']],
                    "dv.entity_id = pw.product_id AND dv.attribute_id = {$visibilityInfo['id']} AND dv.store_id = 0",
                    [],
                )
                ->joinLeft(
                    ['sv' => $visibilityInfo['table']],
                    "sv.entity_id = pw.product_id AND sv.attribute_id = {$visibilityInfo['id']}"
                        . ' AND sv.store_id = ' . (int) $storeId,
                    [],
                )
                ->join(
                    ['ds' => $statusInfo['table']],
                    "ds.entity_id = pw.product_id AND ds.attribute_id = {$statusInfo['id']} AND ds.store_id = 0",
                    [],
                )
                ->joinLeft(
                    ['ss' => $statusInfo['table']],
                    "ss.entity_id = pw.product_id AND ss.attribute_id = {$statusInfo['id']}"
                        . ' AND ss.store_id = ' . (int) $storeId,
                    [],
                )
                ->where('i.product_id IS NULL')
                ->where('pw.website_id=?', $websiteId)
                ->where(
                    $this->_getWriteAdapter()->getCheckSql('ss.value_id IS NOT NULL', 'ss.value', 'ds.value') . ' = ?',
                    Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
                )
                ->columns([
                    'category_id'   => new Zend_Db_Expr($rootId),
                    'product_id'    => 'pw.product_id',
                    'position'      => new Zend_Db_Expr('0'),
                    'is_parent'     => new Zend_Db_Expr('1'),
                    'store_id'      => new Zend_Db_Expr($storeId),
                    'visibility'    => $adapter->getCheckSql('sv.value_id IS NOT NULL', 'sv.value', 'dv.value'),
                ]);

            $query = $select->insertFromSelect($this->getMainTable());
            $this->_getWriteAdapter()->query($query);
        }

        return $this;
    }

    /**
     * Rebuild index for direct associations categories and products
     *
     * @param null|array $categoryIds
     * @param null|array $productIds
     * @return $this
     */
    protected function _refreshDirectRelations($categoryIds = null, $productIds = null)
    {
        if (!$categoryIds && !$productIds) {
            return $this;
        }

        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();
        $adapter = $this->_getWriteAdapter();
        /**
         * Insert direct relations
         * product_ids (enabled filter) X category_ids X store_ids
         * Validate store root category
         */
        $isParent = new Zend_Db_Expr('1');
        $select = $adapter->select()
            ->from(
                ['cp' => $this->_categoryProductTable],
                ['category_id', 'product_id', 'position', $isParent],
            )
            ->joinInner(['pw'  => $this->_productWebsiteTable], 'pw.product_id=cp.product_id', [])
            ->joinInner(['g'   => $this->_groupTable], 'g.website_id=pw.website_id', [])
            ->joinInner(['s'   => $this->_storeTable], 's.group_id=g.group_id', ['store_id'])
            ->joinInner(['rc'  => $this->_categoryTable], 'rc.entity_id=g.root_category_id', [])
            ->joinInner(
                ['ce' => $this->_categoryTable],
                'ce.entity_id=cp.category_id AND (' .
                $adapter->quoteIdentifier('ce.path') . ' LIKE ' .
                $adapter->getConcatSql([$adapter->quoteIdentifier('rc.path') , $adapter->quote('/%')]) .
                ' OR ce.entity_id=rc.entity_id)',
                [],
            )
            ->joinLeft(
                ['dv' => $visibilityInfo['table']],
                $adapter->quoteInto(
                    'dv.entity_id=cp.product_id AND dv.attribute_id=? AND dv.store_id=0',
                    $visibilityInfo['id'],
                ),
                [],
            )
            ->joinLeft(
                ['sv' => $visibilityInfo['table']],
                $adapter->quoteInto(
                    'sv.entity_id=cp.product_id AND sv.attribute_id=? AND sv.store_id=s.store_id',
                    $visibilityInfo['id'],
                ),
                ['visibility' => $adapter->getCheckSql(
                    'sv.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('sv.value'),
                    $adapter->quoteIdentifier('dv.value'),
                )],
            )
            ->joinLeft(
                ['ds' => $statusInfo['table']],
                "ds.entity_id=cp.product_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                [],
            )
            ->joinLeft(
                ['ss' => $statusInfo['table']],
                "ss.entity_id=cp.product_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                [],
            )
            ->where(
                $adapter->getCheckSql(
                    'ss.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('ss.value'),
                    $adapter->quoteIdentifier('ds.value'),
                ) . ' = ?',
                Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
            );
        if ($categoryIds) {
            $select->where('cp.category_id IN (?)', $categoryIds);
        }
        if ($productIds) {
            $select->where('cp.product_id IN(?)', $productIds);
        }
        $sql = $select->insertFromSelect(
            $this->getMainTable(),
            ['category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'],
            true,
        );
        $adapter->query($sql);
        return $this;
    }

    /**
     * Rebuild index for anchor categories and associated to child categories products
     *
     * @param null|array $categoryIds
     * @param null|array $productIds
     * @return $this
     */
    protected function _refreshAnchorRelations($categoryIds = null, $productIds = null)
    {
        if (!$categoryIds && !$productIds) {
            return $this;
        }

        $anchorInfo     = $this->_getAnchorAttributeInfo();
        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();

        /**
         * Insert anchor categories relations
         */
        $adapter = $this->_getReadAdapter();
        $isParent = $adapter->getCheckSql('MIN(cp.category_id)=ce.entity_id', '1', '0');
        $position = 'MIN(' .
            $adapter->getCheckSql(
                'cp.category_id = ce.entity_id',
                'cp.position',
                '(cc.position + 1) * (' . $adapter->quoteIdentifier('cc.level') . ' + 1) * 10000 + cp.position',
            )
        . ')';

        $select = $adapter->select()
            ->distinct()
            ->from(['ce' => $this->_categoryTable], ['entity_id'])
            ->joinInner(
                ['cc' => $this->_categoryTable],
                $adapter->quoteIdentifier('cc.path') .
                ' LIKE (' . $adapter->getConcatSql([$adapter->quoteIdentifier('ce.path'),$adapter->quote('/%')]) . ')'
                . ' OR cc.entity_id=ce.entity_id',
                [],
            )
            ->joinInner(
                ['cp' => $this->_categoryProductTable],
                'cp.category_id=cc.entity_id',
                ['cp.product_id', 'position' => $position, 'is_parent' => $isParent],
            )
            ->joinInner(['pw' => $this->_productWebsiteTable], 'pw.product_id=cp.product_id', [])
            ->joinInner(['g'  => $this->_groupTable], 'g.website_id=pw.website_id', [])
            ->joinInner(['s'  => $this->_storeTable], 's.group_id=g.group_id', ['store_id'])
            ->joinInner(['rc' => $this->_categoryTable], 'rc.entity_id=g.root_category_id', [])
            ->joinLeft(
                ['dca' => $anchorInfo['table']],
                "dca.entity_id=ce.entity_id AND dca.attribute_id={$anchorInfo['id']} AND dca.store_id=0",
                [],
            )
            ->joinLeft(
                ['sca' => $anchorInfo['table']],
                "sca.entity_id=ce.entity_id AND sca.attribute_id={$anchorInfo['id']} AND sca.store_id=s.store_id",
                [],
            )
            ->joinLeft(
                ['dv' => $visibilityInfo['table']],
                "dv.entity_id=pw.product_id AND dv.attribute_id={$visibilityInfo['id']} AND dv.store_id=0",
                [],
            )
            ->joinLeft(
                ['sv' => $visibilityInfo['table']],
                "sv.entity_id=pw.product_id AND sv.attribute_id={$visibilityInfo['id']} AND sv.store_id=s.store_id",
                ['visibility' => $adapter->getCheckSql(
                    'MIN(sv.value_id) IS NOT NULL',
                    'MIN(sv.value)',
                    'MIN(dv.value)',
                )],
            )
            ->joinLeft(
                ['ds' => $statusInfo['table']],
                "ds.entity_id=pw.product_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                [],
            )
            ->joinLeft(
                ['ss' => $statusInfo['table']],
                "ss.entity_id=pw.product_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                [],
            )
            /**
             * Condition for anchor or root category (all products should be assigned to root)
             */
            ->where('(' .
                $adapter->quoteIdentifier('ce.path') . ' LIKE ' .
                $adapter->getConcatSql([$adapter->quoteIdentifier('rc.path'), $adapter->quote('/%')]) . ' AND ' .
                $adapter->getCheckSql(
                    'sca.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('sca.value'),
                    $adapter->quoteIdentifier('dca.value'),
                ) . '=1) OR ce.entity_id=rc.entity_id')
            ->where(
                $adapter->getCheckSql('ss.value_id IS NOT NULL', 'ss.value', 'ds.value') . '=?',
                Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
            )
                ->group(['ce.entity_id', 'cp.product_id', 's.store_id']);
        if ($categoryIds) {
            $select->where('ce.entity_id IN (?)', $categoryIds);
        }
        if ($productIds) {
            $select->where('pw.product_id IN(?)', $productIds);
        }

        $sql = $select->insertFromSelect($this->getMainTable());
        $this->_getWriteAdapter()->query($sql);
        return $this;
    }

    /**
     * Add product association with root store category for products which are not assigned to any another category
     *
     * @param int | array $productIds
     * @return $this
     */
    protected function _refreshRootRelations($productIds)
    {
        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();
        $adapter = $this->_getWriteAdapter();
        /**
         * Insert anchor categories relations
         */
        $isParent = new Zend_Db_Expr('0');
        $position = new Zend_Db_Expr('0');
        $select = $this->_getReadAdapter()->select()
            ->distinct()
            ->from(['pw'  => $this->_productWebsiteTable], [])
            ->joinInner(['g'   => $this->_groupTable], 'g.website_id=pw.website_id', [])
            ->joinInner(['s'   => $this->_storeTable], 's.group_id=g.group_id', [])
            ->joinInner(
                ['rc'  => $this->_categoryTable],
                'rc.entity_id=g.root_category_id',
                ['entity_id'],
            )
            ->joinLeft(
                ['cp'   => $this->_categoryProductTable],
                'cp.product_id=pw.product_id',
                ['pw.product_id', $position, $isParent, 's.store_id'],
            )
            ->joinLeft(
                ['dv' => $visibilityInfo['table']],
                "dv.entity_id=pw.product_id AND dv.attribute_id={$visibilityInfo['id']} AND dv.store_id=0",
                [],
            )
            ->joinLeft(
                ['sv' => $visibilityInfo['table']],
                "sv.entity_id=pw.product_id AND sv.attribute_id={$visibilityInfo['id']} AND sv.store_id=s.store_id",
                ['visibility' => $adapter->getCheckSql(
                    'sv.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('sv.value'),
                    $adapter->quoteIdentifier('dv.value'),
                )],
            )
            ->joinLeft(
                ['ds' => $statusInfo['table']],
                "ds.entity_id=pw.product_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                [],
            )
            ->joinLeft(
                ['ss' => $statusInfo['table']],
                "ss.entity_id=pw.product_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                [],
            )
            /**
             * Condition for anchor or root category (all products should be assigned to root)
             */
            ->where('cp.product_id IS NULL')
            ->where(
                $adapter->getCheckSql(
                    'ss.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('ss.value'),
                    $adapter->quoteIdentifier('ds.value'),
                ) . ' = ?',
                Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
            )
            ->where('pw.product_id IN(?)', $productIds);

        $sql = $select->insertFromSelect($this->getMainTable());
        $this->_getWriteAdapter()->query($sql);

        $select = $this->_getReadAdapter()->select()
            ->from(['pw' => $this->_productWebsiteTable], [])
            ->joinInner(['g' => $this->_groupTable], 'g.website_id = pw.website_id', [])
            ->joinInner(['s' => $this->_storeTable], 's.group_id = g.group_id', [])
            ->joinLeft(
                ['i'  => $this->getMainTable()],
                'i.product_id = pw.product_id AND i.category_id = g.root_category_id',
                [],
            )
            ->joinLeft(
                ['dv' => $visibilityInfo['table']],
                "dv.entity_id = pw.product_id AND dv.attribute_id = {$visibilityInfo['id']} AND dv.store_id = 0",
                [],
            )
            ->joinLeft(
                ['sv' => $visibilityInfo['table']],
                "sv.entity_id = pw.product_id AND sv.attribute_id = {$visibilityInfo['id']}"
                    . ' AND sv.store_id = s.store_id',
                [],
            )
            ->join(
                ['ds' => $statusInfo['table']],
                "ds.entity_id = pw.product_id AND ds.attribute_id = {$statusInfo['id']} AND ds.store_id = 0",
                [],
            )
            ->joinLeft(
                ['ss' => $statusInfo['table']],
                "ss.entity_id = pw.product_id AND ss.attribute_id = {$statusInfo['id']} AND ss.store_id = s.store_id",
                [],
            )
            ->where('i.product_id IS NULL')
            ->where(
                $adapter->getCheckSql('ss.value_id IS NOT NULL', 'ss.value', 'ds.value') . '=?',
                Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
            )
            ->where('pw.product_id IN(?)', $productIds)
            ->columns([
                'category_id'   => 'g.root_category_id',
                'product_id'    => 'pw.product_id',
                'position'      => $position,
                'is_parent'     => new Zend_Db_Expr('1'),
                'store_id'      => 's.store_id',
                'visibility'    => $adapter->getCheckSql('sv.value_id IS NOT NULL', 'sv.value', 'dv.value'),
            ]);

        $sql = $select->insertFromSelect($this->getMainTable());
        $this->_getWriteAdapter()->query($sql);

        return $this;
    }

    /**
     * Get is_anchor category attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getAnchorAttributeInfo()
    {
        $isAnchorAttribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Category::ENTITY, 'is_anchor');
        return [
            'id'    => $isAnchorAttribute->getId() ,
            'table' => $isAnchorAttribute->getBackend()->getTable(),
        ];
    }

    /**
     * Get visibility product attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getVisibilityAttributeInfo()
    {
        $visibilityAttribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'visibility');
        return [
            'id'    => $visibilityAttribute->getId() ,
            'table' => $visibilityAttribute->getBackend()->getTable(),
        ];
    }

    /**
     * Get status product attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getStatusAttributeInfo()
    {
        $statusAttribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'status');
        return [
            'id'    => $statusAttribute->getId() ,
            'table' => $statusAttribute->getBackend()->getTable(),
        ];
    }

    /**
     * Rebuild all index data
     *
     * @return $this
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            $this->clearTemporaryIndexTable();
            $idxTable = $this->getIdxTable();
            $idxAdapter = $this->_getIndexAdapter();
            $stores = $this->_getStoresInfo();
            /**
             * Build index for each store
             */
            foreach ($stores as $storeData) {
                $storeId    = $storeData['store_id'];
                $websiteId  = $storeData['website_id'];
                $rootPath   = $storeData['root_path'];
                $rootId     = $storeData['root_id'];
                /**
                 * Prepare visibility for all enabled store products
                 */
                $enabledTable = $this->_prepareEnabledProductsVisibility($websiteId, $storeId);
                /**
                 * Select information about anchor categories
                 */
                $anchorTable = $this->_prepareAnchorCategories($storeId, $rootPath);
                /**
                 * Add relations between not anchor categories and products
                 */
                $select = $idxAdapter->select();
                $select->from(
                    ['cp' => $this->_categoryProductTable],
                    ['category_id', 'product_id', 'position', 'is_parent' => new Zend_Db_Expr('1'),
                        'store_id' => new Zend_Db_Expr($storeId)],
                )
                ->joinInner(['pv' => $enabledTable], 'pv.product_id=cp.product_id', ['visibility'])
                ->joinLeft(['ac' => $anchorTable], 'ac.category_id=cp.category_id', [])
                ->where('ac.category_id IS NULL');

                $query = $select->insertFromSelect(
                    $idxTable,
                    ['category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'],
                    false,
                );
                $idxAdapter->query($query);

                /**
                 * Assign products not associated to any category to root category in index
                 */

                $select = $idxAdapter->select();
                $select->from(
                    ['pv' => $enabledTable],
                    [new Zend_Db_Expr($rootId), 'product_id', new Zend_Db_Expr('0'), new Zend_Db_Expr('1'),
                        new Zend_Db_Expr($storeId), 'visibility'],
                )
                ->joinLeft(['cp' => $this->_categoryProductTable], 'pv.product_id=cp.product_id', [])
                ->where('cp.product_id IS NULL');

                $query = $select->insertFromSelect(
                    $idxTable,
                    ['category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'],
                    false,
                );
                $idxAdapter->query($query);

                /**
                 * Prepare anchor categories products
                 */
                $anchorProductsTable = $this->_getAnchorCategoriesProductsTemporaryTable();
                // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                $idxAdapter->delete($anchorProductsTable);

                $position = 'MIN(' .
                    $idxAdapter->getCheckSql(
                        'ca.category_id = ce.entity_id',
                        $idxAdapter->quoteIdentifier('cp.position'),
                        '(' . $idxAdapter->quoteIdentifier('ce.position') . ' + 1) * '
                        . '(' . $idxAdapter->quoteIdentifier('ce.level') . ' + 1 * 10000)'
                        . ' + ' . $idxAdapter->quoteIdentifier('cp.position'),
                    )
                . ')';

                $select = $idxAdapter->select()
                ->useStraightJoin(true)
                ->distinct()
                ->from(['ca' => $anchorTable], ['category_id'])
                ->joinInner(
                    ['ce' => $this->_categoryTable],
                    $idxAdapter->quoteIdentifier('ce.path') . ' LIKE ' .
                    $idxAdapter->quoteIdentifier('ca.path') . ' OR ce.entity_id = ca.category_id',
                    [],
                )
                ->joinInner(
                    ['cp' => $this->_categoryProductTable],
                    'cp.category_id = ce.entity_id',
                    ['product_id'],
                )
                ->joinInner(
                    ['pv' => $enabledTable],
                    'pv.product_id = cp.product_id',
                    ['position' => $position],
                )
                ->group(['ca.category_id', 'cp.product_id']);
                $query = $select->insertFromSelect(
                    $anchorProductsTable,
                    ['category_id', 'product_id', 'position'],
                    false,
                );
                $idxAdapter->query($query);

                /**
                 * Add anchor categories products to index
                 */
                $select = $idxAdapter->select()
                ->from(
                    ['ap' => $anchorProductsTable],
                    ['category_id', 'product_id',
                        'position', // => new Zend_Db_Expr('MIN('. $idxAdapter->quoteIdentifier('ap.position').')'),
                        'is_parent' => $idxAdapter->getCheckSql('cp.product_id > 0', '1', '0'),
                        'store_id' => new Zend_Db_Expr($storeId)],
                )
                ->joinLeft(
                    ['cp' => $this->_categoryProductTable],
                    'cp.category_id=ap.category_id AND cp.product_id=ap.product_id',
                    [],
                )
                ->joinInner(['pv' => $enabledTable], 'pv.product_id = ap.product_id', ['visibility']);

                $query = $select->insertFromSelect(
                    $idxTable,
                    ['category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'],
                    false,
                );
                $idxAdapter->query($query);

                $select = $idxAdapter->select()
                    ->from(['e' => $this->getTable('catalog/product')], null)
                    ->join(
                        ['ei' => $enabledTable],
                        'ei.product_id = e.entity_id',
                        [],
                    )
                    ->joinLeft(
                        ['i' => $idxTable],
                        'i.product_id = e.entity_id AND i.category_id = :category_id AND i.store_id = :store_id',
                        [],
                    )
                    ->where('i.product_id IS NULL')
                    ->columns([
                        'category_id'   => new Zend_Db_Expr($rootId),
                        'product_id'    => 'e.entity_id',
                        'position'      => new Zend_Db_Expr('0'),
                        'is_parent'     => new Zend_Db_Expr('1'),
                        'store_id'      => new Zend_Db_Expr($storeId),
                        'visibility'    => 'ei.visibility',
                    ]);

                $query = $select->insertFromSelect(
                    $idxTable,
                    ['category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'],
                    false,
                );

                $idxAdapter->query($query, ['store_id' => $storeId, 'category_id' => $rootId]);
            }

            $this->syncData();

            /**
             * Clean up temporary tables
             */
            $this->clearTemporaryIndexTable();
            $idxAdapter->delete($enabledTable);
            $idxAdapter->delete($anchorTable);
            $idxAdapter->delete($anchorProductsTable);
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Create temporary table with enabled products visibility info
     *
     * @param int $websiteId
     * @param int $storeId
     * @return string temporary table name
     */
    protected function _prepareEnabledProductsVisibility($websiteId, $storeId)
    {
        $statusAttribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'status');
        $visibilityAttribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'visibility');
        $statusAttributeId = $statusAttribute->getId();
        $visibilityAttributeId = $visibilityAttribute->getId();
        $statusTable = $statusAttribute->getBackend()->getTable();
        $visibilityTable = $visibilityAttribute->getBackend()->getTable();

        /**
         * Prepare temporary table
         */
        $tmpTable = $this->_getEnabledProductsTemporaryTable();
        $this->_getIndexAdapter()->delete($tmpTable);

        $adapter        = $this->_getIndexAdapter();
        $visibilityExpr = $adapter->getCheckSql(
            'pvs.value_id>0',
            $adapter->quoteIdentifier('pvs.value'),
            $adapter->quoteIdentifier('pvd.value'),
        );
        $select         = $adapter->select()
            ->from(['pw' => $this->_productWebsiteTable], ['product_id', 'visibility' => $visibilityExpr])
            ->joinLeft(
                ['pvd' => $visibilityTable],
                $adapter->quoteInto(
                    'pvd.entity_id=pw.product_id AND pvd.attribute_id=? AND pvd.store_id=0',
                    $visibilityAttributeId,
                ),
                [],
            )
            ->joinLeft(
                ['pvs' => $visibilityTable],
                $adapter->quoteInto('pvs.entity_id=pw.product_id AND pvs.attribute_id=? AND ', $visibilityAttributeId)
                    . $adapter->quoteInto('pvs.store_id=?', $storeId),
                [],
            )
            ->joinLeft(
                ['psd' => $statusTable],
                $adapter->quoteInto(
                    'psd.entity_id=pw.product_id AND psd.attribute_id=? AND psd.store_id=0',
                    $statusAttributeId,
                ),
                [],
            )
            ->joinLeft(
                ['pss' => $statusTable],
                $adapter->quoteInto('pss.entity_id=pw.product_id AND pss.attribute_id=? AND ', $statusAttributeId)
                        . $adapter->quoteInto('pss.store_id=?', $storeId),
                [],
            )
            ->where('pw.website_id=?', $websiteId)
            ->where($adapter->getCheckSql(
                'pss.value_id > 0',
                $adapter->quoteIdentifier('pss.value'),
                $adapter->quoteIdentifier('psd.value'),
            ) . ' = ?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        $query = $select->insertFromSelect($tmpTable, ['product_id' , 'visibility'], false);
        $adapter->query($query);
        return $tmpTable;
    }

    /**
     * Retrieve temporary table of category enabled products
     *
     * @return string
     */
    protected function _getEnabledProductsTemporaryTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/category_product_enabled_indexer_idx');
        }
        return $this->getTable('catalog/category_product_enabled_indexer_tmp');
    }

    /**
     * Get array with store|website|root_categry path information
     *
     * @return array
     */
    protected function _getStoresInfo()
    {
        if (is_null($this->_storesInfo)) {
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select()
                ->from(['s' => $this->getTable('core/store')], ['store_id', 'website_id'])
                ->join(
                    ['sg' => $this->getTable('core/store_group')],
                    'sg.group_id = s.group_id',
                    [],
                )
                ->join(
                    ['c' => $this->getTable('catalog/category')],
                    'c.entity_id = sg.root_category_id',
                    [
                        'root_path' => 'path',
                        'root_id'   => 'entity_id',
                    ],
                );
            $this->_storesInfo = $adapter->fetchAll($select);
        }

        return $this->_storesInfo;
    }

    /**
     * @param int $storeId
     * @param string $rootPath
     * @return string temporary table name
     */
    protected function _prepareAnchorCategories($storeId, $rootPath)
    {
        $isAnchorAttribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Category::ENTITY, 'is_anchor');
        $anchorAttributeId = $isAnchorAttribute->getId();
        $anchorTable = $isAnchorAttribute->getBackend()->getTable();
        $adapter = $this->_getIndexAdapter();
        $tmpTable = $this->_getAnchorCategoriesTemporaryTable();
        $adapter->delete($tmpTable);

        $anchorExpr = $adapter->getCheckSql(
            'cas.value_id>0',
            $adapter->quoteIdentifier('cas.value'),
            $adapter->quoteIdentifier('cad.value'),
        );
        $pathConcat = $adapter->getConcatSql([$adapter->quoteIdentifier('ce.path'), $adapter->quote('/%')]);
        $select = $adapter->select()
            ->from(
                ['ce' => $this->_categoryTable],
                ['category_id' => 'ce.entity_id', 'path' => $pathConcat],
            )
            ->joinLeft(
                ['cad' => $anchorTable],
                $adapter->quoteInto(
                    'cad.entity_id=ce.entity_id AND cad.attribute_id=? AND cad.store_id=0',
                    $anchorAttributeId,
                ),
                [],
            )
            ->joinLeft(
                ['cas' => $anchorTable],
                $adapter->quoteInto('cas.entity_id=ce.entity_id AND cas.attribute_id=? AND ', $anchorAttributeId)
                    . $adapter->quoteInto('cas.store_id=?', $storeId),
                [],
            )
            ->where("{$anchorExpr} = 1 AND {$adapter->quoteIdentifier('ce.path')} LIKE ?", $rootPath . '%')
            ->orWhere('ce.path = ?', $rootPath);

        $query = $select->insertFromSelect($tmpTable, ['category_id' , 'path'], false);
        $adapter->query($query);
        return $tmpTable;
    }

    /**
     * Retrieve temporary table of anchor categories
     *
     * @return string
     */
    protected function _getAnchorCategoriesTemporaryTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/category_anchor_indexer_idx');
        }
        return $this->getTable('catalog/category_anchor_indexer_tmp');
    }

    /**
     * Retrieve temporary table of anchor categories products
     *
     * @return string
     */
    protected function _getAnchorCategoriesProductsTemporaryTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/category_anchor_products_indexer_idx');
        }
        return $this->getTable('catalog/category_anchor_products_indexer_tmp');
    }

    /**
     * Retrieve temporary decimal index table name
     *
     * @param string $table
     * @return string
     */
    public function getIdxTable($table = null)
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/category_product_indexer_idx');
        }
        return $this->getTable('catalog/category_product_indexer_tmp');
    }
}
