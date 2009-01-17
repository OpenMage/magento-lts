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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog url rewrite resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Url extends Mage_Core_Model_Mysql4_Abstract
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
    protected $_categoryAttributes = array();

    /**
     * Product attribute properties cache
     *
     * @var array
     */
    protected $_productAttributes = array();

    /**
     * Limit products for select
     *
     * @var int
     */
    protected $_productLimit = 250;

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
     * @return Mage_Core_Model_Store|array
     */
    public function getStores($storeId = null)
    {
        if (is_null($this->_stores)) {
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
     * @return Varien_Object
     */
    public function getRewriteByIdPath($idPath, $storeId)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable())
            ->where('store_id=?', $storeId)
            ->where('id_path=?', $idPath);
        $row = $this->_getWriteAdapter()->fetchRow($select);

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
     * @return Varien_Object
     */
    public function getRewriteByRequestPath($requestPath, $storeId)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable())
            ->where('store_id=?', $storeId)
            ->where('request_path=?', $requestPath);
        $row = $this->_getWriteAdapter()->fetchRow($select);

        if (!$row) {
            return false;
        }
        $rewrite = new Varien_Object($row);
        $rewrite->setIdFieldName($this->getIdFieldName());
        return $rewrite;
    }

    public function prepareRewrites($storeId, $categoryIds = null, $productIds = null)
    {
        $rewrites = array();
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable())
            ->where('store_id=?', $storeId)
            ->where('is_system=?', 1);

        if (is_null($categoryIds)) {
            $select->where('category_id IS NULL');
        }
        elseif ($categoryIds) {
            $select->where('category_id IN(?)', $categoryIds);
        }
        if (is_null($productIds)) {
            $select->where('product_id IS NULL');
        }
        elseif ($productIds) {
            $select->where('product_id IN(?)', $productIds);
        }

        $query = $this->_getWriteAdapter()->query((string)$select);

        while ($row = $query->fetch()) {
            $rewrite = new Varien_Object($row);
            $rewrite->setIdFieldName($this->getIdFieldName());
            $rewrites[$rewrite->getIdPath()] = $rewrite;
        }
        unset($query);

        return $rewrites;
    }

    /**
     * Save rewrite url
     *
     * @param array $rewriteData
     * @param Varien_Object $rewriteObject
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Url
     */
    public function saveRewrite($rewriteData, $rewrite)
    {
        if ($rewrite && $rewrite->getId()) {
            if ($rewriteData['request_path'] != $rewrite->getRequestPath()) {
                $where = $this->_getWriteAdapter()->quoteInto($this->getIdFieldName() . '=?', $rewrite->getId());
                $this->_getWriteAdapter()->update(
                    $this->getMainTable(),
                    $rewriteData,
                    $where
                );
            }
        }
        else {
            try {
                $this->_getWriteAdapter()->insert($this->getMainTable(), $rewriteData);
            }
            catch (Exception $e) {
                var_dump($rewriteData);
                echo $e;
                die();
            }
        }
        unset($rewriteData);
        return $this;
    }

    /**
     * Save category attribute
     *
     * @param Varien_Object $category
     * @param string $attributeCode
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Url
     */
    public function saveCategoryAttribute(Varien_Object $category, $attributeCode)
    {
        if (!isset($this->_categoryAttributes[$attributeCode])) {
            $attribute = $this->getCategoryModel()->getResource()->getAttribute($attributeCode);

            $this->_categoryAttributes[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal()
            );
            unset($attribute);
        }

        $attributeTable = $this->_categoryAttributes[$attributeCode]['table'];

        $attributeData = array(
            'entity_type_id'    => $this->_categoryAttributes[$attributeCode]['entity_type_id'],
            'attribute_id'      => $this->_categoryAttributes[$attributeCode]['attribute_id'],
            'store_id'          => $category->getStoreId(),
            'entity_id'         => $category->getId(),
            'value'             => $category->getData($attributeCode)
        );

        if ($this->_categoryAttributes[$attributeCode]['is_global'] || $category->getStoreId() == 0) {
            $attributeData['store_id'] = 0;
        }

        $select = $this->_getWriteAdapter()->select()
            ->from($attributeTable)
            ->where('entity_type_id=?', $attributeData['entity_type_id'])
            ->where('attribute_id=?', $attributeData['attribute_id'])
            ->where('store_id=?', $attributeData['store_id'])
            ->where('entity_id=?', $attributeData['entity_id']);
        if ($row = $this->_getWriteAdapter()->fetchRow($select)) {
            $whereCond = $this->_getWriteAdapter()->quoteInto('value_id=?', $row['value_id']);
            $this->_getWriteAdapter()->update($attributeTable, $attributeData, $whereCond);
        }
        else {
            $this->_getWriteAdapter()->insert($attributeTable, $attributeData);
        }

        if ($attributeData['store_id'] != 0) {
            $attributeData['store_id'] = 0;
            $select = $this->_getWriteAdapter()->select()
                ->from($attributeTable)
                ->where('entity_type_id=?', $attributeData['entity_type_id'])
                ->where('attribute_id=?', $attributeData['attribute_id'])
                ->where('store_id=?', $attributeData['store_id'])
                ->where('entity_id=?', $attributeData['entity_id']);
            if ($row = $this->_getWriteAdapter()->fetchRow($select)) {
                $whereCond = $this->_getWriteAdapter()->quoteInto('value_id=?', $row['value_id']);
                $this->_getWriteAdapter()->update($attributeTable, $attributeData, $whereCond);
            }
            else {
                $this->_getWriteAdapter()->insert($attributeTable, $attributeData);
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
        if (!isset($this->_categoryAttributes[$attributeCode])) {
            $attribute = $this->getCategoryModel()->getResource()->getAttribute($attributeCode);

            $this->_categoryAttributes[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal()
            );
            unset($attribute);
        }

        if (!is_array($categoryIds)) {
            $categoryIds = array($categoryIds);
        }

        $attributeTable = $this->_categoryAttributes[$attributeCode]['table'];
        if ($this->_categoryAttributes[$attributeCode]['is_global'] || $storeId == 0) {
            $select = $this->_getWriteAdapter()->select()
                ->from($attributeTable, array('entity_id', 'value'))
                ->where('attribute_id = ?', $this->_categoryAttributes[$attributeCode]['attribute_id'])
                ->where('store_id=?', 0)
                ->where('entity_id IN(?)', $categoryIds);
        }
        else {
            $select = $this->_getWriteAdapter()->select()
                ->from(array('t1'=>$attributeTable), array('entity_id', 'IFNULL(t2.value, t1.value) as value'))
                ->joinLeft(
                    array('t2'=>$attributeTable),
                    $this->_getWriteAdapter()->quoteInto('t1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id=?', $storeId),
                    array()
                )
                ->where('t1.store_id = ?', 0)
                ->where('t1.attribute_id = ?', $this->_categoryAttributes[$attributeCode]['attribute_id'])
                ->where('t1.entity_id IN(?)', $categoryIds);
        }


        $rowSet = $this->_getWriteAdapter()->fetchAll($select);

        $attributes = array();
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
     * @param Varien_Object $product
     * @param string $attributeCode
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Url
     */
    public function saveProductAttribute(Varien_Object $product, $attributeCode)
    {
        if (!isset($this->_productAttributes[$attributeCode])) {
            $attribute = $this->getProductModel()->getResource()->getAttribute($attributeCode);

            $this->_productAttributes[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal()
            );
            unset($attribute);
        }

        $attributeTable = $this->_productAttributes[$attributeCode]['table'];

        $attributeData = array(
            'entity_type_id'    => $this->_productAttributes[$attributeCode]['entity_type_id'],
            'attribute_id'      => $this->_productAttributes[$attributeCode]['attribute_id'],
            'store_id'          => $product->getStoreId(),
            'entity_id'         => $product->getId(),
            'value'             => $product->getData($attributeCode)
        );

        if ($this->_productAttributes[$attributeCode]['is_global'] || $product->getStoreId() == 0) {
            $attributeData['store_id'] = 0;
        }

        $select = $this->_getWriteAdapter()->select()
            ->from($attributeTable)
            ->where('entity_type_id=?', $attributeData['entity_type_id'])
            ->where('attribute_id=?', $attributeData['attribute_id'])
            ->where('store_id=?', $attributeData['store_id'])
            ->where('entity_id=?', $attributeData['entity_id']);
        if ($row = $this->_getWriteAdapter()->fetchRow($select)) {
            $whereCond = $this->_getWriteAdapter()->quoteInto('value_id=?', $row['value_id']);
            $this->_getWriteAdapter()->update($attributeTable, $attributeData, $whereCond);
        }
        else {
            $this->_getWriteAdapter()->insert($attributeTable, $attributeData);
        }

        if ($attributeData['store_id'] != 0) {
            $attributeData['store_id'] = 0;
            $select = $this->_getWriteAdapter()->select()
                ->from($attributeTable)
                ->where('entity_type_id=?', $attributeData['entity_type_id'])
                ->where('attribute_id=?', $attributeData['attribute_id'])
                ->where('store_id=?', $attributeData['store_id'])
                ->where('entity_id=?', $attributeData['entity_id']);
            if ($row = $this->_getWriteAdapter()->fetchRow($select)) {
                $whereCond = $this->_getWriteAdapter()->quoteInto('value_id=?', $row['value_id']);
                $this->_getWriteAdapter()->update($attributeTable, $attributeData, $whereCond);
            }
            else {
                $this->_getWriteAdapter()->insert($attributeTable, $attributeData);
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
     * @param string $storeId
     * @return array
     */
    public function _getProductAttribute($attributeCode, $productIds, $storeId)
    {
        if (!isset($this->_productAttributes[$attributeCode])) {
            $attribute = $this->getProductModel()->getResource()->getAttribute($attributeCode);

            $this->_productAttributes[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal()
            );
            unset($attribute);
        }

        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }

        $attributeTable = $this->_productAttributes[$attributeCode]['table'];
        if ($this->_productAttributes[$attributeCode]['is_global'] || $storeId == 0) {
            $select = $this->_getWriteAdapter()->select()
                ->from($attributeTable, array('entity_id', 'value'))
                ->where('attribute_id = ?', $this->_productAttributes[$attributeCode]['attribute_id'])
                ->where('store_id=?', 0)
                ->where('entity_id IN(?)', $productIds);
        }
        else {
            $select = $this->_getWriteAdapter()->select()
                ->from(array('t1'=>$attributeTable), array('entity_id', 'IFNULL(t2.value, t1.value) as value'))
                ->joinLeft(
                    array('t2'=>$attributeTable),
                    $this->_getWriteAdapter()->quoteInto('t1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id=?', $storeId),
                    array()
                )
                ->where('t1.store_id = ?', 0)
                ->where('t1.attribute_id = ?', $this->_productAttributes[$attributeCode]['attribute_id'])
                ->where('t1.entity_id IN(?)', $productIds);
        }


        $rowSet = $this->_getWriteAdapter()->fetchAll($select);

        $attributes = array();
        foreach ($rowSet as $row) {
            $attributes[$row['entity_id']] = $row['value'];
        }
        unset($rowSet);
        foreach ($productIds as $productIds) {
            if (!isset($attributes[$productIds])) {
                $attributes[$productIds] = null;
            }
        }

        return $attributes;
    }

    /**
     * Prepare category parentId
     *
     * @param Varien_Object $category
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Url
     */
    protected function _prepareCategoryParentId(Varien_Object $category)
    {
        if ($category->getPath() != $category->getId()) {
            $split = split('/', $category->getPath());
            $category->setParentId($split[(count($split) - 2)]);
        }
        else {
            $category->setParentId(0);
        }
        return $this;
    }

    /**
     * Prepare stores root categories
     *
     * @param array $stores
     * @return array
     */
    protected function _prepareStoreRootCategories($stores)
    {
        $rootCategoryIds = array();
        foreach ($stores as $store) {
            /* @var $store Mage_Core_Model_Store */
            $rootCategoryIds[$store->getRootCategoryId()] = $store->getRootCategoryId();
        }
        if ($rootCategoryIds) {
            $categories = $this->_getCategories($rootCategoryIds);
        }
        foreach ($stores as $store) {
            /* @var $store Mage_Core_Model_Store */
            if (isset($categories[$store->getRootCategoryId()])) {
                $store->setRootCategoryPath($categories[$store->getRootCategoryId()]->getPath());
                $store->setRootCategory($categories[$store->getRootCategoryId()]);
            }
            else {
                unset($stores[$store->getId()]);
            }
        }
        return $stores;
    }

    protected function _getCategories($categoryIds, $storeId = null, $path = null)
    {
        $isActiveAttribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_category', 'is_active');
        $categories = array();

        if (!is_array($categoryIds)) {
            $categoryIds = array($categoryIds);
        }

        $select = $this->_getWriteAdapter()->select()
            ->from(array('main_table'=>$this->getTable('catalog/category')), array('main_table.entity_id', 'main_table.parent_id', 'is_active'=>'IFNULL(c.value, d.value)', 'main_table.path'));

        if (is_null($path)) {
            $select->where('main_table.entity_id IN(?)', $categoryIds);
        }
        else {
            $select->where('main_table.path LIKE ?', $path . '%')
                ->order('main_table.path');
        }
        $table = $this->getTable('catalog/category') . '_int';
        $select->joinLeft(array('d'=>$table), "d.attribute_id = '{$isActiveAttribute->getId()}' AND d.store_id = 0 AND d.entity_id = main_table.entity_id", array())
            ->joinLeft(array('c'=>$table), "c.attribute_id = '{$isActiveAttribute->getId()}' AND c.store_id = '{$storeId}' AND c.entity_id = main_table.entity_id", array());

        if (!is_null($storeId)) {
            $rootCategoryPath = $this->getStores($storeId)->getRootCategoryPath();
            $rootCategoryPathLength = strlen($rootCategoryPath);
        }

        $rowSet = $this->_getWriteAdapter()->fetchAll($select);
        foreach ($rowSet as $row) {
            if (!is_null($storeId) && substr($row['path'], 0, $rootCategoryPathLength) != $rootCategoryPath) {
                continue;
            }

            $category = new Varien_Object($row);
            $category->setIdFieldName('entity_id');
            $category->setStoreId($storeId);
            $this->_prepareCategoryParentId($category);

            $categories[$category->getId()] = $category;
        }
        unset($rowSet);

        if (!is_null($storeId) && $categories) {
            foreach (array('name', 'url_key', 'url_path') as $attributeCode) {
                $attributes = $this->_getCategoryAttribute($attributeCode, array_keys($categories), $category->getStoreId());
                foreach ($attributes as $categoryId => $attributeValue) {
                    $categories[$categoryId]->setData($attributeCode, $attributeValue);
                }
            }
        }

        return $categories;
    }

    public function getCategory($categoryId, $storeId)
    {
        if (!$categoryId || !$storeId) {
            return false;
        }

        $categories = $this->_getCategories($categoryId, $storeId);
        if (isset($categories[$categoryId])) {
            return $categories[$categoryId];
        }
        return false;
    }

    public function getCategories($categoryIds, $storeId)
    {
        if (!$categoryIds || !$storeId) {
            return false;
        }

        return $this->_getCategories($categoryIds, $storeId);
    }

    public function loadCategoryChilds(Varien_Object $category)
    {
        if (is_null($category->getId()) || is_null($category->getStoreId())) {
            return $category;
        }

        $categories = $this->_getCategories(null, $category->getStoreId(), $category->getPath() . '/');
        $category->setChilds(array());
        foreach ($categories as $child) {
            if (!is_array($child->getChilds())) {
                $child->setChilds(array());
            }
            if ($child->getParentId() == $category->getId()) {
                $category->setChilds($category->getChilds() + array($child->getId() => $child));
            }
            else {
                if (isset($categories[$child->getParentId()])) {
                    if (!is_array($categories[$child->getParentId()]->getChilds())) {
                        $categories[$child->getParentId()]->setChilds(array());
                    }
                    $categories[$child->getParentId()]->setChilds($categories[$child->getParentId()]->getChilds() + array($child->getId() => $child));
                }
            }
        }
        $category->setAllChilds($categories);

        return $category;
    }

    public function getCategoryParentPath(Varien_Object $category)
    {
        $store = Mage::app()->getStore($category->getStoreId());
        if ($category->getId() == $store->getRootCategoryId()) {
            return '';
        }
        elseif ($category->getParentId() == 1 || $category->getParentId() == $store->getRootCategoryId()) {
            return '';
        }
        else {
            $parentCategory = $this->getCategory($category->getParentId(), $store->getId());
            return $parentCategory->getUrlPath() . '/';
        }
    }

    public function getProductIdsByCategory($category)
    {
        $productIds = array();
        if ($category instanceof Varien_Object) {
            $categoryId = $category->getId();
        }
        else {
            $categoryId = $category;
        }
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getTable('catalog/category_product'))
            ->where('category_id=?', $categoryId)
            ->order('product_id');
        $rowSet = $this->_getWriteAdapter()->fetchAll($select);
        foreach ($rowSet as $row) {
            $productIds[$row['product_id']] = $row['product_id'];
        }

        return $productIds;
    }

    protected function _getProducts($productIds = null, $storeId, $entityId = 0, &$lastEntityId)
    {
        $products = array();

        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        if (!is_null($productIds)) {
            if (!is_array($productIds)) {
                $productIds = array($productIds);
            }
        }
        $select = $this->_getWriteAdapter()->select()
            ->from(array('e' => $this->getTable('catalog/product')), array('entity_id', 'category_ids'))
            ->join(
                array('w' => $this->getTable('catalog/product_website')),
                $this->_getWriteAdapter()->quoteInto('e.entity_id=w.product_id AND w.website_id=?', $websiteId),
                array()
            )
            ->where('e.entity_id>?', $entityId)
            ->order('e.entity_id')
            ->limit($this->_productLimit);
        if (!is_null($productIds)) {
            $select->where('e.entity_id IN(?)', $productIds);
        }

        $query = $this->_getWriteAdapter()->query((string)$select);
        while ($row = $query->fetch()) {
            $product = new Varien_Object($row);
            $product->setIdFieldName('entity_id');
            $product->setCategoryIds(split(',', $product->getCategoryIds()));
            $products[$product->getId()] = $product;
            $lastEntityId = $product->getId();
        }

        unset($query);

        if ($products) {
            foreach (array('name', 'url_key', 'url_path') as $attributeCode) {
                $attributes = $this->_getProductAttribute($attributeCode, array_keys($products), $storeId);
                foreach ($attributes as $productId => $attributeValue) {
                    $products[$productId]->setData($attributeCode, $attributeValue);
                }
            }
        }

        return $products;
    }

    public function getProduct($productId, $storeId)
    {
        $lastId   = 0;
        $products = $this->_getProducts($productId, $storeId, 0, $lastId);
        if (isset($products[$productId])) {
            return $products[$productId];
        }
        return false;
    }

    public function getProductsByStore($storeId, &$lastEntityId)
    {
        return $this->_getProducts(null, $storeId, $lastEntityId, $lastEntityId);
    }

    public function getProductsByCategory(Varien_Object $category, &$lastEntityId)
    {
        $productIds = $this->getProductIdsByCategory($category);
        if (!$productIds) {
            return array();
        }
        return $this->_getProducts($productIds, $category->getStoreId(), $lastEntityId, $lastEntityId);
    }

    public function clearCategoryProduct($storeId)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from(array('tur' => $this->getMainTable()), $this->getIdFieldName())
            ->joinLeft(
                array('tcp' => $this->getTable('catalog/category_product')),
                'tur.category_id=tcp.category_id AND tur.product_id=tcp.product_id',
                array()
            )->where('tur.store_id=?', $storeId)
            ->where('tur.category_id IS NOT NULL')
            ->where('tur.product_id IS NOT NULL')
            ->where('tcp.category_id IS NULL');
        $rowSet = $this->_getWriteAdapter()->fetchAll($select);
        $rewriteIds = array();
        foreach ($rowSet as $row) {
            $rewriteIds[] = $row[$this->getIdFieldName()];
        }
        if ($rewriteIds) {
            $where = $this->_getWriteAdapter()->quoteInto($this->getIdFieldName() . ' IN(?)', $rewriteIds);
            $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        }
    }


//    protected $_rewrite = array();
//    protected $_catRoots = array();
//
//    public function addCategoryToRewrite($category)
//    {
//        $a = array('refresh'=>true);
//        if (is_object($category)) {
//            $a['path'] = $category->getPath();
//            $this->_rewrite[0]['category'][$category->getId()] = $a;
//        } elseif (is_numeric($category)) {
//            $this->_rewrite[0]['category'][$category] = $a;
//        }
//        return $this;
//    }
//
//    public function addProductToRewrite($product)
//    {
//        $a = array('refresh'=>true);
//        if (is_object($product)) {
//            $this->_rewrite[0]['product'][$product->getId()] = $a;
//        } else {
//            $this->_rewrite[0]['product'][$product] = $a;
//        }
//        return $this;
//    }
//
//    protected function _getCategoryRootsByStore()
//    {
//        if (!$this->_catRoots) {
//            $res = Mage::getSingleton('core/resource');
//            /* @var $res Mage_Core_Model_Resource */
//            $read = $res->getConnection('core_read');
//            /* @var $read Zend_Db_Adapter_Abstract */
//            $select = $read->select()->from(array(
//                        's' => $res->getTableName('core/store'),
//                        'g' => $res->getTableName('core/store_group'),
//                        'c' => $res->getTableName('catalog/category'),
//                    ), array('s.store_id', 'g.category_root_id'=>'category_id', 'c.path'))
//                ->where('s.group_id=g.group_id and c.entity_id=g.category_root_id');
//            $categories = $read->fetchAll($select);
//            $this->_catRoots = array();
//            foreach ($categories as $c) {
//                $this->_catRoots[$c['store_id']] = $c;
//            }
//        }
//        return $this->_catRoots;
//    }
//
//    protected function _loadCategories()
//    {
//        if (empty($this->_rewrite[0]['category'])) {
//            return;
//        }
//        $res = Mage::getSingleton('core/resource');
//        /* @var $res Mage_Core_Model_Resource */
//        $read = $res->getConnection('core_read');
//        /* @var $read Zend_Db_Adapter_Abstract */
//
//        // first load categories that don't have path
//        $req1 = array();
//        foreach ($this->_rewrite[0]['category'] as $cId=>$rData) {
//            if (empty($rData['path'])) {
//                $req1[] = $cId;
//            }
//        }
//        $select = $read->select()->from(array(
//            'c' => $res->getTableName('catalog/category'),
//            'url_key' => $res->getTableName('catalog/category').'_varchar',
//            'url_path' => $res->getTableName('catalog/category').'_varchar',
//        ), array('c.entity_id'=>'category_id', 'c.path', 'url_path.value'=>'url_path'))
//        // now load all required categories (children)
//        $req2 = array();
//        foreach ($this->_rewrite[0]['category'] as $cId=>$rData) {
//            $req2[] = "path like ''";
//            foreach ($this->_getCategoryRootsByStore() as $storeId=>$c) {
//                $this->_rewrite[$storeId]['category']
//            }
//        }
//        $collection = Mage::getModel('catalog/category')->getCollection()
//            ->;
//    }
//
//    protected function _loadProducts()
//    {
//        if (empty($this->_rewrite[0]['product'])) {
//            return;
//        }
//    }
//
//    public function commitRewrites()
//    {
//        $this->_loadCategories();
//        $this->_loadProducts();
//
//    }
}