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
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CatalogSearch Fulltext Index resource model
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Mysql4_Fulltext extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Searchable attributes cache
     *
     * @var array
     */
    protected $_searchableAttributes = null;

    /**
     * Index values separator
     *
     * @var string
     */
    protected $_separator = ' ';

    /**
     * Product Type Instances cache
     *
     * @var array
     */
    protected $_productTypes = array();

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('catalogsearch/fulltext', 'product_id');
    }

    /**
     * Regenerate search index for store(s)
     *
     * @param int $storeId Store View Id
     * @param int|array $productIds Product Entity Id(s)
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    public function rebuildIndex($storeId = null, $productIds = null)
    {
        if (is_null($storeId)) {
            foreach (Mage::app()->getStores(false) as $store) {
                $this->_rebuildStoreIndex($store->getId(), $productIds);
            }
        } else {
            $this->_rebuildStoreIndex($storeId, $productIds);
        }
        return $this;
    }

    /**
     * Regenerate search index for specific store
     *
     * @param int $storeId Store View Id
     * @param int|array $productIds Product Entity Id
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    protected function _rebuildStoreIndex($storeId, $productIds = null)
    {
        $this->cleanIndex($storeId, $productIds);

        // preparesearchable attributes
        $staticFields   = array();
        foreach ($this->_getSearchableAttributes('static') as $attribute) {
            $staticFields[] = $attribute->getAttributeCode();
        }
        $dynamicFields  = array(
            'int'       => array_keys($this->_getSearchableAttributes('int')),
            'varchar'   => array_keys($this->_getSearchableAttributes('varchar')),
            'text'      => array_keys($this->_getSearchableAttributes('text')),
        );

        // status and visibility filter
        $visibility     = $this->_getSearchableAttribute('visibility');
        $status         = $this->_getSearchableAttribute('status');
        $visibilityVals = Mage::getSingleton('catalog/product_visibility')->getVisibleInSearchIds();
        $statusVals     = Mage::getSingleton('catalog/product_status')->getVisibleStatusIds();

        $lastProductId = 0;
        while (true) {
            $products = $this->_getSearchableProducts($storeId, $staticFields, $productIds, $lastProductId);
            if (!$products) {
                break;
            }

            $productAttributes  = array();
            $productRelations   = array();
            foreach ($products as $productData) {
                $lastProductId = $productData['entity_id'];
                $productAttributes[$productData['entity_id']] = $productData['entity_id'];
                $productChilds = $this->_getProductChildIds($productData['entity_id'], $productData['type_id']);
                $productRelations[$productData['entity_id']] = $productChilds;
                if ($productChilds) {
                    foreach ($productChilds as $productChildId) {
                        $productAttributes[$productChildId] = $productChildId;
                    }
                }
            }

            $productIndexes     = array();
            $productAttributes  = $this->_getProductAttributes($storeId, $productAttributes, $dynamicFields);
            foreach ($products as $productData) {
                if (!isset($productAttributes[$productData['entity_id']])) {
                    continue;
                }
                $protductAttr = $productAttributes[$productData['entity_id']];
                if (!isset($protductAttr[$visibility->getId()]) || !in_array($protductAttr[$visibility->getId()], $visibilityVals)) {
                    continue;
                }
                if (!isset($protductAttr[$status->getId()]) || !in_array($protductAttr[$status->getId()], $statusVals)) {
                    continue;
                }

                $productIndex = array(
                    $productData['entity_id'] => $protductAttr
                );
                if ($productChilds = $productRelations[$productData['entity_id']]) {
                    foreach ($productChilds as $productChildId) {
                        if (isset($productAttributes[$productChildId])) {
                            $productIndex[$productChildId] = $productAttributes[$productChildId];
                        }
                    }
                }

                $index = $this->_prepareProductIndex($productIndex, $productData, $storeId);
                $productIndexes[$productData['entity_id']] = $index;
                //$this->_saveProductIndex($productData['entity_id'], $storeId, $index);
            }
            $this->_saveProductIndexes($storeId, $productIndexes);
        }

        $this->resetSearchResults();

        return $this;
    }

    /**
     * Retrieve searchable products per store
     *
     * @param int $storeId
     * @param array $staticFields
     * @param array|int $productIds
     * @param int $lastProductId
     * @param int $limit
     * @return array
     */
    protected function _getSearchableProducts($storeId, array $staticFields, $productIds = null, $lastProductId = 0, $limit = 100)
    {
        $entityType = $this->getEavConfig()->getEntityType('catalog_product');
        $store      = Mage::app()->getStore($storeId);

        $select = $this->_getReadAdapter()->select()
            ->from(
                array('e' => $this->getTable('catalog/product')),
                array_merge(array('entity_id', 'type_id'), $staticFields))
            ->joinInner(
                array('website' => $this->getTable('catalog/product_website')),
                $this->_getReadAdapter()->quoteInto('website.product_id=e.entity_id AND website.website_id=?', $store->getWebsiteId()),
                array()
            );

        if (!is_null($productIds)) {
            $select->where('e.entity_id IN(?)', $productIds);
        }

        $select->where('e.entity_id>?', $lastProductId)
            ->limit($limit)
            ->order('e.entity_id');

        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Reset search results
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    public function resetSearchResults()
    {
        $this->beginTransaction();
        try {
            $this->_getWriteAdapter()->update($this->getTable('catalogsearch/search_query'), array('is_processed' => 0));
            $this->_getWriteAdapter()->query("TRUNCATE TABLE {$this->getTable('catalogsearch/result')}");

            $this->commit();
        }
        catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        Mage::dispatchEvent('catalogsearch_reset_search_result');

        return $this;
    }

    /**
     * Delete search index data for store
     *
     * @param int $storeId Store View Id
     * @param int $productId Product Entity Id
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    public function cleanIndex($storeId = null, $productId = null)
    {
        $where = array();

        if (!is_null($storeId)) {
            $where[] = $this->_getWriteAdapter()->quoteInto('store_id=?', $storeId);
        }
        if (!is_null($productId)) {
            $where[] = $this->_getWriteAdapter()->quoteInto('product_id IN(?)', $productId);
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        return $this;
    }

    /**
     * Prepare results for query
     *
     * @param Mage_CatalogSearch_Model_Fulltext $object
     * @param string $queryText
     * @param Mage_CatalogSearch_Model_Query $query
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    public function prepareResult($object, $queryText, $query)
    {
        if (!$query->getIsProcessed()) {
            $searchType = $object->getSearchType($query->getStoreId());

            $stringHelper = Mage::helper('core/string');
            /* @var $stringHelper Mage_Core_Helper_String */

            $bind = array(
                ':query'     => $queryText
            );
            $like = array();

            $fulltextCond   = '';
            $likeCond       = '';
            $separateCond   = '';

            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE
                || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
                $words = $stringHelper->splitWords($queryText, true, $query->getMaxQueryWords());
                $likeI = 0;
                foreach ($words as $word) {
                    $like[] = '`data_index` LIKE :likew' . $likeI;
                    $bind[':likew' . $likeI] = '%' . $word . '%';
                    $likeI ++;
                }
                if ($like) {
                    $likeCond = '(' . join(' AND ', $like) . ')';
                }
            }
            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_FULLTEXT
                || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
                $fulltextCond = 'MATCH (`data_index`) AGAINST (:query IN BOOLEAN MODE)';
            }
            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE && $likeCond) {
                $separateCond = ' OR ';
            }

            $sql = sprintf("REPLACE INTO `{$this->getTable('catalogsearch/result')}` "
                . "(SELECT '%d', `product_id`, MATCH (`data_index`) AGAINST (:query IN BOOLEAN MODE) "
                . "FROM `{$this->getMainTable()}` WHERE (%s%s%s) AND `store_id`='%d')",
                $query->getId(),
                $fulltextCond,
                $separateCond,
                $likeCond,
                $query->getStoreId()
            );

            $this->_getWriteAdapter()->query($sql, $bind);

            $query->setIsProcessed(1);
        }

        return $this;
    }

    /**
     * Retrieve EAV Config Singleton
     *
     * @return Mage_Eav_Model_Config
     */
    public function getEavConfig()
    {
        return Mage::getSingleton('eav/config');
    }

    /**
     * Retrieve Searchable attributes
     *
     * @return array
     */
    protected function _getSearchableAttributes($backendType = null)
    {
        if (is_null($this->_searchableAttributes)) {
            $this->_searchableAttributes = array();
            $entityType = $this->getEavConfig()->getEntityType('catalog_product');
            $entity     = $entityType->getEntity();

            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('eav/attribute'), array('attribute_code'))
                ->where('entity_type_id=?', $entityType->getEntityTypeId())
                ->where('is_searchable=?', 1);
            $attributeCodes = array_merge($this->_getReadAdapter()->fetchCol($select), array('status', 'visibility'));
            $this->getEavConfig()->preloadAttributes($entityType, $attributeCodes);
            foreach ($attributeCodes as $attributeCode) {
                $attribute = $this->getEavConfig()->getAttribute($entityType, $attributeCode);
                $attribute->setEntity($entity);
                $this->_searchableAttributes[$attribute->getId()] = $attribute;
            }
        }
        if (!is_null($backendType)) {
            $attributes = array();
            foreach ($this->_searchableAttributes as $attribute) {
                if ($attribute->getBackendType() == $backendType) {
                    $attributes[$attribute->getId()] = $attribute;
                }
            }
            return $attributes;
        }
        return $this->_searchableAttributes;
    }

    /**
     * Retrieve searchable attribute by Id or code
     *
     * @param int|string $attribute
     * @return Mage_Eav_Model_Entity_Attribute
     */
    protected function _getSearchableAttribute($attribute)
    {
        $attributes = $this->_getSearchableAttributes();
        if (is_numeric($attribute)) {
            if (isset($attributes[$attribute])) {
                return $attributes[$attribute];
            }
        }
        elseif (is_string($attribute)) {
            foreach ($attributes as $attributeModel) {
                if ($attributeModel->getAttributeCode() == $attribute) {
                    return $attributeModel;
                }
            }
        }
        return $this->getEavConfig()->getAttribute('catalog_product', $attribute);
    }

    /**
     * Load product(s) attributes
     *
     * @param int $storeId
     * @param array $productIds
     * @param array $atributeTypes
     *
     * @return array
     */
    protected function _getProductAttributes($storeId, array $productIds, array $atributeTypes)
    {
        $result  = array();
        $selects = array();
        foreach ($atributeTypes as $backendType => $attributeIds) {
            if ($attributeIds) {
                $tableName = $this->getTable('catalog/product') . '_' . $backendType;
                $selects[] = $this->_getReadAdapter()->select()
                    ->from(
                        array('t_default' => $tableName),
                        array('entity_id', 'attribute_id'))
                    ->joinLeft(
                        array('t_store' => $tableName),
                        $this->_getReadAdapter()->quoteInto("t_default.entity_id=t_store.entity_id AND t_default.attribute_id=t_store.attribute_id AND t_store.store_id=?", $storeId),
                        array('value'=>'IFNULL(t_store.value, t_default.value)'))
                    ->where('t_default.store_id=?', 0)
                    ->where('t_default.attribute_id IN(?)', $attributeIds)
                    ->where('t_default.entity_id IN(?)', $productIds);
            }
        }

        if ($selects) {
            $select = '('.join(')UNION(', $selects).')';
            $query = $this->_getReadAdapter()->query($select);
            while ($row = $query->fetch()) {
                $result[$row['entity_id']][$row['attribute_id']] = $row['value'];
            }
        }

        return $result;
    }

    /**
     * Return all product children ids
     *
     * @param int $productId Product Entity Id
     * @param string $typeId Super Product Link Type
     * @return array
     */
    protected function _getProductChildIds($productId, $typeId)
    {
        if (!isset($this->_productTypes[$typeId])) {
            $productEmulator = new Varien_Object();
            $productEmulator->setTypeId($typeId);

            $typeInstance = Mage::getSingleton('catalog/product_type')->factory($productEmulator);
            $this->_productTypes[$typeId] = $typeInstance->isComposite() ? $typeInstance->getRelationInfo() : false;
        }

        $relation = $this->_productTypes[$typeId];
        if ($relation && $relation->getTable() && $relation->getParentFieldName() && $relation->getChildFieldName()) {
            $select = $this->_getReadAdapter()->select()
                ->from(
                    array('main' => $this->getTable($relation->getTable())),
                    array($relation->getChildFieldName()))
                ->where("{$relation->getParentFieldName()}=?", $productId);
            if (!is_null($relation->getWhere())) {
                $select->where($relation->getWhere());
            }
            return $this->_getReadAdapter()->fetchCol($select);
        }

        return null;
    }

    /**
     * Prepare Fulltext index value for product
     *
     * @param array $indexData
     * @param array $productData
     * @return string
     */
    protected function _prepareProductIndex($indexData, $productData, $storeId)
    {
        $index = array();
        foreach ($this->_getSearchableAttributes('static') as $attribute) {
            if (isset($productData[$attribute->getAttributeCode()])) {
                if ($value = $this->_getAttributeValue($attribute->getId(), $productData[$attribute->getAttributeCode()], $storeId)) {
                    $index[] = $value;
                }
            }
        }
        foreach ($indexData as $attributeData) {
            foreach ($attributeData as $attributeId => $attributeValue) {
                if ($value = $this->_getAttributeValue($attributeId, $attributeValue, $storeId)) {
                    $index[] = $value;
                }
            }
        }
        return join($this->_separator, $index);
    }

    /**
     * Retrieve attribute source value for search
     *
     * @param int $attributeId
     * @param mixed $value
     * @return mixed
     */
    protected function _getAttributeValue($attributeId, $value, $storeId)
    {
        $attribute = $this->_getSearchableAttribute($attributeId);
        if (!$attribute->getIsSearchable()) {
            return null;
        }
        if ($attribute->usesSource()) {
            $attribute->setStoreId($storeId);
            $value = $attribute->getSource()->getOptionText($value);
        }

        if (is_array($value)) {
            $value = implode($this->_separator, $value);
        }

        return preg_replace("#\s+#si", " ", trim(strip_tags($value)));
    }

    /**
     * Save Product index
     *
     * @param int $productId
     * @param int $storeId
     * @param string $index
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    protected function _saveProductIndex($productId, $storeId, $index)
    {
        $this->_getWriteAdapter()->insert($this->getMainTable(), array(
            'product_id'    => $productId,
            'store_id'      => $storeId,
            'data_index'    => $index
        ));
        return $this;
    }

    /**
     * Save Multiply Product indexes
     *
     * @param int $storeId
     * @param array $productIndexes
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    protected function _saveProductIndexes($storeId, $productIndexes)
    {
        $values = array();
        $bind   = array();
        foreach ($productIndexes as $productId => &$index) {
            $values[] = sprintf('(%s,%s,%s)',
                $this->_getWriteAdapter()->quoteInto('?', $productId),
                $this->_getWriteAdapter()->quoteInto('?', $storeId),
                '?'
            );
            $bind[] = $index;
        }

        if ($values) {
            $sql = "REPLACE INTO `{$this->getMainTable()}` VALUES"
                . join(',', $values);
            $this->_getWriteAdapter()->query($sql, $bind);
        }

        return $this;
    }
}
