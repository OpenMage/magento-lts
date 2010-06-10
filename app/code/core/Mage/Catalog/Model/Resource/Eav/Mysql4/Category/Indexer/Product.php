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
class Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Indexer_Product extends Mage_Index_Model_Mysql4_Abstract
{
    protected $_categoryTable;
    protected $_categoryProductTable;
    protected $_productWebsiteTable;
    protected $_storeTable;
    protected $_groupTable;

    protected function _construct()
    {
        $this->_init('catalog/category_product_index', 'category_id');
        $this->_categoryTable = $this->getTable('catalog/category');
        $this->_categoryProductTable = $this->getTable('catalog/category_product');
        $this->_productWebsiteTable = $this->getTable('catalog/product_website');
        $this->_storeTable = $this->getTable('core/store');
        $this->_groupTable = $this->getTable('core/store_group');
    }

    /**
     * Process product save.
     * Method is responsible for index support
     * when product was saved and assigned categories was changed.
     *
     * @param   Mage_Index_Model_Event $event
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Indexer_Product
     */
    public function catalogProductSave(Mage_Index_Model_Event $event)
    {
        $productId = $event->getEntityPk();
        $data = $event->getNewData();

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
            ->from(array('cp' => $this->_categoryProductTable), 'category_id')
            ->joinInner(array('ce' => $this->_categoryTable), 'ce.entity_id=cp.category_id', 'path')
            ->where('cp.product_id=?', $productId);

        /**
         * Get information about product categories
         */
        $categories = $this->_getWriteAdapter()->fetchPairs($select);
        $categoryIds = array();
        $allCategoryIds = array();

        foreach ($categories as $id=>$path) {
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
            $this->_getWriteAdapter()->quoteInto('product_id=?', $productId)
        );

        $this->_refreshAnchorRelations($allCategoryIds, $productId);
        $this->_refreshDirectRelations($categoryIds, $productId);
        $this->_refreshRootRelations($productId);
        return $this;
    }

