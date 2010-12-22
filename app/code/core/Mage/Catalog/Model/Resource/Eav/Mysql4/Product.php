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
 * Product entity resource model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product extends Mage_Catalog_Model_Resource_Eav_Mysql4_Abstract
{
    protected $_productWebsiteTable;
    protected $_productCategoryTable;

    /**
     * Initialize resource
     */
    public function __construct()
    {
        parent::__construct();
        $resource = Mage::getSingleton('core/resource');
        $this->setType('catalog_product')
            ->setConnection('catalog_read', 'catalog_write');
        $this->_productWebsiteTable = $resource->getTableName('catalog/product_website');
        $this->_productCategoryTable= $resource->getTableName('catalog/category_product');
    }

    /**
     * Default product attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return array('entity_id', 'entity_type_id', 'attribute_set_id', 'type_id', 'created_at', 'updated_at');
    }

    /**
     * Retrieve product website identifiers
     *
     * @param   $product
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    public function getWebsiteIds($product)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_productWebsiteTable, 'website_id')
            ->where('product_id=?', $product->getId());
        return $this->_getWriteAdapter()->fetchCol($select);
    }

    /**
     * Retrieve product category identifiers
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getCategoryIds($product)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->_productCategoryTable, 'category_id')
            ->where('product_id=?', $product->getId());
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Get product identifier by sku
     *
     * @param   string $sku
     * @return  int|false
     */
    public function getIdBySku($sku)
    {
         return $this->_getReadAdapter()->fetchOne('select entity_id from '.$this->getEntityTable().' where sku=?', $sku);
    }

    /**
     * Process product data before save
     *
     * @param   Varien_Object $object
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product
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
     * @param   Varien_Object $product
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    protected function _afterSave(Varien_Object $product)
    {
        $this->_saveWebsiteIds($product)
            ->_saveCategories($product)
            //->refreshIndex($product)
            ;

        parent::_afterSave($product);
        return $this;
    }

    /**
     * Save product website relations
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    protected function _saveWebsiteIds($product)
    {
        $websiteIds = $product->getWebsiteIds();
        $oldWebsiteIds = array();

        $product->setIsChangedWebsites(false);

        $select = $this->_getWriteAdapter()->select()
            ->from($this->_productWebsiteTable)
            ->where('product_id=?', $product->getId());
        $query  = $this->_getWriteAdapter()->query($select);
        while ($row = $query->fetch()) {
            $oldWebsiteIds[] = $row['website_id'];
        }

        $insert = array_diff($websiteIds, $oldWebsiteIds);
        $delete = array_diff($oldWebsiteIds, $websiteIds);

        if (!empty($insert)) {
            foreach ($insert as $websiteId) {
                $this->_getWriteAdapter()->insert($this->_productWebsiteTable, array(
                    'product_id' => $product->getId(),
                    'website_id' => $websiteId
                ));
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $websiteId) {
                $this->_getWriteAdapter()->delete($this->_productWebsiteTable, array(
                    $this->_getWriteAdapter()->quoteInto('product_id=?', $product->getId()),
                    $this->_getWriteAdapter()->quoteInto('website_id=?', $websiteId)
                ));
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
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product
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
            $data = array();
            foreach ($insert as $categoryId) {
                if (empty($categoryId)) {
                    continue;
                }
                $data[] = array(
                    'category_id' => (int)$categoryId,
                    'product_id'  => $object->getId(),
                    'position'    => 1
                );
            }
            if ($data) {
                $write->insertMultiple($this->_productCategoryTable, $data);
            }
        }

        if (!empty($delete)) {
            $where = join(' AND ', array(
                $write->quoteInto('product_id=?', $object->getId()),
                $write->quoteInto('category_id IN(?)', $delete)
            ));
            $write->delete($this->_productCategoryTable, $where);
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
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    public function refreshIndex($product)
    {
        /**
         * Ids of all categories where product is assigned (not related with store)
         */
        $categoryIds = $product->getCategoryIds();

        /**
         * Clear previos index data related with product
         */
        $this->_getWriteAdapter()->delete(
            $this->getTable('catalog/category_product_index'),
            $this->_getWriteAdapter()->quoteInto('product_id=?', $product->getId())
        );

        if (!empty($categoryIds)) {
            $categoriesSelect = $this->_getWriteAdapter()->select()
                ->from($this->getTable('catalog/category'))
                ->where('entity_id IN (?)', $categoryIds);
            $categoriesInfo = $this->_getWriteAdapter()->fetchAll($categoriesSelect);


            $indexCategoryIds = array();
            foreach ($categoriesInfo as $categoryInfo) {
                $ids = explode('/', $categoryInfo['path']);
                $ids[] = $categoryInfo['entity_id'];
                $indexCategoryIds = array_merge($indexCategoryIds, $ids);
            }

            $indexCategoryIds   = array_unique($indexCategoryIds);
            $indexProductIds    = array($product->getId());
            Mage::getResourceSingleton('catalog/category')
                ->refreshProductIndex($indexCategoryIds, $indexProductIds);
        }
        else {
            $websites = $product->getWebsiteIds();
            if ($websites) {
                $storeIds = array();
                foreach ($websites as $websiteId) {
                    $website  = Mage::app()->getWebsite($websiteId);
                    $storeIds = array_merge($storeIds, $website->getStoreIds());
                }
                Mage::getResourceSingleton('catalog/category')
                    ->refreshProductIndex(array(), array($product->getId()), $storeIds);
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
     * @param   Mage_Core_Model_Store $store
     * @param   Mage_Core_Model_Product $product
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    public function refreshEnabledIndex($store=null, $product=null)
    {
        $statusAttribute        = $this->getAttribute('status');
        $visibilityAttribute    = $this->getAttribute('visibility');
        $statusAttributeId      = $statusAttribute->getId();
        $visibilityAttributeId  = $visibilityAttribute->getId();
        $statusTable            = $statusAttribute->getBackend()->getTable();
        $visibilityTable        = $visibilityAttribute->getBackend()->getTable();

        $indexTable = $this->getTable('catalog/product_enabled_index');
        if (is_null($store) && is_null($product)) {
            Mage::throwException(
                Mage::helper('catalog')->__('To reindex the enabled product(s), the store or product must be specified.')
            );
        } elseif (is_null($product) || is_array($product)) {
            $storeId    = $store->getId();
            $websiteId  = $store->getWebsiteId();

            $productsCondition = '';
            $deleteCondition = '';
            if (is_array($product) && !empty($product)) {
                $productsCondition  = $this->_getWriteAdapter()->quoteInto(
                    ' AND t_v_default.entity_id IN (?)',
                    $product
                );
                $deleteCondition    = $this->_getWriteAdapter()->quoteInto(' AND product_id IN (?)', $product);
            }
            $this->_getWriteAdapter()->delete($indexTable, 'store_id='.$storeId.$deleteCondition);
            $query = "INSERT INTO $indexTable
            SELECT
                t_v_default.entity_id, {$storeId}, IF(t_v.value_id>0, t_v.value, t_v_default.value)
            FROM
                {$visibilityTable} AS t_v_default
            INNER JOIN {$this->getTable('catalog/product_website')} AS w
                ON w.product_id=t_v_default.entity_id AND w.website_id={$websiteId}
            LEFT JOIN {$visibilityTable} AS `t_v`
                ON (t_v.entity_id = t_v_default.entity_id)
                    AND (t_v.attribute_id='{$visibilityAttributeId}')
                    AND (t_v.store_id='{$storeId}')
            INNER JOIN {$statusTable} AS `t_s_default`
                ON (t_s_default.entity_id = t_v_default.entity_id)
                    AND (t_s_default.attribute_id='{$statusAttributeId}')
                    AND t_s_default.store_id=0
            LEFT JOIN {$statusTable} AS `t_s`
                ON (t_s.entity_id = t_v_default.entity_id)
                    AND (t_s.attribute_id='{$statusAttributeId}')
                    AND (t_s.store_id='{$storeId}')
            WHERE
                t_v_default.attribute_id='{$visibilityAttributeId}'
                AND t_v_default.store_id=0{$productsCondition}
                AND (IF(t_s.value_id>0, t_s.value, t_s_default.value)=".Mage_Catalog_Model_Product_Status::STATUS_ENABLED.")";
            $this->_getWriteAdapter()->query($query);
        }
        elseif (is_null($store)) {
            foreach ($product->getStoreIds() as $storeId) {
                $store = Mage::app()->getStore($storeId);
                $this->refreshEnabledIndex($store, $product);
            }
        }
        else {
            $productId  = $product->getId();
            $storeId    = $store->getId();
            $this->_getWriteAdapter()->delete($indexTable, 'product_id='.$productId.' AND store_id='.$storeId);
            $query = "INSERT INTO $indexTable
            SELECT
                {$productId}, {$storeId}, IF(t_v.value_id>0, t_v.value, t_v_default.value)
            FROM
                {$visibilityTable} AS t_v_default
            LEFT JOIN {$visibilityTable} AS `t_v`
                ON (t_v.entity_id = t_v_default.entity_id)
                    AND (t_v.attribute_id='{$visibilityAttributeId}')
                    AND (t_v.store_id='{$storeId}')
            INNER JOIN {$statusTable} AS `t_s_default`
                ON (t_s_default.entity_id = t_v_default.entity_id)
                    AND (t_s_default.attribute_id='{$statusAttributeId}')
                    AND t_s_default.store_id=0
            LEFT JOIN {$statusTable} AS `t_s`
                ON (t_s.entity_id = t_v_default.entity_id)
                    AND (t_s.attribute_id='{$statusAttributeId}')
                    AND (t_s.store_id='{$storeId}')
            WHERE
                t_v_default.entity_id={$productId}
                AND t_v_default.attribute_id='{$visibilityAttributeId}' AND t_v_default.store_id=0
                AND (IF(t_s.value_id>0, t_s.value, t_s_default.value)=".Mage_Catalog_Model_Product_Status::STATUS_ENABLED.")";
            $this->_getWriteAdapter()->query($query);
        }

        return $this;
    }

    /**
     * Get collection of product categories
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    public function getCategoryCollection($product)
    {
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->joinField('product_id',
                'catalog/category_product',
                'product_id',
                'category_id=entity_id',
                null)
            ->addFieldToFilter('product_id', (int) $product->getId());
        return $collection;
    }

    /**
     * Retrieve category ids where product is available
     *
     * @param Mage_Catalog_Model_Product $object
     * @return array
     */
    public function getAvailableInCategories($object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/category_product_index'), array('category_id'))
            ->where('product_id=?', $object->getEntityId());
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
     * Validate all object's attributes against configuration
     *
     * @todo implement full validation process with errors returning which are ignoring now
     *
     * @param Varien_Object $object
     * @return Varien_Object
     */
    public function validate($object)
    {
//        $this->walkAttributes('backend/beforeSave', array($object));
//        return parent::validate($object);
        parent::validate($object);
        return $this;
    }

    /**
     * Check availability display product in category
     *
     * @param   int $categoryId
     * @return  bool
     */
    public function canBeShowInCategory($product, $categoryId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/category_product_index'), 'product_id')
            ->where('product_id=?', $product->getId())
            ->where('category_id=?', $categoryId);
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Duplicate product store values
     *
     * @param int $oldId
     * @param int $newId
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    public function duplicate($oldId, $newId)
    {
        $adapter = $this->_getWriteAdapter();
        $eavTables = array('datetime', 'decimal', 'int', 'text', 'varchar');

        // duplicate EAV store values
        foreach ($eavTables as $suffix) {
            $tableName = $this->getTable('catalog_product_entity_' . $suffix);
            $sql = 'REPLACE INTO `' . $tableName . '` '
                . 'SELECT NULL, `entity_type_id`, `attribute_id`, `store_id`, ' . $newId . ', `value`'
                . 'FROM `' . $tableName . '` WHERE `entity_id`=' . $oldId . ' AND `store_id`>0';
            $adapter->query($sql);
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
            array('value' => Mage_Catalog_Model_Product_Status::STATUS_DISABLED),
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
            ->from($this->getTable('catalog/product'), array('entity_id', 'sku'))
            ->where('entity_id IN (?)', $productIds);
        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * @deprecated after 1.4.2.0
     * @param  $object Mage_Catalog_Model_Product
     * @return array
     */
    public function getParentProductIds($object)
    {
        return array();
    }
}
