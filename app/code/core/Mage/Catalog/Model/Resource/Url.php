<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog url rewrite resource model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Url extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Stores configuration array
     *
     * @var array
     */
    protected $_stores;

    /**
     * Category attribute properties cache
     *
     * @var array
     */
    protected $_categoryAttributes          = [];

    /**
     * Product attribute properties cache
     *
     * @var array
     */
    protected $_productAttributes           = [];

    /**
     * Limit products for select
     *
     * @var int
     */
    protected $_productLimit                = 250;

    /**
     * Cache of root category children ids
     *
     * @var array
     */
    protected $_rootChildrenIds             = [];

    /**
     * Load core Url rewrite model
     *
     */
    protected function _construct()
    {
        $this->_init('core/url_rewrite', 'url_rewrite_id');
    }

    /**
     * Retrieve stores array or store model
     *
     * @param int $storeId
     * @return Mage_Core_Model_Store|Mage_Core_Model_Store[]
     */
    public function getStores($storeId = null)
    {
        if ($this->_stores === null) {
            $this->_stores = $this->_prepareStoreRootCategories(Mage::app()->getStores());
        }
        if ($storeId && isset($this->_stores[$storeId])) {
            return $this->_stores[$storeId];
        }
        return $this->_stores;
    }

    /**
     * Retrieve Category model singleton
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategoryModel()
    {
        return Mage::getSingleton('catalog/category');
    }

    /**
     * Retrieve product model singleton
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProductModel()
    {
        return Mage::getSingleton('catalog/product');
    }

    /**
     * Retrieve rewrite by idPath
     *
     * @param string $idPath
     * @param int $storeId
     * @return Varien_Object|false
     */
    public function getRewriteByIdPath($idPath, $storeId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('store_id = :store_id')
            ->where('id_path = :id_path');
        $bind = [
            'store_id' => (int) $storeId,
            'id_path'  => $idPath,
        ];
        $row = $adapter->fetchRow($select, $bind);

        if (!$row) {
            return false;
        }
        $rewrite = new Varien_Object($row);
        $rewrite->setIdFieldName($this->getIdFieldName());

        return $rewrite;
    }

    /**
     * Retrieve rewrite by requestPath
     *
     * @param string $requestPath
     * @param int $storeId
     * @return Varien_Object|false
     */
    public function getRewriteByRequestPath($requestPath, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('store_id = :store_id')
            ->where('request_path = :request_path');
        $bind = [
            'request_path'  => $requestPath,
            'store_id'      => (int) $storeId,
        ];
        $row = $adapter->fetchRow($select, $bind);

        if (!$row) {
            return false;
        }
        $rewrite = new Varien_Object($row);
        $rewrite->setIdFieldName($this->getIdFieldName());

        return $rewrite;
    }

    /**
     * Get last used increment part of rewrite request path
     *
     * @param string $prefix
     * @param string $suffix
     * @param int $storeId
     * @return int
     */
    public function getLastUsedRewriteRequestIncrement($prefix, $suffix, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $requestPathField = new Zend_Db_Expr($adapter->quoteIdentifier('request_path'));
        /** @var Mage_Eav_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('eav');
        //select increment part of request path and cast expression to integer
        $urlIncrementPartExpression = $helper
            ->getCastToIntExpression($adapter->getSubstringSql(
                $requestPathField,
                strlen($prefix) + 1,
                $adapter->getLengthSql($requestPathField) . ' - ' . strlen($prefix) . ' - ' . strlen($suffix),
            ));
        $select = $adapter->select()
            ->from($this->getMainTable(), new Zend_Db_Expr('MAX(' . $urlIncrementPartExpression . ')'))
            ->where('store_id = :store_id')
            ->where('request_path LIKE :request_path')
            ->where($adapter->prepareSqlCondition('request_path', [
                'regexp' => '^' . preg_quote($prefix) . '[0-9]*' . preg_quote($suffix) . '$',
            ]));
        $bind = [
            'store_id'            => (int) $storeId,
            'request_path'        => $prefix . '%' . $suffix,
        ];

        return (int) $adapter->fetchOne($select, $bind);
    }

    /**
     * Validate array of request paths. Return first not used path in case if validations passed
     *
     * @param array $paths
     * @param int $storeId
     * @return false | string
     */
    public function checkRequestPaths($paths, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'request_path')
            ->where('store_id = :store_id')
            ->where('request_path IN (?)', $paths);
        $data = $adapter->fetchCol($select, ['store_id' => $storeId]);
        $paths = array_diff($paths, $data);
        if (empty($paths)) {
            return false;
        }
        reset($paths);

        return current($paths);
    }

    /**
     * Prepare rewrites for condition
     *
     * @param int $storeId
     * @param int|string|array|false $categoryIds
     * @param int|array $productIds
     * @return array
     */
    public function prepareRewrites($storeId, $categoryIds = null, $productIds = null)
    {
        $rewrites   = [];
        $adapter    = $this->_getWriteAdapter();
        $select     = $adapter->select()
            ->from($this->getMainTable())
            ->where('store_id = :store_id')
            ->where('is_system = ?', 1);
        $bind = ['store_id' => $storeId];
        if ($categoryIds === null) {
            $select->where('category_id IS NULL');
        } elseif ($categoryIds) {
            $catIds = is_array($categoryIds) ? $categoryIds : [$categoryIds];

            // Check maybe we request products and root category id is within categoryIds,
            // it's a separate case because root category products are stored with NULL categoryId
            if ($productIds) {
                $addNullCategory = in_array($this->getStores($storeId)->getRootCategoryId(), $catIds);
            } else {
                $addNullCategory = false;
            }

            // Compose optimal condition
            if ($addNullCategory) {
                $select->where('category_id IN(?) OR category_id IS NULL', $catIds);
            } else {
                $select->where('category_id IN(?)', $catIds);
            }
        }

        if ($productIds === null) {
            $select->where('product_id IS NULL');
        } elseif ($productIds) {
            $select->where('product_id IN(?)', $productIds);
        }

        $rowSet = $adapter->fetchAll($select, $bind);

        foreach ($rowSet as $row) {
            $rewrite = new Varien_Object($row);
            $rewrite->setIdFieldName($this->getIdFieldName());
            $rewrites[$rewrite->getIdPath()] = $rewrite;
        }

        return $rewrites;
    }

    /**
     * Save rewrite URL
     *
     * @param array $rewriteData
     * @param Varien_Object|Mage_Core_Model_Url_Rewrite $rewrite
     * @return $this
     */
    public function saveRewrite($rewriteData, $rewrite)
    {
        $adapter = $this->_getWriteAdapter();
        try {
            $adapter->insertOnDuplicate($this->getMainTable(), $rewriteData);
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::throwException(Mage::helper('catalog')->__('An error occurred while saving the URL rewrite'));
        }

        if ($rewrite && $rewrite->getId()) {
            if ($rewriteData['request_path'] != $rewrite->getRequestPath()) {
                // Update existing rewrites history and avoid chain redirects
                $where = ['target_path = ?' => $rewrite->getRequestPath()];
                if ($rewrite->getStoreId()) {
                    $where['store_id = ?'] = (int) $rewrite->getStoreId();
                }
                $adapter->update(
                    $this->getMainTable(),
                    ['target_path' => $rewriteData['request_path']],
                    $where,
                );
            }
        }
        unset($rewriteData);

        return $this;
    }

    /**
     * Saves rewrite history
     *
     * @param array $rewriteData
     * @return $this
     */
    public function saveRewriteHistory($rewriteData)
    {
        $rewriteData = new Varien_Object($rewriteData);
        // check if rewrite exists with save request_path
        $rewrite = $this->getRewriteByRequestPath($rewriteData->getRequestPath(), $rewriteData->getStoreId());
        if ($rewrite === false) {
            // create permanent redirect
            $this->_getWriteAdapter()->insert($this->getMainTable(), $rewriteData->getData());
        }

        return $this;
    }

    /**
     * Save category attribute
     *
     * @param string $attributeCode
     * @return $this
     */
    public function saveCategoryAttribute(Varien_Object $category, $attributeCode)
    {
        $adapter = $this->_getWriteAdapter();
        if (!isset($this->_categoryAttributes[$attributeCode])) {
            $attribute = $this->getCategoryModel()->getResource()->getAttribute($attributeCode);

            $this->_categoryAttributes[$attributeCode] = [
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal(),
            ];
            unset($attribute);
        }

        $attributeTable = $this->_categoryAttributes[$attributeCode]['table'];

        $attributeData = [
            'entity_type_id'    => $this->_categoryAttributes[$attributeCode]['entity_type_id'],
            'attribute_id'      => $this->_categoryAttributes[$attributeCode]['attribute_id'],
            'store_id'          => $category->getStoreId(),
            'entity_id'         => $category->getId(),
            'value'             => $category->getData($attributeCode),
        ];

        if ($this->_categoryAttributes[$attributeCode]['is_global'] || $category->getStoreId() == 0) {
            $attributeData['store_id'] = 0;
        }

        $select = $adapter->select()
            ->from($attributeTable)
            ->where('entity_type_id = ?', (int) $attributeData['entity_type_id'])
            ->where('attribute_id = ?', (int) $attributeData['attribute_id'])
            ->where('store_id = ?', (int) $attributeData['store_id'])
            ->where('entity_id = ?', (int) $attributeData['entity_id']);

        $row = $adapter->fetchRow($select);
        if ($row) {
            $whereCond = ['value_id = ?' => $row['value_id']];
            $adapter->update($attributeTable, $attributeData, $whereCond);
        } else {
            $adapter->insert($attributeTable, $attributeData);
        }

        if ($attributeData['store_id'] != 0) {
            $attributeData['store_id'] = 0;
            $select = $adapter->select()
                ->from($attributeTable)
                ->where('entity_type_id = ?', (int) $attributeData['entity_type_id'])
                ->where('attribute_id = ?', (int) $attributeData['attribute_id'])
                ->where('store_id = ?', (int) $attributeData['store_id'])
                ->where('entity_id = ?', (int) $attributeData['entity_id']);

            $row = $adapter->fetchRow($select);
            if ($row) {
                $whereCond = ['value_id = ?' => $row['value_id']];
                $adapter->update($attributeTable, $attributeData, $whereCond);
            } else {
                $adapter->insert($attributeTable, $attributeData);
            }
        }
        unset($attributeData);

        return $this;
    }

    /**
     * Retrieve category attributes
     *
     * @param string $attributeCode
     * @param int|array $categoryIds
     * @param int $storeId
     * @return array
     */
    protected function _getCategoryAttribute($attributeCode, $categoryIds, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        if (!isset($this->_categoryAttributes[$attributeCode])) {
            $attribute = $this->getCategoryModel()->getResource()->getAttribute($attributeCode);

            $this->_categoryAttributes[$attributeCode] = [
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal(),
                'is_static'      => $attribute->isStatic(),
            ];
            unset($attribute);
        }

        if (!is_array($categoryIds)) {
            $categoryIds = [$categoryIds];
        }

        $attributeTable = $this->_categoryAttributes[$attributeCode]['table'];
        $select         = $adapter->select();
        $bind           = [];
        if ($this->_categoryAttributes[$attributeCode]['is_static']) {
            $select
                ->from(
                    $this->getTable('catalog/category'),
                    ['value' => $attributeCode, 'entity_id' => 'entity_id'],
                )
                ->where('entity_id IN(?)', $categoryIds);
        } elseif ($this->_categoryAttributes[$attributeCode]['is_global'] || $storeId == 0) {
            $select
                ->from($attributeTable, ['entity_id', 'value'])
                ->where('attribute_id = :attribute_id')
                ->where('store_id = ?', 0)
                ->where('entity_id IN(?)', $categoryIds);
            $bind['attribute_id'] = $this->_categoryAttributes[$attributeCode]['attribute_id'];
        } else {
            $valueExpr = $adapter->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');
            $select
                ->from(
                    ['t1' => $attributeTable],
                    ['entity_id', 'value' => $valueExpr],
                )
                ->joinLeft(
                    ['t2' => $attributeTable],
                    't1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id = :store_id',
                    [],
                )
                ->where('t1.store_id = ?', 0)
                ->where('t1.attribute_id = :attribute_id')
                ->where('t1.entity_id IN(?)', $categoryIds);

            $bind['attribute_id'] = $this->_categoryAttributes[$attributeCode]['attribute_id'];
            $bind['store_id']     = $storeId;
        }

        $rowSet = $adapter->fetchAll($select, $bind);

        $attributes = [];
        foreach ($rowSet as $row) {
            $attributes[$row['entity_id']] = $row['value'];
        }
        unset($rowSet);
        foreach ($categoryIds as $categoryId) {
            if (!isset($attributes[$categoryId])) {
                $attributes[$categoryId] = null;
            }
        }

        return $attributes;
    }

    /**
     * Save product attribute
     *
     * @param string $attributeCode
     * @return $this
     */
    public function saveProductAttribute(Varien_Object $product, $attributeCode)
    {
        $adapter = $this->_getWriteAdapter();
        if (!isset($this->_productAttributes[$attributeCode])) {
            $attribute = $this->getProductModel()->getResource()->getAttribute($attributeCode);

            $this->_productAttributes[$attributeCode] = [
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal(),
            ];
            unset($attribute);
        }

        $attributeTable = $this->_productAttributes[$attributeCode]['table'];

        $attributeData = [
            'entity_type_id'    => $this->_productAttributes[$attributeCode]['entity_type_id'],
            'attribute_id'      => $this->_productAttributes[$attributeCode]['attribute_id'],
            'store_id'          => $product->getStoreId(),
            'entity_id'         => $product->getId(),
            'value'             => $product->getData($attributeCode),
        ];

        if ($this->_productAttributes[$attributeCode]['is_global'] || $product->getStoreId() == 0) {
            $attributeData['store_id'] = 0;
        }

        $select = $adapter->select()
            ->from($attributeTable)
            ->where('entity_type_id = ?', (int) $attributeData['entity_type_id'])
            ->where('attribute_id = ?', (int) $attributeData['attribute_id'])
            ->where('store_id = ?', (int) $attributeData['store_id'])
            ->where('entity_id = ?', (int) $attributeData['entity_id']);

        $row = $adapter->fetchRow($select);
        if ($row) {
            $whereCond = ['value_id = ?' => $row['value_id']];
            $adapter->update($attributeTable, $attributeData, $whereCond);
        } else {
            $adapter->insert($attributeTable, $attributeData);
        }

        if ($attributeData['store_id'] != 0) {
            $attributeData['store_id'] = 0;
            $select = $adapter->select()
                ->from($attributeTable)
                ->where('entity_type_id = ?', (int) $attributeData['entity_type_id'])
                ->where('attribute_id = ?', (int) $attributeData['attribute_id'])
                ->where('store_id = ?', (int) $attributeData['store_id'])
                ->where('entity_id = ?', (int) $attributeData['entity_id']);

            $row = $adapter->fetchRow($select);
            if ($row) {
                $whereCond = ['value_id = ?' => $row['value_id']];
                $adapter->update($attributeTable, $attributeData, $whereCond);
            } else {
                $adapter->insert($attributeTable, $attributeData);
            }
        }
        unset($attributeData);

        return $this;
    }

    /**
     * Retrieve product attribute
     *
     * @param string $attributeCode
     * @param int|array $productIds
     * @param int|string $storeId
     * @return array
     */
    public function _getProductAttribute($attributeCode, $productIds, $storeId)
    {
        $adapter = $this->_getReadAdapter();
        if (!isset($this->_productAttributes[$attributeCode])) {
            $attribute = $this->getProductModel()->getResource()->getAttribute($attributeCode);

            $this->_productAttributes[$attributeCode] = [
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal(),
            ];
            unset($attribute);
        }

        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }
        $bind = ['attribute_id' => $this->_productAttributes[$attributeCode]['attribute_id']];
        $select = $adapter->select();
        $attributeTable = $this->_productAttributes[$attributeCode]['table'];
        if ($this->_productAttributes[$attributeCode]['is_global'] || $storeId == 0) {
            $select
                ->from($attributeTable, ['entity_id', 'value'])
                ->where('attribute_id = :attribute_id')
                ->where('store_id = ?', 0)
                ->where('entity_id IN(?)', $productIds);
        } else {
            $valueExpr = $adapter->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');
            $select
                ->from(
                    ['t1' => $attributeTable],
                    ['entity_id', 'value' => $valueExpr],
                )
                ->joinLeft(
                    ['t2' => $attributeTable],
                    't1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id=:store_id',
                    [],
                )
                ->where('t1.store_id = ?', 0)
                ->where('t1.attribute_id = :attribute_id')
                ->where('t1.entity_id IN(?)', $productIds);
            $bind['store_id'] = $storeId;
        }

        $rowSet = $adapter->fetchAll($select, $bind);

        $attributes = [];
        foreach ($rowSet as $row) {
            $attributes[$row['entity_id']] = $row['value'];
        }
        unset($rowSet);
        foreach ($productIds as $productId) {
            if (!isset($attributes[$productId])) {
                $attributes[$productId] = null;
            }
        }

        return $attributes;
    }

    /**
     * Prepare category parentId
     *
     * @return $this
     */
    protected function _prepareCategoryParentId(Varien_Object $category)
    {
        if ($category->getPath() != $category->getId()) {
            $split = explode('/', $category->getPath());
            $category->setParentId($split[(count($split) - 2)]);
        } else {
            $category->setParentId(0);
        }
        return $this;
    }

    /**
     * Prepare stores root categories
     *
     * @param Mage_Core_Model_Store[] $stores
     * @return Mage_Core_Model_Store[]
     */
    protected function _prepareStoreRootCategories($stores)
    {
        $rootCategoryIds = [];
        foreach ($stores as $store) {
            $rootCategoryId = $store->getRootCategoryId();
            $rootCategoryIds[$rootCategoryId] = $rootCategoryId;
        }
        if ($rootCategoryIds) {
            $categories = $this->_getCategories($rootCategoryIds);
        }
        foreach ($stores as $store) {
            $rootCategoryId = $store->getRootCategoryId();
            if (isset($categories[$rootCategoryId])) {
                $store->setRootCategoryPath($categories[$rootCategoryId]->getPath());
                $store->setRootCategory($categories[$rootCategoryId]);
            } else {
                unset($stores[$store->getId()]);
            }
        }
        return $stores;
    }

    /**
     * Retrieve categories objects
     * Either $categoryIds or $path (with ending slash) must be specified
     *
     * @param int|array|null $categoryIds
     * @param int $storeId
     * @param string $path
     * @return array
     */
    protected function _getCategories($categoryIds, $storeId = null, $path = null)
    {
        $isActiveAttribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Category::ENTITY, 'is_active');
        $categories        = [];
        $adapter           = $this->_getReadAdapter();

        if (!is_array($categoryIds)) {
            $categoryIds = [$categoryIds];
        }
        $isActiveExpr = $adapter->getCheckSql('c.value_id > 0', 'c.value', 'd.value');
        $select = $adapter->select()
            ->from(['main_table' => $this->getTable('catalog/category')], [
                'main_table.entity_id',
                'main_table.parent_id',
                'main_table.level',
                'is_active' => $isActiveExpr,
                'main_table.path']);

        // Prepare variables for checking whether categories belong to store
        if ($path === null) {
            $select->where('main_table.entity_id IN(?)', $categoryIds);
        } else {
            // Ensure that path ends with '/', otherwise we can get wrong results - e.g. $path = '1/2' will get '1/20'
            if (!str_ends_with($path, '/')) {
                $path .= '/';
            }

            $select
                ->where('main_table.path LIKE ?', $path . '%')
                ->order('main_table.path');
        }
        $table = $this->getTable(['catalog/category', 'int']);
        $select->joinLeft(
            ['d' => $table],
            'd.attribute_id = :attribute_id AND d.store_id = 0 AND d.entity_id = main_table.entity_id',
            [],
        )
        ->joinLeft(
            ['c' => $table],
            'c.attribute_id = :attribute_id AND c.store_id = :store_id AND c.entity_id = main_table.entity_id',
            [],
        );

        if ($storeId !== null) {
            $rootCategoryPath = $this->getStores($storeId)->getRootCategoryPath();
            $rootCategoryPathLength = strlen($rootCategoryPath);
        }
        $bind = [
            'attribute_id' => (int) $isActiveAttribute->getId(),
            'store_id'     => (int) $storeId,
        ];

        $rowSet = $adapter->fetchAll($select, $bind);
        foreach ($rowSet as $row) {
            if ($storeId !== null) {
                // Check the category to be either store's root or its descendant
                // First - check that category's start is the same as root category
                if (substr($row['path'], 0, $rootCategoryPathLength) != $rootCategoryPath) {
                    continue;
                }
                // Second - check non-root category - that it's really a descendant, not a simple string match
                if ((strlen($row['path']) > $rootCategoryPathLength)
                    && ($row['path'][$rootCategoryPathLength] !== '/')
                ) {
                    continue;
                }
            }

            $category = new Varien_Object($row);
            $category->setIdFieldName('entity_id');
            $category->setStoreId($storeId);
            $this->_prepareCategoryParentId($category);

            $categories[$category->getId()] = $category;
        }
        unset($rowSet);

        if (isset($category) && $storeId !== null && $categories) {
            foreach (['name', 'url_key', 'url_path'] as $attributeCode) {
                $attributes = $this->_getCategoryAttribute(
                    $attributeCode,
                    array_keys($categories),
                    $category->getStoreId(),
                );
                foreach ($attributes as $categoryId => $attributeValue) {
                    $categories[$categoryId]->setData($attributeCode, $attributeValue);
                }
            }
        }

        return $categories;
    }

    /**
     * Retrieve category data object
     *
     * @param int $categoryId
     * @param int $storeId
     * @return Varien_Object|false
     */
    public function getCategory($categoryId, $storeId)
    {
        if (!$categoryId || !$storeId) {
            return false;
        }

        $categories = $this->_getCategories($categoryId, $storeId);
        return $categories[$categoryId] ?? false;
    }

    /**
     * Retrieve categories data objects by their ids. Return only categories that belong to specified store.
     *
     * @param int|array $categoryIds
     * @param int $storeId
     * @return Mage_Catalog_Model_Category[]|false
     */
    public function getCategories($categoryIds, $storeId)
    {
        if (!$categoryIds || !$storeId) {
            return false;
        }

        return $this->_getCategories($categoryIds, $storeId);
    }

    /**
     * Retrieve category children data objects
     *
     * @return Varien_Object
     */
    public function loadCategoryChilds(Varien_Object $category)
    {
        if ($category->getId() === null || $category->getStoreId() === null) {
            return $category;
        }

        $categories = $this->_getCategories(null, $category->getStoreId(), $category->getPath() . '/');
        $category->setChilds([]);
        foreach ($categories as $child) {
            if (!is_array($child->getChilds())) {
                $child->setChilds([]);
            }
            if ($child->getParentId() == $category->getId()) {
                $category->setChilds($category->getChilds() + [$child->getId() => $child]);
            } elseif (isset($categories[$child->getParentId()])) {
                if (!is_array($categories[$child->getParentId()]->getChilds())) {
                    $categories[$child->getParentId()]->setChilds([]);
                }
                $categories[$child->getParentId()]->setChilds(
                    $categories[$child->getParentId()]->getChilds() + [$child->getId() => $child],
                );
            }
        }
        $category->setAllChilds($categories);

        return $category;
    }

    /**
     * Retrieves all children ids of root category tree
     * Actually this routine can be used to get children ids of any category, not only root.
     * But as far as result is cached in memory, it's not recommended to do so.
     *
     * @param int $categoryId
     * @param string $categoryPath
     * @param bool $includeStart
     * @return Varien_Object
     */
    public function getRootChildrenIds($categoryId, $categoryPath, $includeStart = true)
    {
        if (!isset($this->_rootChildrenIds[$categoryId])) {
            // Select all descedant category ids
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select()
                ->from([$this->getTable('catalog/category')], ['entity_id'])
                ->where('path LIKE ?', $categoryPath . '/%');

            $categoryIds = [];
            $rowSet = $adapter->fetchAll($select);
            foreach ($rowSet as $row) {
                $categoryIds[$row['entity_id']] = $row['entity_id'];
            }
            $this->_rootChildrenIds[$categoryId] = $categoryIds;
        }

        $categoryIds = $this->_rootChildrenIds[$categoryId];
        if ($includeStart) {
            $categoryIds[$categoryId] = $categoryId;
        }
        return $categoryIds;
    }

    /**
     * Retrieve category parent path
     *
     * @return string
     */
    public function getCategoryParentPath(Varien_Object $category)
    {
        $store = Mage::app()->getStore($category->getStoreId());

        if ($category->getId() == $store->getRootCategoryId()) {
            return '';
        } elseif ($category->getParentId() == 1 || $category->getParentId() == $store->getRootCategoryId()) {
            return '';
        }

        $parentCategory = $this->getCategory($category->getParentId(), $store->getId());
        return $parentCategory->getUrlPath() . '/';
    }

    /**
     * Retrieve product ids by category
     *
     * @param Varien_Object|int $category
     * @return array
     */
    public function getProductIdsByCategory($category)
    {
        if ($category instanceof Varien_Object) {
            $categoryId = $category->getId();
        } else {
            $categoryId = $category;
        }
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('catalog/category_product'), ['product_id'])
            ->where('category_id = :category_id')
            ->order('product_id');
        $bind = ['category_id' => $categoryId];

        return $adapter->fetchCol($select, $bind);
    }

    /**
     * Retrieve Product data objects
     *
     * @param int|array|null $productIds
     * @param int $storeId
     * @param int $entityId
     * @param int $lastEntityId
     * @return array
     */
    protected function _getProducts($productIds, $storeId, $entityId, &$lastEntityId)
    {
        $products   = [];
        $websiteId  = Mage::app()->getStore($storeId)->getWebsiteId();
        $adapter    = $this->_getReadAdapter();
        if ($productIds !== null) {
            if (!is_array($productIds)) {
                $productIds = [$productIds];
            }
        }
        $bind = [
            'website_id' => (int) $websiteId,
            'entity_id'  => (int) $entityId,
        ];
        $select = $adapter->select()
            ->useStraightJoin(true)
            ->from(['e' => $this->getTable('catalog/product')], ['entity_id'])
            ->join(
                ['w' => $this->getTable('catalog/product_website')],
                'e.entity_id = w.product_id AND w.website_id = :website_id',
                [],
            )
            ->where('e.entity_id > :entity_id')
            ->order('e.entity_id')
            ->limit($this->_productLimit);
        if ($productIds !== null) {
            $select->where('e.entity_id IN(?)', $productIds);
        }

        $rowSet = $adapter->fetchAll($select, $bind);
        foreach ($rowSet as $row) {
            $product = new Varien_Object($row);
            $product->setIdFieldName('entity_id');
            $product->setCategoryIds([]);
            $product->setStoreId($storeId);
            $products[$product->getId()] = $product;
            $lastEntityId = $product->getId();
        }

        unset($rowSet);

        if ($products) {
            $select = $adapter->select()
                ->from(
                    $this->getTable('catalog/category_product'),
                    ['product_id', 'category_id'],
                )
                ->where('product_id IN(?)', array_keys($products));
            $categories = $adapter->fetchAll($select);
            foreach ($categories as $category) {
                $productId = $category['product_id'];
                $categoryIds = $products[$productId]->getCategoryIds();
                $categoryIds[] = $category['category_id'];
                $products[$productId]->setCategoryIds($categoryIds);
            }

            foreach (['name', 'url_key', 'url_path'] as $attributeCode) {
                $attributes = $this->_getProductAttribute($attributeCode, array_keys($products), $storeId);
                foreach ($attributes as $productId => $attributeValue) {
                    $products[$productId]->setData($attributeCode, $attributeValue);
                }
            }
        }

        return $products;
    }

    /**
     * Retrieve Product data object
     *
     * @param int $productId
     * @param int $storeId
     * @return Varien_Object|false
     */
    public function getProduct($productId, $storeId)
    {
        $entityId = 0;
        $products = $this->_getProducts($productId, $storeId, 0, $entityId);
        return $products[$productId] ?? false;
    }

    /**
     * Retrieve Product data obects for store
     *
     * @param int $storeId
     * @param int $lastEntityId
     * @return Mage_Catalog_Model_Product[]
     */
    public function getProductsByStore($storeId, &$lastEntityId)
    {
        return $this->_getProducts(null, $storeId, $lastEntityId, $lastEntityId);
    }

    /**
     * Retrieve Product data objects in category
     *
     * @param int $lastEntityId
     * @return array
     */
    public function getProductsByCategory(Varien_Object $category, &$lastEntityId)
    {
        $productIds = $this->getProductIdsByCategory($category);
        if (!$productIds) {
            return [];
        }
        return $this->_getProducts($productIds, $category->getStoreId(), $lastEntityId, $lastEntityId);
    }

    /**
     * Find and remove unused products rewrites - a case when products were moved away from the category
     * (either to other category or deleted), so rewrite "category_id-product_id" is invalid
     *
     * @param int $storeId
     * @return $this
     */
    public function clearCategoryProduct($storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from(['tur' => $this->getMainTable()], $this->getIdFieldName())
            ->joinLeft(
                ['tcp' => $this->getTable('catalog/category_product')],
                'tur.category_id = tcp.category_id AND tur.product_id = tcp.product_id',
                [],
            )
            ->where('tur.store_id = :store_id')
            ->where('tur.category_id IS NOT NULL')
            ->where('tur.product_id IS NOT NULL')
            ->where('tcp.category_id IS NULL');
        $rewriteIds = $adapter->fetchCol($select, ['store_id' => $storeId]);
        if ($rewriteIds) {
            $where = [$this->getIdFieldName() . ' IN(?)' => $rewriteIds];
            $adapter->delete($this->getMainTable(), $where);
        }

        return $this;
    }

    /**
     * Remove unused rewrites for product - called after we created all needed rewrites for product and know the categories
     * where the product is contained ($excludeCategoryIds), so we can remove all invalid product rewrites that have other category ids
     *
     * Notice: this routine is not identical to clearCategoryProduct(), because after checking all categories this one removes rewrites
     * for product still contained within categories.
     *
     * @param int $productId Product entity Id
     * @param int $storeId Store Id for rewrites
     * @param array $excludeCategoryIds Array of category Ids that should be skipped
     * @return $this
     */
    public function clearProductRewrites($productId, $storeId, $excludeCategoryIds = [])
    {
        $where = [
            'product_id = ?' => $productId,
            'store_id = ?' => $storeId,
        ];

        if (!empty($excludeCategoryIds)) {
            $where['category_id NOT IN (?)'] = $excludeCategoryIds;
            // If there's at least one category to skip, also skip root category, because product belongs to website
            $where[] = 'category_id IS NOT NULL';
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }

    /**
     * Finds and deletes all old category and category/product rewrites for store
     * left from the times when categories/products belonged to store
     *
     * @param int $storeId
     * @return $this
     */
    public function clearStoreCategoriesInvalidRewrites($storeId)
    {
        // Form a list of all current store categories ids
        $store          = $this->getStores($storeId);
        if (!$store instanceof Mage_Core_Model_Store) {
            return $this;
        }

        $rootCategoryId = $store->getRootCategoryId();
        if (!$rootCategoryId) {
            return $this;
        }
        $categoryIds = $this->getRootChildrenIds($rootCategoryId, $store->getRootCategoryPath());

        // Remove all store catalog rewrites that are for some category or category/product not within store categories
        $where   = [
            'store_id = ?' => $storeId,
            'category_id IS NOT NULL', // For sure check that it's a catalog rewrite
            'category_id NOT IN (?)' => $categoryIds,
        ];

        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }

    /**
     * Finds and deletes product rewrites (that are not assigned to any category) for store
     * left from the times when product was assigned to this store's website and now is not assigned
     *
     * Notice: this routine is different from clearProductRewrites() and clearCategoryProduct() because
     * it handles direct rewrites to product without defined category (category_id IS NULL) whilst that routines
     * handle only product rewrites within categories
     *
     * @param int $storeId
     * @param int|array|null $productId
     * @return $this
     */
    public function clearStoreProductsInvalidRewrites($storeId, $productId = null)
    {
        $store   = $this->getStores($storeId);
        if (!$store instanceof Mage_Core_Model_Store) {
            return $this;
        }

        $adapter = $this->_getReadAdapter();
        $bind    = [
            'website_id' => (int) $store->getWebsiteId(),
            'store_id'   => (int) $storeId,
        ];
        $select = $adapter->select()
            ->from(['rewrite' => $this->getMainTable()], $this->getIdFieldName())
            ->joinLeft(
                ['website' => $this->getTable('catalog/product_website')],
                'rewrite.product_id = website.product_id AND website.website_id = :website_id',
                [],
            )->where('rewrite.store_id = :store_id')
            ->where('rewrite.category_id IS NULL');
        if ($productId) {
            $select->where('rewrite.product_id IN (?)', $productId);
        } else {
            $select->where('rewrite.product_id IS NOT NULL');
        }
        $select->where('website.website_id IS NULL');

        $rewriteIds = $adapter->fetchCol($select, $bind);
        if ($rewriteIds) {
            $where = [$this->getIdFieldName() . ' IN(?)' => $rewriteIds];
            $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        }

        return $this;
    }

    /**
     * Finds and deletes old rewrites for store
     * a) category rewrites left from the times when store had some other root category
     * b) product rewrites left from products that once belonged to this site, but then deleted or just removed from website
     *
     * @param int $storeId
     * @return $this
     */
    public function clearStoreInvalidRewrites($storeId)
    {
        $this->clearStoreCategoriesInvalidRewrites($storeId);
        $this->clearStoreProductsInvalidRewrites($storeId);
        return $this;
    }

    /**
     * Delete rewrites for associated to category products
     *
     * @param int $categoryId
     * @param array $productIds
     * @return $this
     */
    public function deleteCategoryProductRewrites($categoryId, $productIds)
    {
        $this->deleteCategoryProductStoreRewrites($categoryId, $productIds);
        return $this;
    }

    /**
     * Delete URL rewrites for category products of specific store
     *
     * @param int $categoryId
     * @param array|int|null $productIds
     * @param null|int $storeId
     * @return $this
     */
    public function deleteCategoryProductStoreRewrites($categoryId, $productIds = null, $storeId = null)
    {
        // Notice that we don't include category_id = NULL in case of root category,
        // because product removed from all categories but assigned to store's website is still
        // assumed to be in root cat. Unassigned products must be removed by other routine.
        $condition = ['category_id = ?' => $categoryId];
        if (empty($productIds)) {
            $condition[] = 'product_id IS NOT NULL';
        } else {
            $condition['product_id IN (?)'] = $productIds;
        }

        if ($storeId !== null) {
            $condition['store_id IN(?)'] = $storeId;
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }

    /**
     * Retrieve rewrites and visibility by store
     * Input array format:
     * product_id as key and store_id as value
     * Output array format (product_id as key)
     * store_id     int; store id
     * visibility   int; visibility for store
     * url_rewrite  string; rewrite URL for store
     *
     * @return array
     */
    public function getRewriteByProductStore(array $products)
    {
        $result = [];

        if (empty($products)) {
            return $result;
        }
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(
                ['i' => $this->getTable('catalog/category_product_index')],
                ['product_id', 'store_id', 'visibility'],
            )
            ->joinLeft(
                ['r' => $this->getMainTable()],
                'i.product_id = r.product_id AND i.store_id=r.store_id AND r.category_id IS NULL',
                ['request_path'],
            );

        $bind = [];
        foreach ($products as $productId => $storeId) {
            $catId = Mage::app()->getStore($storeId)->getRootCategoryId();
            $productBind = 'product_id' . $productId;
            $storeBind   = 'store_id' . $storeId;
            $catBind     = 'category_id' . $catId;
            $cond  = '(' . implode(' AND ', [
                'i.product_id = :' . $productBind,
                'i.store_id = :' . $storeBind,
                'i.category_id = :' . $catBind,
            ]) . ')';
            $bind[$productBind] = $productId;
            $bind[$storeBind]   = $storeId;
            $bind[$catBind]     = $catId;
            $select->orWhere($cond);
        }

        $rowSet = $adapter->fetchAll($select, $bind);
        foreach ($rowSet as $row) {
            $result[$row['product_id']] = [
                'store_id'      => $row['store_id'],
                'visibility'    => $row['visibility'],
                'url_rewrite'   => $row['request_path'],
            ];
        }

        return $result;
    }

    /**
     * Find and return final id path by request path
     * Needed for permanent redirect old URLs.
     *
     * @param string $requestPath
     * @param int $storeId
     * @param array $checkedPaths internal variable to prevent infinite loops.
     * @return string | bool
     */
    public function findFinalTargetPath($requestPath, $storeId, &$checkedPaths = [])
    {
        if (in_array($requestPath, $checkedPaths)) {
            return false;
        }

        $checkedPaths[] = $requestPath;

        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable(), ['target_path', 'id_path'])
            ->where('store_id = ?', $storeId)
            ->where('request_path = ?', $requestPath);

        if ($row = $this->_getWriteAdapter()->fetchRow($select)) {
            $idPath = $this->findFinalTargetPath($row['target_path'], $storeId, $checkedPaths);
            if (!$idPath) {
                return $row['id_path'];
            } else {
                return $idPath;
            }
        }

        return false;
    }

    /**
     * Delete rewrite path record from the database.
     *
     * @param string $requestPath
     * @param int $storeId
     */
    public function deleteRewrite($requestPath, $storeId)
    {
        $this->deleteRewriteRecord($requestPath, $storeId);
    }

    /**
     * Delete rewrite path record from the database with RP checking.
     *
     * @param string $requestPath
     * @param int $storeId
     * @param bool $rp whether check rewrite option to be "Redirect = Permanent"
     */
    public function deleteRewriteRecord($requestPath, $storeId, $rp = false)
    {
        $conditions =  [
            'store_id = ?' => $storeId,
            'request_path = ?' => $requestPath,
        ];
        if ($rp) {
            $conditions['options = ?'] = 'RP';
        }
        $this->_getWriteAdapter()->delete($this->getMainTable(), $conditions);
    }
}
