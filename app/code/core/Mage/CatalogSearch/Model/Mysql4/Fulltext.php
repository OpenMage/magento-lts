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
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    protected $_separator = '|';

    /**
     * Array of Zend_Date objects per store
     *
     * @var array
     */
    protected $_dates = array();

    /**
     * Product Type Instances cache
     *
     * @var array
     */
    protected $_productTypes = array();

    /**
     * Store search engine instance
     * @var object
     */
    protected $_engine = null;

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('catalogsearch/fulltext', 'product_id');
        $this->_engine = Mage::helper('catalogsearch')->getEngine();
    }

    /**
     * Return options separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    /**
     * Update category'es products indexes
     *
     * @param array $productIds
     * @param array $categoryIds
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    public function updateCategoryIndex($productIds, $categoryIds)
    {
        if ($this->_engine && $this->_engine->allowAdvancedIndex()) {
            $this->_engine->updateCategoryIndex($productIds, $categoryIds);
        }

        return $this;
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
            'decimal'   => array_keys($this->_getSearchableAttributes('decimal')),
            'datetime'  => array_keys($this->_getSearchableAttributes('datetime')),
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

            $productAttributes = array();
            $productRelations  = array();
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

            $productIndexes    = array();
            $productAttributes = $this->_getProductAttributes($storeId, $productAttributes, $dynamicFields);
            foreach ($products as $productData) {
                /*
                 * If using advanced index and there is no required fields - do not add to index.
                 * Skipping out of stock products if there are no prices for them in catalog_product_index_price table
                 */
                if ($this->_engine->allowAdvancedIndex() &&
                    (!isset($productData[$this->_engine->getFieldsPrefix() . 'categories']))) {
                    continue;
                }
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
        $store  = Mage::app()->getStore($storeId);
        $select = $this->_getWriteAdapter()->select()
            ->useStraightJoin(true)
            ->from(
                array('e' => $this->getTable('catalog/product')),
                array_merge(array('entity_id', 'type_id'), $staticFields))
            ->join(
                array('website' => $this->getTable('catalog/product_website')),
                $this->_getWriteAdapter()->quoteInto('website.product_id=e.entity_id AND website.website_id=?', $store->getWebsiteId()),
                array()
            )
            ->join(
                array('stock_status' => $this->getTable('cataloginventory/stock_status')),
                $this->_getWriteAdapter()->quoteInto('stock_status.product_id=e.entity_id AND stock_status.website_id=?', $store->getWebsiteId()),
                array('in_stock' => 'stock_status')
            );

        if (!is_null($productIds)) {
            $select->where('e.entity_id IN(?)', $productIds);
        }

        $select->where('e.entity_id>?', $lastProductId)
            ->limit($limit)
            ->order('e.entity_id');

        $result = $this->_getWriteAdapter()->fetchAll($select);
        if ($this->_engine && $this->_engine->allowAdvancedIndex() && count($result) > 0) {
            return $this->_engine->addAdvancedIndex($result, $storeId, $productIds);
        }
        else {
            return $result;
        }
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
            $this->_getWriteAdapter()->query('TRUNCATE TABLE ' . $this->getTable('catalogsearch/result'));

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
        if ($this->_engine) {
            $this->_engine->cleanIndex($storeId, $productId);
        }
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
                ':query' => $queryText
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
                    $like[] = '`s`.`data_index` LIKE :likew' . $likeI;
                    $bind[':likew' . $likeI] = '%' . $word . '%';
                    $likeI ++;
                }
                if ($like) {
                    $likeCond = '(' . join(' OR ', $like) . ')';
                }
            }
            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_FULLTEXT
                || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
                $fulltextCond = 'MATCH (`s`.`data_index`) AGAINST (:query IN BOOLEAN MODE)';
            }
            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE && $likeCond) {
                $separateCond = ' OR ';
            }

            $sql = sprintf("INSERT INTO `{$this->getTable('catalogsearch/result')}` "
                . "(SELECT STRAIGHT_JOIN '%d', `s`.`product_id`, MATCH (`s`.`data_index`) "
                . "AGAINST (:query IN BOOLEAN MODE) FROM `{$this->getMainTable()}` AS `s` "
                . "INNER JOIN `{$this->getTable('catalog/product')}` AS `e` "
                . "ON `e`.`entity_id`=`s`.`product_id` WHERE (%s%s%s) AND `s`.`store_id`='%d')"
                . " ON DUPLICATE KEY UPDATE `relevance`=VALUES(`relevance`)",
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

            $entityType   = $this->getEavConfig()->getEntityType('catalog_product');
            $entity       = $entityType->getEntity();

            $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->setEntityTypeFilter($entityType->getEntityTypeId());
            if ($this->_engine && $this->_engine->allowAdvancedIndex()) {
                $productAttributeCollection->addToIndexFilter(true);
            } else {
                $productAttributeCollection->addSearchableAttributeFilter();
            }
            $attributes = $productAttributeCollection->getItems();

            foreach ($attributes as $attribute) {
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
                $selects[] = $this->_getWriteAdapter()->select()
                    ->from(
                        array('t_default' => $tableName),
                        array('entity_id', 'attribute_id'))
                    ->joinLeft(
                        array('t_store' => $tableName),
                        $this->_getWriteAdapter()->quoteInto("t_default.entity_id=t_store.entity_id AND t_default.attribute_id=t_store.attribute_id AND t_store.store_id=?", $storeId),
                        array('value'=>'IF(t_store.value_id > 0, t_store.value, t_default.value)'))
                    ->where('t_default.store_id=?', 0)
                    ->where('t_default.attribute_id IN(?)', $attributeIds)
                    ->where('t_default.entity_id IN(?)', $productIds);
            }
        }

        if ($selects) {
            $select = '('.join(')UNION(', $selects).')';
            $query = $this->_getWriteAdapter()->query($select);
            while ($row = $query->fetch()) {
                $result[$row['entity_id']][$row['attribute_id']] = $row['value'];
            }
        }

        return $result;
    }

    /**
     * Retrieve Product Type Instance
     *
     * @param string $typeId
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    protected function _getProductTypeInstance($typeId)
    {
        if (!isset($this->_productTypes[$typeId])) {
            $productEmulator = $this->_getProductEmulator();
            $productEmulator->setTypeId($typeId);

            $this->_productTypes[$typeId] = Mage::getSingleton('catalog/product_type')
                ->factory($productEmulator);
        }
        return $this->_productTypes[$typeId];
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
        $typeInstance = $this->_getProductTypeInstance($typeId);
        $relation = $typeInstance->isComposite()
            ? $typeInstance->getRelationInfo()
            : false;

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
     * Retrieve Product Emulator (Varien Object)
     *
     * @return Varien_Object
     */
    protected function _getProductEmulator()
    {
        $productEmulator = new Varien_Object();
        $productEmulator->setIdFieldName('entity_id');
        return $productEmulator;
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

                    //For grouped products
                    if (isset($index[$attribute->getAttributeCode()])) {
                        if (!is_array($index[$attribute->getAttributeCode()])) {
                            $index[$attribute->getAttributeCode()] = array($index[$attribute->getAttributeCode()]);
                        }
                        $index[$attribute->getAttributeCode()][] = $value;
                    }
                    //For other types of products
                    else {
                        $index[$attribute->getAttributeCode()] = $value;
                    }
                }
            }
        }

        foreach ($indexData as $attributeData) {
            foreach ($attributeData as $attributeId => $attributeValue) {
                $value = $this->_getAttributeValue($attributeId, $attributeValue, $storeId);
                if (!is_null($value) && $value !== false) {
                    $code = $this->_getSearchableAttribute($attributeId)->getAttributeCode();
                    //For grouped products
                    if (isset($index[$code])) {
                        if (!is_array($index[$code])) {
                            $index[$code] = array($index[$code]);
                        }
                        $index[$code][] = $value;
                    }
                    //For other types of products
                    else {
                        $index[$code] = $value;
                    }
                }
            }
        }

        $product = $this->_getProductEmulator()
            ->setId($productData['entity_id'])
            ->setTypeId($productData['type_id'])
            ->setStoreId($storeId);
        $typeInstance = $this->_getProductTypeInstance($productData['type_id']);
        if ($data = $typeInstance->getSearchableData($product)) {
            $index['options'] = $data;
        }

        if (isset($productData['in_stock'])) {
            $index['in_stock'] = $productData['in_stock'];
        }

        if ($this->_engine) {
            if ($this->_engine->allowAdvancedIndex()) {
                $index += $this->_engine->addAllowedAdvancedIndexField($productData);
            }

            return $this->_engine->prepareEntityIndex($index, $this->_separator);
        }

        return Mage::helper('catalogsearch')->prepareIndexdata($index, $this->_separator);
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
        if (!($attribute->getIsSearchable() ||
            $attribute->getIsVisibleInAdvancedSearch() ||
            $attribute->getIsFilterable() ||
            $attribute->getIsFilterableInSearch())) {
            return null;
        }
        if ($attribute->usesSource()) {
            $attribute->setStoreId($storeId);
            $value = $attribute->getSource()->getOptionText($value);
        }
        if ($attribute->getBackendType() == 'datetime') {
            $value = $this->_getStoreDate($storeId, $value);
        }
        if ($attribute->getFrontend()->getInputType() == 'price') {
            $value = Mage::app()->getStore($storeId)->roundPrice($value);
        }

        if (is_array($value)) {
            $value = implode($this->_separator, $value);
        }

        return preg_replace("#\s+#si", ' ', trim(strip_tags($value)));
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
        if ($this->_engine) {
            $this->_engine->saveEntityIndex($productId, $storeId, $index);
        }
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
        if ($this->_engine) {
            $this->_engine->saveEntityIndexes($storeId, $productIndexes);
        }
        return $this;
    }

    /**
     * Retrieve Date value for store
     *
     * @param int $storeId
     * @param string $date
     * @return string
     */
    protected function _getStoreDate($storeId, $date = null)
    {
        if (!isset($this->_dates[$storeId])) {
            $timezone = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE, $storeId);
            $locale   = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId);
            $locale   = new Zend_Locale($locale);

            $dateObj = new Zend_Date(null, null, $locale);
            $dateObj->setTimezone($timezone);
            $this->_dates[$storeId] = array($dateObj, $locale->getTranslation(null, 'date', $locale));
        }

        if (!is_empty_date($date)) {
            list($dateObj, $format) = $this->_dates[$storeId];
            $dateObj->setDate($date, Varien_Date::DATETIME_INTERNAL_FORMAT);

            return $dateObj->toString($format);
        }

        return null;
    }
}
