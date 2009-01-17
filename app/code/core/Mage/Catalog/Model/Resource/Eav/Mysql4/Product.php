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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product entity resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
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
        $resource = Mage::getSingleton('core/resource');
        $this->setType('catalog_product')
            ->setConnection(
                $resource->getConnection('catalog_read'),
                $resource->getConnection('catalog_write')
            );

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
        return array('entity_type_id', 'attribute_set_id', 'type_id', 'created_at', 'updated_at');
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
     * @param   $product
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    public function getCategoryIds($product)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_productCategoryTable, 'category_id')
            ->where('product_id=?', $product->getId());
        return $this->_getWriteAdapter()->fetchCol($select);
    }

    public function getIdBySku($sku)
    {
         return $this->_read->fetchOne('select entity_id from '.$this->getEntityTable().' where sku=?', $sku);
    }

    protected function _beforeSave(Varien_Object $object)
    {
        if (!$object->getId() && $object->getSku()) {
           $object->setId($this->getIdBySku($object->getSku()));
        }

        $categoryIds = $object->getCategoryIds();
        if ($categoryIds) {
            $categoryIds = Mage::getModel('catalog/category')->verifyIds($categoryIds);
        }

        $object->setData('category_ids', implode(',', $categoryIds));

        return parent::_beforeSave($object);
    }

    protected function _afterSave(Varien_Object $product)
    {
        $this->_saveWebsiteIds($product)
            ->_saveCategories($product)
            ->refreshIndex($product);

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
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    protected function _saveCategories(Varien_Object $object)
    {
        $categoryIds = $object->getCategoryIds();

        $oldCategoryIds = $object->getOrigData('category_ids');
        $oldCategoryIds = !empty($oldCategoryIds) ? explode(',', $oldCategoryIds) : array();

        $object->setIsChangedCategories(false);

        $insert = array_diff($categoryIds, $oldCategoryIds);
        $delete = array_diff($oldCategoryIds, $categoryIds);

        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $insertSql = array();
            foreach ($insert as $v) {
                if (!empty($v)) {
                    $insertSql[] = '('.(int)$v.','.$object->getId().',0)';
                }
            };
            if ($insertSql) {
                $write->query("insert into {$this->_productCategoryTable} (category_id, product_id, position) values ".join(',', $insertSql));
            }
        }

        if (!empty($delete)) {
            $write->delete($this->_productCategoryTable,
                $write->quoteInto('product_id=?', $object->getId())
                .' and '.$write->quoteInto('category_id in (?)', $delete)
            );
        }

        if (!empty($insert) || !empty($delete)) {
            $object->setIsChangedCategories(true);
        }

        return $this;
    }

    public function refreshIndex($product)
    {
        $categoryIds = $product->getCategoryIds();

        /**
         * Refresh category/product index
         */
        $this->_getWriteAdapter()->delete($this->getTable('catalog/category_product_index'), 'product_id='.$product->getId());
        if (!empty($categoryIds)) {
            $categoriesSelect = $this->_getWriteAdapter()->select()->from($this->getTable('catalog/category'))
                ->where('entity_id IN (?)', $categoryIds);
            $categoriesInfo = $this->_getWriteAdapter()->fetchAll($categoriesSelect);

            // get categories positions (bug #6940)
            $select = $this->_getWriteAdapter()->select()
                ->from($this->getTable('catalog/category_product'), array('category_id', 'position'))
                ->where('product_id=' . $product->getId());
            $categoriesPositions = $this->_getWriteAdapter()->fetchPairs($select);

            $indexCategoryIds = array();
            foreach ($categoriesInfo as $categoryInfo) {
                $ids = explode('/', $categoryInfo['path']);
                $ids[] = $categoryInfo['entity_id'];
                $indexCategoryIds = array_merge($indexCategoryIds, $ids);
            }
            $indexCategoryIds = array_unique($indexCategoryIds);
            foreach ($indexCategoryIds as $categoryId) {
                $data = array(
                    'category_id'   => $categoryId,
                    'product_id'    => $product->getId(),
                    'position'      => (isset($categoriesPositions[(int)$categoryId]) ? $categoriesPositions[(int)$categoryId] : 0),
                    'is_parent'     => in_array($categoryId, $categoryIds)
                );
                $this->_getWriteAdapter()->insert($this->getTable('catalog/category_product_index'), $data);
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
        $statusTable    = $statusAttribute->getBackend()->getTable();
        $visibilityTable= $visibilityAttribute->getBackend()->getTable();

        $indexTable = $this->getTable('catalog/product_enabled_index');
        if (is_null($store) && is_null($product)) {
            Mage::throwException(Mage::helper('catalog')->__('For reindex enabled product(s) you need specify store or product'));
        }
        elseif (is_null($product) || is_array($product)) {
            $storeId    = $store->getId();
            $websiteId  = $store->getWebsiteId();

            $productsCondition = '';
            $deleteCondition = '';
            if (is_array($product) && !empty($product)) {
                $productsCondition  = $this->_getWriteAdapter()->quoteInto(' AND t_v_default.entity_id IN (?)', $product);
                $deleteCondition    = $this->_getWriteAdapter()->quoteInto(' AND product_id IN (?)', $product);
            }
        	$this->_getWriteAdapter()->delete($indexTable, 'store_id='.$storeId.$deleteCondition);
        	$query = "INSERT INTO $indexTable
            SELECT
                t_v_default.entity_id, {$storeId}, IFNULL(t_v.value, t_v_default.value)
            FROM
                {$visibilityTable} AS t_v_default
            INNER JOIN {$this->getTable('catalog/product_website')} AS w
                ON w.product_id=t_v_default.entity_id AND w.website_id={$websiteId}
            LEFT JOIN {$visibilityTable} AS `t_v`
                ON (t_v.entity_id = t_v_default.entity_id) AND (t_v.attribute_id='{$visibilityAttributeId}') AND (t_v.store_id='{$storeId}')
            INNER JOIN {$statusTable} AS `t_s_default`
                ON (t_s_default.entity_id = t_v_default.entity_id) AND (t_s_default.attribute_id='{$statusAttributeId}') AND t_s_default.store_id=0
            LEFT JOIN {$statusTable} AS `t_s`
                ON (t_s.entity_id = t_v_default.entity_id) AND (t_s.attribute_id='{$statusAttributeId}') AND (t_s.store_id='{$storeId}')
            WHERE
                t_v_default.attribute_id='{$visibilityAttributeId}' AND t_v_default.store_id=0{$productsCondition}
                AND (IFNULL(t_s.value, t_s_default.value)=".Mage_Catalog_Model_Product_Status::STATUS_ENABLED.")";
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
                {$productId}, {$storeId}, IFNULL(t_v.value, t_v_default.value)
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
                AND (IFNULL(t_s.value, t_s_default.value)=".Mage_Catalog_Model_Product_Status::STATUS_ENABLED.")";
            $this->_getWriteAdapter()->query($query);
        }

        return $this;
    }

    /**
     * Retrieve collection of product categories
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return unknown
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


    public function getDefaultAttributeSourceModel()
    {
        return 'eav/entity_attribute_source_table';
    }

    /**
     * Validate all object's attributes against configuration
     *
     * @param Varien_Object $object
     * @return Varien_Object
     */
    public function validate($object)
    {
        parent::validate($object);
        return $this;
    }


    public function getParentProductIds($object)
    {
        $childId = $object->getId();

        $groupedProductsTable = $this->getTable('catalog/product_link');
        $groupedLinkTypeId = Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED;

        $configurableProductsTable = $this->getTable('catalog/product_super_link');

        $groupedSelect = $this->_getReadAdapter()->select()
            ->from(array('g'=>$groupedProductsTable), 'g.product_id')
            ->where("g.linked_product_id = ?", $childId)
            ->where("link_type_id = ?", $groupedLinkTypeId);

        $configurableSelect = $this->_getReadAdapter()->select()
            ->from(array('c'=>$configurableProductsTable), 'c.parent_id')
            ->where("c.product_id = ?", $childId);

        $select = $this->_getReadAdapter()->select();
        $select
            ->from(array('e'=>$this->getTable('catalog/product')), 'DISTINCT(e.entity_id)')
            ->where('e.entity_id in ('.new Zend_Db_Expr($groupedSelect).' UNION '.new Zend_Db_Expr($configurableSelect).')');

        return $this->_getReadAdapter()->fetchCol($select);
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
}