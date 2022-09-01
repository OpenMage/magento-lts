<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product entity resource model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product extends Mage_Catalog_Model_Resource_Abstract
{
    /**
     * Product to website linkage table
     *
     * @var string
     */
    protected $_productWebsiteTable;

    /**
     * Product to category linkage table
     *
     * @var string
     */
    protected $_productCategoryTable;

    /**
     * Initialize resource
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType(Mage_Catalog_Model_Product::ENTITY)
             ->setConnection('catalog_read', 'catalog_write');
        $this->_productWebsiteTable  = $this->getTable('catalog/product_website');
        $this->_productCategoryTable = $this->getTable('catalog/category_product');
    }

    /**
     * Default product attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return ['entity_id', 'entity_type_id', 'attribute_set_id', 'type_id', 'created_at', 'updated_at'];
    }

    /**
     * Retrieve product website identifiers
     *
     * @param Mage_Catalog_Model_Product|int $product
     * @return array
     */
    public function getWebsiteIds($product)
    {
        $adapter = $this->_getReadAdapter();

        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        $select = $adapter->select()
            ->from($this->_productWebsiteTable, 'website_id')
            ->where('product_id = ?', (int)$productId);

        return $adapter->fetchCol($select);
    }

    /**
     * Retrieve product website identifiers by product identifiers
     *
     * @param array $productIds
     * @return  array
     */
    public function getWebsiteIdsByProductIds($productIds)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_productWebsiteTable, ['product_id', 'website_id'])
            ->where('product_id IN (?)', $productIds);
        $productsWebsites = [];
        foreach ($this->_getWriteAdapter()->fetchAll($select) as $productInfo) {
            $productId = $productInfo['product_id'];
            if (!isset($productsWebsites[$productId])) {
                $productsWebsites[$productId] = [];
            }
            $productsWebsites[$productId][] = $productInfo['website_id'];
        }

        return $productsWebsites;
    }

    /**
     * Retrieve product category identifiers
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getCategoryIds($product)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->_productCategoryTable, 'category_id')
            ->where('product_id = ?', (int)$product->getId());

        return $adapter->fetchCol($select);
    }

    /**
     * Get product identifier by sku
     *
     * @param string $sku
     * @return int|false
     */
    public function getIdBySku($sku)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getEntityTable(), 'entity_id')
            ->where('sku = :sku');

        $bind = [':sku' => (string)$sku];

        return $adapter->fetchOne($select, $bind);
    }

    /**
     * Process product data before save
     *
     * @param Mage_Catalog_Model_Product $object
     * @inheritDoc
     */
    protected function _beforeSave(Varien_Object $object)
    {
        /**
         * Try detect product id by sku if id is not declared
         */
        if (!$object->getId() && $object->getSku()) {
            $object->setId($this->getIdBySku($object->getSku()));
        }

        /**
         * Check if declared category ids in object data.
         */
        if ($object->hasCategoryIds()) {
            $categoryIds = Mage::getResourceSingleton('catalog/category')->verifyIds(
                $object->getCategoryIds()
            );
            $object->setCategoryIds($categoryIds);
        }

        return parent::_beforeSave($object);
    }

    /**
     * Save data related with product
     *
     * @param Mage_Catalog_Model_Product $product
     * @inheritDoc
     */
    protected function _afterSave(Varien_Object $product)
    {
        $this->_saveWebsiteIds($product)
            ->_saveCategories($product);

        return parent::_afterSave($product);
    }

    /**
     * Save product website relations
     *
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    protected function _saveWebsiteIds($product)
    {
        $websiteIds = $product->getWebsiteIds();
        $oldWebsiteIds = [];

        $product->setIsChangedWebsites(false);

        $adapter = $this->_getWriteAdapter();

        $oldWebsiteIds = $this->getWebsiteIds($product);

        $insert = array_diff($websiteIds, $oldWebsiteIds);
        $delete = array_diff($oldWebsiteIds, $websiteIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $websiteId) {
                $data[] = [
                    'product_id' => (int)$product->getId(),
                    'website_id' => (int)$websiteId
                ];
            }
            $adapter->insertMultiple($this->_productWebsiteTable, $data);
        }

        if (!empty($delete)) {
            foreach ($delete as $websiteId) {
                $condition = [
                    'product_id = ?' => (int)$product->getId(),
                    'website_id = ?' => (int)$websiteId,
                ];

                $adapter->delete($this->_productWebsiteTable, $condition);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $product->setIsChangedWebsites(true);
        }

        return $this;
    }

    /**
     * Save product category relations
     *
     * @param Varien_Object|Mage_Catalog_Model_Product $object
     * @return $this
     */
    protected function _saveCategories(Varien_Object $object)
    {
        /**
         * If category ids data is not declared we haven't do manipulations
         */
        if (!$object->hasCategoryIds()) {
            return $this;
        }
        $categoryIds = $object->getCategoryIds();
        $oldCategoryIds = $this->getCategoryIds($object);

        $object->setIsChangedCategories(false);

        $insert = array_diff($categoryIds, $oldCategoryIds);
        $delete = array_diff($oldCategoryIds, $categoryIds);

        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $categoryId) {
                if (empty($categoryId)) {
                    continue;
                }
                $data[] = [
                    'category_id' => (int)$categoryId,
                    'product_id'  => (int)$object->getId(),
                    'position'    => 1
                ];
            }
            if ($data) {
                $write->insertMultiple($this->_productCategoryTable, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $categoryId) {
                $where = [
                    'product_id = ?'  => (int)$object->getId(),
                    'category_id = ?' => (int)$categoryId,
                ];

                $write->delete($this->_productCategoryTable, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $object->setAffectedCategoryIds(array_merge($insert, $delete));
            $object->setIsChangedCategories(true);
        }

        return $this;
    }

    /**
     * Refresh Product Enabled Index
     *
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function refreshIndex($product)
    {
        $writeAdapter = $this->_getWriteAdapter();

        /**
         * Ids of all categories where product is assigned (not related with store)
         */
        $categoryIds = $product->getCategoryIds();

        /**
         * Clear previous index data related with product
         */
        $condition = ['product_id = ?' => (int)$product->getId()];
        $writeAdapter->delete($this->getTable('catalog/category_product_index'), $condition);

        /** @var Mage_Catalog_Model_Resource_Category $categoryObject */
        $categoryObject = Mage::getResourceSingleton('catalog/category');
        if (!empty($categoryIds)) {
            $categoriesSelect = $writeAdapter->select()
                ->from($this->getTable('catalog/category'))
                ->where('entity_id IN (?)', $categoryIds);

            $categoriesInfo = $writeAdapter->fetchAll($categoriesSelect);

            $indexCategoryIds = [];
            foreach ($categoriesInfo as $categoryInfo) {
                $ids = explode('/', $categoryInfo['path']);
                $ids[] = $categoryInfo['entity_id'];
                $indexCategoryIds = array_merge($indexCategoryIds, $ids);
            }

            $indexCategoryIds   = array_unique($indexCategoryIds);
            $indexProductIds    = [$product->getId()];

            $categoryObject->refreshProductIndex($indexCategoryIds, $indexProductIds);
        } else {
            $websites = $product->getWebsiteIds();

            if ($websites) {
                $storeIds = [];

                foreach ($websites as $websiteId) {
                    $website  = Mage::app()->getWebsite($websiteId);
                    $storeIds = array_merge($storeIds, $website->getStoreIds());
                }

                $categoryObject->refreshProductIndex([], [$product->getId()], $storeIds);
            }
        }

        /**
         * Refresh enabled products index (visibility state)
         */
        $this->refreshEnabledIndex(null, $product);

        return $this;
    }

    /**
     * Refresh index for visibility of enabled product in store
     * if store parameter is null - index will refreshed for all stores
     * if product parameter is null - idex will be refreshed for all products
     *
     * @param Mage_Core_Model_Store $store
     * @param Mage_Catalog_Model_Product $product
     * @throws Mage_Core_Exception
     * @return $this
     */
    public function refreshEnabledIndex($store = null, $product = null)
    {
        $statusAttribute        = $this->getAttribute('status');
        $visibilityAttribute    = $this->getAttribute('visibility');
        $statusAttributeId      = $statusAttribute->getId();
        $visibilityAttributeId  = $visibilityAttribute->getId();
        $statusTable            = $statusAttribute->getBackend()->getTable();
        $visibilityTable        = $visibilityAttribute->getBackend()->getTable();

        $adapter = $this->_getWriteAdapter();

        $select = $adapter->select();
        $condition = [];

        $indexTable = $this->getTable('catalog/product_enabled_index');
        if (is_null($store) && is_null($product)) {
            Mage::throwException(
                Mage::helper('catalog')->__('To reindex the enabled product(s), the store or product must be specified')
            );
        } elseif (is_null($product) || is_array($product)) {
            $storeId    = $store->getId();
            $websiteId  = $store->getWebsiteId();

            if (is_array($product) && !empty($product)) {
                $condition[] = $adapter->quoteInto('product_id IN (?)', $product);
            }

            $condition[] = $adapter->quoteInto('store_id = ?', $storeId);

            $selectFields = [
                't_v_default.entity_id',
                new Zend_Db_Expr($storeId),
                $adapter->getCheckSql('t_v.value_id > 0', 't_v.value', 't_v_default.value'),
            ];

            $select->joinInner(
                ['w' => $this->getTable('catalog/product_website')],
                $adapter->quoteInto(
                    'w.product_id = t_v_default.entity_id AND w.website_id = ?',
                    $websiteId
                ),
                []
            );
        } elseif ($store === null) {
            foreach ($product->getStoreIds() as $storeId) {
                $store = Mage::app()->getStore($storeId);
                $this->refreshEnabledIndex($store, $product);
            }
            return $this;
        } else {
            $productId = is_numeric($product) ? $product : $product->getId();
            $storeId   = is_numeric($store) ? $store : $store->getId();

            $condition = [
                'product_id = ?' => (int)$productId,
                'store_id   = ?' => (int)$storeId,
            ];

            $selectFields = [
                new Zend_Db_Expr($productId),
                new Zend_Db_Expr($storeId),
                $adapter->getCheckSql('t_v.value_id > 0', 't_v.value', 't_v_default.value')
            ];

            $select->where('t_v_default.entity_id = ?', $productId);
        }

        $adapter->delete($indexTable, $condition);

        $select->from(['t_v_default' => $visibilityTable], $selectFields);

        $visibilityTableJoinCond = [
            't_v.entity_id = t_v_default.entity_id',
            $adapter->quoteInto('t_v.attribute_id = ?', $visibilityAttributeId),
            $adapter->quoteInto('t_v.store_id     = ?', $storeId),
        ];

        $select->joinLeft(
            ['t_v' => $visibilityTable],
            implode(' AND ', $visibilityTableJoinCond),
            []
        );

        $defaultStatusJoinCond = [
            't_s_default.entity_id = t_v_default.entity_id',
            't_s_default.store_id = 0',
            $adapter->quoteInto('t_s_default.attribute_id = ?', $statusAttributeId),
        ];

        $select->joinInner(
            ['t_s_default' => $statusTable],
            implode(' AND ', $defaultStatusJoinCond),
            []
        );

        $statusJoinCond = [
            't_s.entity_id = t_v_default.entity_id',
            $adapter->quoteInto('t_s.store_id     = ?', $storeId),
            $adapter->quoteInto('t_s.attribute_id = ?', $statusAttributeId),
        ];

        $select->joinLeft(
            ['t_s' => $statusTable],
            implode(' AND ', $statusJoinCond),
            []
        );

        $valueCondition = $adapter->getCheckSql('t_s.value_id > 0', 't_s.value', 't_s_default.value');

        $select->where('t_v_default.attribute_id = ?', $visibilityAttributeId)
            ->where('t_v_default.store_id = ?', 0)
            ->where(sprintf('%s = ?', $valueCondition), Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        if (is_array($product) && !empty($product)) {
            $select->where('t_v_default.entity_id IN (?)', $product);
        }

        $adapter->query($adapter->insertFromSelect($select, $indexTable));

        return $this;
    }

    /**
     * Get collection of product categories
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getCategoryCollection($product)
    {
        return Mage::getResourceModel('catalog/category_collection')
            ->joinField(
                'product_id',
                'catalog/category_product',
                'product_id',
                'category_id = entity_id',
                null
            )
            ->addFieldToFilter('product_id', (int)$product->getId());
    }

    /**
     * Retrieve category ids where product is available
     *
     * @param Mage_Catalog_Model_Product $object
     * @return array
     */
    public function getAvailableInCategories($object)
    {
        // is_parent=1 ensures that we'll get only category IDs those are direct parents of the product, instead of
        // fetching all parent IDs, including those are higher on the tree
        $select = $this->_getReadAdapter()->select()->distinct()
            ->from($this->getTable('catalog/category_product_index'), ['category_id'])
            ->where('product_id = ? AND is_parent = 1', (int)$object->getEntityId());

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Get default attribute source model
     *
     * @return string
     */
    public function getDefaultAttributeSourceModel()
    {
        return 'eav/entity_attribute_source_table';
    }

    /**
     * Check availability display product in category
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $categoryId
     * @return string
     */
    public function canBeShowInCategory($product, $categoryId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/category_product_index'), 'product_id')
            ->where('product_id = ?', (int)$product->getId())
            ->where('category_id = ?', (int)$categoryId);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Duplicate product store values
     *
     * @param int $oldId
     * @param int $newId
     * @return $this
     */
    public function duplicate($oldId, $newId)
    {
        $adapter = $this->_getWriteAdapter();
        $eavTables = ['datetime', 'decimal', 'int', 'text', 'varchar'];

        $adapter = $this->_getWriteAdapter();

        // duplicate EAV store values
        foreach ($eavTables as $suffix) {
            $tableName = $this->getTable(['catalog/product', $suffix]);

            $select = $adapter->select()
                ->from($tableName, [
                    'entity_type_id',
                    'attribute_id',
                    'store_id',
                    'entity_id' => new Zend_Db_Expr($adapter->quote($newId)),
                    'value'
                ])
                ->where('entity_id = ?', $oldId)
                ->where('store_id > ?', 0);

            $adapter->query($adapter->insertFromSelect(
                $select,
                $tableName,
                [
                    'entity_type_id',
                    'attribute_id',
                    'store_id',
                    'entity_id',
                    'value'
                ],
                Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE
            ));
        }

        // set status as disabled
        $statusAttribute      = $this->getAttribute('status');
        $statusAttributeId    = $statusAttribute->getAttributeId();
        $statusAttributeTable = $statusAttribute->getBackend()->getTable();
        $updateCond[]         = 'store_id > 0';
        $updateCond[]         = $adapter->quoteInto('entity_id = ?', $newId);
        $updateCond[]         = $adapter->quoteInto('attribute_id = ?', $statusAttributeId);
        $adapter->update(
            $statusAttributeTable,
            ['value' => Mage_Catalog_Model_Product_Status::STATUS_DISABLED],
            $updateCond
        );

        return $this;
    }

    /**
     * Get SKU through product identifiers
     *
     * @param  array $productIds
     * @return array
     */
    public function getProductsSku(array $productIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/product'), ['entity_id', 'sku'])
            ->where('entity_id IN (?)', $productIds);
        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * @param  Mage_Catalog_Model_Product$object
     * @return array
     *@deprecated after 1.4.2.0
     */
    public function getParentProductIds($object)
    {
        return [];
    }

    /**
     * Retrieve product entities info
     *
     * @param  array|string|null $columns
     * @return array
     */
    public function getProductEntitiesInfo($columns = null)
    {
        if (!empty($columns) && is_string($columns)) {
            $columns = [$columns];
        }
        if (empty($columns) || !is_array($columns)) {
            $columns = $this->_getDefaultAttributes();
        }

        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('catalog/product'), $columns);

        return $adapter->fetchAll($select);
    }

    /**
     * Return assigned images for specific stores
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int|array $storeIds
     * @return array
     *
     */
    public function getAssignedImages($product, $storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }

        $mainTable = $product->getResource()->getAttribute('image')
            ->getBackend()
            ->getTable();
        $read      = $this->_getReadAdapter();
        $select    = $read->select()
            ->from(
                ['images' => $mainTable],
                ['value as filepath', 'store_id']
            )
            ->joinLeft(
                ['attr' => $this->getTable('eav/attribute')],
                'images.attribute_id = attr.attribute_id',
                ['attribute_code']
            )
            ->where('entity_id = ?', $product->getId())
            ->where('store_id IN (?)', $storeIds)
            ->where('attribute_code IN (?)', ['small_image', 'thumbnail', 'image']);

        return $read->fetchAll($select);
    }

    /**
     * Retrieve product categories
     *
     * @param Mage_Catalog_Model_Product $object
     * @return array
     */
    public function getCategoryIdsWithAnchors($object)
    {
        $selectRootCategories = $this->_getReadAdapter()->select()
            ->from(
                [$this->getTable('catalog/category')],
                ['entity_id']
            )
            ->where('level <= 1');
        $rootIds = $this->_getReadAdapter()->fetchCol($selectRootCategories);
        $select = $this->_getReadAdapter()->select()
            ->from(
                [$this->getTable('catalog/category_product_index')],
                ['category_id']
            )
            ->where('product_id = ?', (int)$object->getEntityId())
            ->where('category_id NOT IN(?)', $rootIds);

        return $this->_getReadAdapter()->fetchCol($select);
    }
}