    /**
     * Process Catalog Product mass action
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Indexer_Product
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
        $categoryIds    = array();
        $allCategoryIds = array();

        /**
         * Select relations to categories
         */
        $adapter = $this->_getWriteAdapter();
        $select  = $adapter->select()
            ->distinct(true)
            ->from(array('cp' => $this->_categoryProductTable), array('category_id'))
            ->join(
                array('ce' => $this->_categoryTable),
                'ce.entity_id=cp.category_id',
                array('path'))
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
            $this->getMainTable(), $this->_getWriteAdapter()->quoteInto('product_id IN(?)', $productIds)
        );

        $this->_refreshAnchorRelations($allCategoryIds, $productIds);
        $this->_refreshDirectRelations($categoryIds, $productIds);
        $this->_refreshRootRelations($productIds);
        return $this;
    }

    /**
     * Process category index after category save
     *
     * @param Mage_Index_Model_Event $event
     */
    public function catalogCategorySave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        /**
         * Check if we have reindex category move results
         */
        if (isset($data['affected_category_ids'])) {
            $categoryIds = $event->getNewData('affected_category_ids');
        } else if (isset($data['products_was_changed'])) {
            $categoryIds = array($event->getEntityPk());
        } else {
            return;
        }

        $select = $this->_getWriteAdapter()->select()
            ->from($this->_categoryTable, 'path')
            ->where('entity_id IN (?)', $categoryIds);
        $paths = $this->_getWriteAdapter()->fetchCol($select);
        $allCategoryIds = array();
        foreach ($paths as $path) {
            $allCategoryIds = array_merge($allCategoryIds, explode('/', $path));
        }
        $allCategoryIds = array_unique($allCategoryIds);

        /**
         * retrieve anchor category id
         */
        $anchorInfo     = $this->_getAnchorAttributeInfo();
        $select = $this->_getReadAdapter()->select()
            ->distinct(true)
            ->from(array('ce' => $this->_categoryTable), array('entity_id'))
            ->joinInner(
                array('dca'=>$anchorInfo['table']),
                "dca.entity_id=ce.entity_id AND dca.attribute_id={$anchorInfo['id']} AND dca.store_id=0",
                array())
             ->where('dca.value=1')
             ->where('ce.entity_id IN (?)', $allCategoryIds);
        $anchorIds = $this->_getWriteAdapter()->fetchCol($select);
        /**
         * delete only anchor id and category ids
         */
        $deleteCategoryIds = array_merge($anchorIds,$categoryIds);

        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('category_id IN(?)', $deleteCategoryIds)
        );

        $anchorIds = array_diff($anchorIds, $categoryIds);
        $this->_refreshAnchorRelations($anchorIds);
        $this->_refreshDirectRelations($categoryIds);
    }

    /**
     * Rebuild index for direct associations categories and products
     *
     * @param   null|array $categoryIds
     * @param   null|array $productIds
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Indexer_Product
     */
    protected function _refreshDirectRelations($categoryIds=null, $productIds=null)
    {
        if (!$categoryIds && !$productIds) {
            return $this;
        }

        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();

        /**
         * Insert direct relations
         * product_ids (enabled filter) X category_ids X store_ids
         * Validate store root category
         */
        $isParent = new Zend_Db_Expr('1 AS is_parent');
        $select = $this->_getWriteAdapter()->select()
            ->from(array('cp' => $this->_categoryProductTable),
                array('category_id', 'product_id', 'position', $isParent))
            ->joinInner(array('pw'  => $this->_productWebsiteTable), 'pw.product_id=cp.product_id', array())
            ->joinInner(array('g'   => $this->_groupTable), 'g.website_id=pw.website_id', array())
            ->joinInner(array('s'   => $this->_storeTable), 's.group_id=g.group_id', array('store_id'))
            ->joinInner(array('rc'  => $this->_categoryTable), 'rc.entity_id=g.root_category_id', array())
            ->joinInner(
                array('ce'=>$this->_categoryTable),
                'ce.entity_id=cp.category_id AND (ce.path LIKE CONCAT(rc.path, \'/%\') OR ce.entity_id=rc.entity_id)',
                array())
            ->joinLeft(
                array('dv'=>$visibilityInfo['table']),
                "dv.entity_id=cp.product_id AND dv.attribute_id={$visibilityInfo['id']} AND dv.store_id=0",
                array())
            ->joinLeft(
                array('sv'=>$visibilityInfo['table']),
                "sv.entity_id=cp.product_id AND sv.attribute_id={$visibilityInfo['id']} AND sv.store_id=s.store_id",
                array('visibility' => 'IF(sv.value_id, sv.value, dv.value)'))
            ->joinLeft(
                array('ds'=>$statusInfo['table']),
                "ds.entity_id=cp.product_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                array())
            ->joinLeft(
                array('ss'=>$statusInfo['table']),
                "ss.entity_id=cp.product_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                array())
            ->where('IF(ss.value_id, ss.value, ds.value)=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        if ($categoryIds) {
            $select->where('cp.category_id IN (?)', $categoryIds);
        }
        if ($productIds) {
            $select->where('cp.product_id IN(?)', $productIds);
        }
        $sql = $select->insertFromSelect(
            $this->getMainTable(),
            array('category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'),
            true
        );
        $this->_getWriteAdapter()->query($sql);
        return $this;
    }

    /**
     * Rebuild index for anchor categories and associated t child categories products
     *
     * @param   null | array $categoryIds
     * @param   null | array $productIds
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Indexer_Product
     */
    protected function _refreshAnchorRelations($categoryIds=null, $productIds=null)
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
        $isParent = new Zend_Db_Expr('0');
        $position = new Zend_Db_Expr('0');
        $select = $this->_getReadAdapter()->select()
            ->distinct(true)
            ->from(array('ce' => $this->_categoryTable), array('entity_id'))
            ->joinInner(array('cc'   => $this->_categoryTable), 'cc.path LIKE CONCAT(ce.path, \'/%\')', array())
            ->joinInner(array('cp'   => $this->_categoryProductTable), 'cp.category_id=cc.entity_id', array())
            ->joinInner(
                array('pw'  => $this->_productWebsiteTable),
                'pw.product_id=cp.product_id',
                array('product_id', $position, $isParent)
            )
            ->joinInner(array('g'   => $this->_groupTable), 'g.website_id=pw.website_id', array())
            ->joinInner(array('s'   => $this->_storeTable), 's.group_id=g.group_id', array('store_id'))
            ->joinInner(array('rc'  => $this->_categoryTable), 'rc.entity_id=g.root_category_id', array())
            ->joinLeft(
                array('dca'=>$anchorInfo['table']),
                "dca.entity_id=ce.entity_id AND dca.attribute_id={$anchorInfo['id']} AND dca.store_id=0",
                array())
            ->joinLeft(
                array('sca'=>$anchorInfo['table']),
                "sca.entity_id=ce.entity_id AND sca.attribute_id={$anchorInfo['id']} AND sca.store_id=s.store_id",
                array())
            ->joinLeft(
                array('dv'=>$visibilityInfo['table']),
                "dv.entity_id=pw.product_id AND dv.attribute_id={$visibilityInfo['id']} AND dv.store_id=0",
                array())
            ->joinLeft(
                array('sv'=>$visibilityInfo['table']),
                "sv.entity_id=pw.product_id AND sv.attribute_id={$visibilityInfo['id']} AND sv.store_id=s.store_id",
                array('visibility' => 'IF(sv.value_id, sv.value, dv.value)'))
            ->joinLeft(
                array('ds'=>$statusInfo['table']),
                "ds.entity_id=pw.product_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                array())
            ->joinLeft(
                array('ss'=>$statusInfo['table']),
                "ss.entity_id=pw.product_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                array())
            /**
             * Condition for anchor or root category (all products should be assigned to root)
             */
            ->where('(ce.path LIKE CONCAT(rc.path, \'/%\') AND IF(sca.value_id, sca.value, dca.value)=1) OR ce.entity_id=rc.entity_id')
            ->where('IF(ss.value_id, ss.value, ds.value)=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

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
     * @param   int | array $productIds
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Indexer_Product
     */
    protected function _refreshRootRelations($productIds)
    {
        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();

        /**
         * Insert anchor categories relations
         */
        $isParent = new Zend_Db_Expr('0');
        $position = new Zend_Db_Expr('0');
        $select = $this->_getReadAdapter()->select()
            ->distinct(true)
            ->from(array('pw'  => $this->_productWebsiteTable), array())
            ->joinInner(array('g'   => $this->_groupTable), 'g.website_id=pw.website_id', array())
            ->joinInner(array('s'   => $this->_storeTable), 's.group_id=g.group_id', array())
            ->joinInner(array('rc'  => $this->_categoryTable), 'rc.entity_id=g.root_category_id',
                array('entity_id'))
            ->joinLeft(array('cp'   => $this->_categoryProductTable), 'cp.product_id=pw.product_id',
                array('pw.product_id', $position, $isParent, 's.store_id'))
            ->joinLeft(
                array('dv'=>$visibilityInfo['table']),
                "dv.entity_id=pw.product_id AND dv.attribute_id={$visibilityInfo['id']} AND dv.store_id=0",
                array())
            ->joinLeft(
                array('sv'=>$visibilityInfo['table']),
                "sv.entity_id=pw.product_id AND sv.attribute_id={$visibilityInfo['id']} AND sv.store_id=s.store_id",
                array('visibility' => 'IF(sv.value_id, sv.value, dv.value)'))
            ->joinLeft(
                array('ds'=>$statusInfo['table']),
                "ds.entity_id=pw.product_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                array())
            ->joinLeft(
                array('ss'=>$statusInfo['table']),
                "ss.entity_id=pw.product_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                array())
            /**
             * Condition for anchor or root category (all products should be assigned to root)
             */
            ->where('cp.product_id IS NULL')
            ->where('IF(ss.value_id, ss.value, ds.value)=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->where('pw.product_id IN(?)', $productIds);
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
        $isAnchorAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'is_anchor');
        $info = array(
            'id'    => $isAnchorAttribute->getId() ,
            'table' => $isAnchorAttribute->getBackend()->getTable()
        );
        return $info;
    }

    /**
     * Get visibility product attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getVisibilityAttributeInfo()
    {
        $visibilityAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'visibility');
        $info = array(
            'id'    => $visibilityAttribute->getId() ,
            'table' => $visibilityAttribute->getBackend()->getTable()
        );
        return $info;
    }

    /**
     * Get status product attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getStatusAttributeInfo()
    {
        $statusAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'status');
        $info = array(
            'id'    => $statusAttribute->getId() ,
            'table' => $statusAttribute->getBackend()->getTable()
        );
        return $info;
    }

    /**
     * Rebuild all index data
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Indexer_Product
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
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
            $sql = "INSERT INTO {$idxTable}
                SELECT
                    cp.category_id, cp.product_id, cp.position, 1, {$storeId}, pv.visibility
                FROM
                    {$this->_categoryProductTable} AS cp
                    INNER JOIN {$enabledTable} AS pv ON pv.product_id=cp.product_id
                    LEFT JOIN {$anchorTable} AS ac ON ac.category_id=cp.category_id
                WHERE
                    ac.category_id IS NULL";
            $idxAdapter->query($sql);
            /**
             * Assign products not associated to any category to root category in index
             */
            $sql = "INSERT INTO {$idxTable}
                SELECT
                    {$rootId}, pv.product_id, 0, 1, {$storeId}, pv.visibility
                FROM
                    {$enabledTable} AS pv
                    LEFT JOIN {$this->_categoryProductTable} AS cp ON pv.product_id=cp.product_id
                WHERE
                    cp.product_id IS NULL";
            $idxAdapter->query($sql);

            /**
             * Prepare anchor categories products
             */
            $anchorProductsTable = $this->_getAnchorCategoriesProductsTemporaryTable();
            $idxAdapter->delete($anchorProductsTable);

            $sql = "SELECT
                    STRAIGHT_JOIN DISTINCT
                    ca.category_id, cp.product_id
                FROM {$anchorTable} AS ca
                  INNER JOIN {$this->_categoryTable} AS ce
                    ON ce.path LIKE ca.path OR ce.entity_id = ca.category_id
                  INNER JOIN {$this->_categoryProductTable} AS cp
                    ON cp.category_id = ce.entity_id
                  INNER JOIN {$enabledTable} as pv
                    ON pv.product_id = cp.product_id";
            $this->insertFromSelect($sql, $anchorProductsTable, array('category_id' , 'product_id'));
            /**
             * Add anchor categories products to index
             */
            $sql = "INSERT INTO {$idxTable}
                SELECT
                    ap.category_id, ap.product_id, cp.position,
                    IF(cp.product_id, 1, 0), {$storeId}, pv.visibility
                FROM
                    {$anchorProductsTable} AS ap
                    LEFT JOIN {$this->_categoryProductTable} AS cp
                        ON cp.category_id=ap.category_id AND cp.product_id=ap.product_id
                    INNER JOIN {$enabledTable} as pv
                        ON pv.product_id = ap.product_id";
            $idxAdapter->query($sql);
        }
        $this->syncData();

        /**
         * Clean up temporary tables
         */
        $this->clearTemporaryIndexTable();
        $idxAdapter->delete($enabledTable);
        $idxAdapter->delete($anchorTable);
        $idxAdapter->delete($anchorProductsTable);

        return $this;
    }

    /**
     * Get array with store|website|root_categry path information
     *
     * @return array
     */
    protected function _getStoresInfo()
    {
        $stores = $this->_getReadAdapter()->fetchAll("
            SELECT
                s.store_id, s.website_id, c.path AS root_path, c.entity_id AS root_id
            FROM
                {$this->getTable('core/store')} AS s,
                {$this->getTable('core/store_group')} AS sg,
                {$this->getTable('catalog/category')} AS c
            WHERE
                sg.group_id=s.group_id
                AND c.entity_id=sg.root_category_id
        ");
        return $stores;
    }

    /**
     * Create temporary table with enabled products visibility info
     *
     * @return string temporary table name
     */
    protected function _prepareEnabledProductsVisibility($websiteId, $storeId)
    {
        $statusAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'status');
        $visibilityAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'visibility');
        $statusAttributeId = $statusAttribute->getId();
        $visibilityAttributeId = $visibilityAttribute->getId();
        $statusTable = $statusAttribute->getBackend()->getTable();
        $visibilityTable = $visibilityAttribute->getBackend()->getTable();

        /**
         * Prepare temporary table
         */
        $tmpTable = $this->_getEnabledProductsTemporaryTable();
        $this->_getIndexAdapter()->delete($tmpTable);

        $sql = "SELECT
                pw.product_id AS product_id,
                IF(pvs.value_id>0, pvs.value, pvd.value) AS visibility
            FROM
                {$this->_productWebsiteTable} AS pw
                LEFT JOIN {$visibilityTable} AS pvd
                    ON pvd.entity_id=pw.product_id AND pvd.attribute_id={$visibilityAttributeId} AND pvd.store_id=0
                LEFT JOIN {$visibilityTable} AS pvs
                    ON pvs.entity_id=pw.product_id AND pvs.attribute_id={$visibilityAttributeId} AND pvs.store_id={$storeId}
                LEFT JOIN {$statusTable} AS psd
                    ON psd.entity_id=pw.product_id AND psd.attribute_id={$statusAttributeId} AND psd.store_id=0
                LEFT JOIN {$statusTable} AS pss
                    ON pss.entity_id=pw.product_id AND pss.attribute_id={$statusAttributeId} AND pss.store_id={$storeId}
            WHERE
                pw.website_id={$websiteId}
                AND IF(pss.value_id>0, pss.value, psd.value) = " . Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
        $this->insertFromSelect($sql, $tmpTable, array('product_id' , 'visibility'));
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
     * Create temporary table with list of anchor categories
     *
     * @param   int $storeId
     * @return  string temporary table name
     */
    protected function _prepareAnchorCategories($storeId, $rootPath)
    {
        $isAnchorAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'is_anchor');
        $anchorAttributeId = $isAnchorAttribute->getId();
        $anchorTable = $isAnchorAttribute->getBackend()->getTable();

        $tmpTable = $this->_getAnchorCategoriesTemporaryTable();
        $this->_getIndexAdapter()->delete($tmpTable);

        $sql = "SELECT
            ce.entity_id AS category_id,
            concat(ce.path, '/%') AS path
        FROM
            {$this->_categoryTable} as ce
            LEFT JOIN {$anchorTable} AS cad
                ON cad.entity_id=ce.entity_id AND cad.attribute_id={$anchorAttributeId} AND cad.store_id=0
            LEFT JOIN {$anchorTable} AS cas
                ON cas.entity_id=ce.entity_id AND cas.attribute_id={$anchorAttributeId} AND cas.store_id={$storeId}
        WHERE
            (IF(cas.value_id>0, cas.value, cad.value) = 1 AND ce.path LIKE '{$rootPath}/%')
            OR ce.path='{$rootPath}'";
        $this->insertFromSelect($sql, $tmpTable, array('category_id' , 'path'));
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
        return $this->getTable('catalog/category_anchor_indexer_idx');
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
