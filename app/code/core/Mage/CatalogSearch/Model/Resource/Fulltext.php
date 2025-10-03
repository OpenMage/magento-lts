<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogSearch
 */

/**
 * CatalogSearch Fulltext Index resource model
 *
 * @package    Mage_CatalogSearch
 */
class Mage_CatalogSearch_Model_Resource_Fulltext extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Searchable attributes cache
     *
     * @var array|null
     */
    protected $_searchableAttributes     = null;

    /**
     * Index values separator
     *
     * @var string
     */
    protected $_separator                = '|';

    /**
     * Array of Zend_Date objects per store
     *
     * @var array
     */
    protected $_dates                    = [];

    /**
     * Product Type Instances cache
     *
     * @var array
     */
    protected $_productTypes             = [];

    /**
     * Store search engine instance
     *
     * @var object
     */
    protected $_engine                   = null;

    /**
     * Whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     * @var bool
     */
    protected $_allowTableChanges       = true;

    /**
     * @var array
     */
    protected $_foundData = [];

    /**
     * Init resource model
     *
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
     * Regenerate search index for store(s)
     *
     * @param  int|null $storeId
     * @param  int|array|null $productIds
     * @return $this
     */
    public function rebuildIndex($storeId = null, $productIds = null)
    {
        if (is_null($storeId)) {
            $storeIds = array_keys(Mage::app()->getStores());
            foreach ($storeIds as $storeId) {
                $this->_rebuildStoreIndex($storeId, $productIds);
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
     * @return $this
     */
    protected function _rebuildStoreIndex($storeId, $productIds = null)
    {
        $this->cleanIndex($storeId, $productIds);

        // prepare searchable attributes
        $staticFields = [];
        foreach ($this->_getSearchableAttributes('static') as $attribute) {
            $staticFields[] = $attribute->getAttributeCode();
        }
        $dynamicFields = [
            'int'       => array_keys($this->_getSearchableAttributes('int')),
            'varchar'   => array_keys($this->_getSearchableAttributes('varchar')),
            'text'      => array_keys($this->_getSearchableAttributes('text')),
            'decimal'   => array_keys($this->_getSearchableAttributes('decimal')),
            'datetime'  => array_keys($this->_getSearchableAttributes('datetime')),
        ];

        // status and visibility filter
        $visibility     = $this->_getSearchableAttribute('visibility');
        $status         = $this->_getSearchableAttribute('status');
        $statusVals     = Mage::getSingleton('catalog/product_status')->getVisibleStatusIds();
        $allowedVisibilityValues = $this->_engine->getAllowedVisibility();

        $websiteId = Mage::app()->getStore($storeId)->getWebsite()->getId();
        $lastProductId = 0;
        while (true) {
            $products = $this->_getSearchableProducts($storeId, $staticFields, $productIds, $lastProductId);
            if (!$products) {
                break;
            }

            $productAttributes = [];
            $productRelations  = [];
            foreach ($products as $productData) {
                $lastProductId = $productData['entity_id'];
                $productAttributes[$productData['entity_id']] = $productData['entity_id'];
                $productChildren = $this->_getProductChildrenIds(
                    $productData['entity_id'],
                    $productData['type_id'],
                    $websiteId,
                );
                $productRelations[$productData['entity_id']] = $productChildren;
                if ($productChildren) {
                    foreach ($productChildren as $productChildId) {
                        $productAttributes[$productChildId] = $productChildId;
                    }
                }
            }

            $productIndexes    = [];
            $productAttributes = $this->_getProductAttributes($storeId, $productAttributes, $dynamicFields);
            foreach ($products as $productData) {
                if (!isset($productAttributes[$productData['entity_id']])) {
                    continue;
                }

                $productAttr = $productAttributes[$productData['entity_id']];
                if (!isset($productAttr[$visibility->getId()])
                    || !in_array($productAttr[$visibility->getId()], $allowedVisibilityValues)
                ) {
                    continue;
                }
                if (!isset($productAttr[$status->getId()]) || !in_array($productAttr[$status->getId()], $statusVals)) {
                    continue;
                }

                $productIndex = [
                    $productData['entity_id'] => $productAttr,
                ];

                $hasChildren = false;
                if ($productChildren = $productRelations[$productData['entity_id']]) {
                    foreach ($productChildren as $productChildId) {
                        if (isset($productAttributes[$productChildId])) {
                            $productChildAttr = $productAttributes[$productChildId];
                            if (!isset($productChildAttr[$status->getId()])
                                || !in_array($productChildAttr[$status->getId()], $statusVals)
                            ) {
                                continue;
                            }

                            $hasChildren = true;
                            $productIndex[$productChildId] = $productChildAttr;
                        }
                    }
                }
                if (!is_null($productChildren) && !$hasChildren) {
                    continue;
                }

                $index = $this->_prepareProductIndex($productIndex, $productData, $storeId);

                $productIndexes[$productData['entity_id']] = $index;
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
     * @param array|int $productIds
     * @param int $lastProductId
     * @param int $limit
     * @return array
     */
    protected function _getSearchableProducts(
        $storeId,
        array $staticFields,
        $productIds = null,
        $lastProductId = 0,
        $limit = 100
    ) {
        $websiteId      = Mage::app()->getStore($storeId)->getWebsiteId();
        $writeAdapter   = $this->_getWriteAdapter();

        $select = $writeAdapter->select()
            ->useStraightJoin(true)
            ->from(
                ['e' => $this->getTable('catalog/product')],
                array_merge(['entity_id', 'type_id'], $staticFields),
            )
            ->join(
                ['website' => $this->getTable('catalog/product_website')],
                $writeAdapter->quoteInto(
                    'website.product_id=e.entity_id AND website.website_id=?',
                    $websiteId,
                ),
                [],
            )
            ->join(
                ['stock_status' => $this->getTable('cataloginventory/stock_status')],
                $writeAdapter->quoteInto(
                    'stock_status.product_id=e.entity_id AND stock_status.website_id=?',
                    $websiteId,
                ),
                ['in_stock' => 'stock_status'],
            );

        if (!is_null($productIds)) {
            $select->where('e.entity_id IN(?)', $productIds);
        }

        $select->where('e.entity_id>?', $lastProductId)
            ->limit($limit)
            ->order('e.entity_id');

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('prepare_catalog_product_index_select', [
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('e.entity_id'),
            'website_field' => new Zend_Db_Expr('website.website_id'),
            'store_field'   => $storeId,
        ]);

        return $writeAdapter->fetchAll($select);
    }

    /**
     * Reset search results
     *
     * @return $this
     */
    public function resetSearchResults()
    {
        Mage::dispatchEvent('catalogsearch_reset_search_result');
        return $this;
    }

    /**
     * Delete search index data for store
     *
     * @param int $storeId Store View Id
     * @param int $productId Product Entity Id
     * @return $this
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
     * @return $this
     */
    public function prepareResult($object, $queryText, $query)
    {
        /** @var Mage_CatalogSearch_Model_Resource_Helper_Mysql4 $searchHelper */
        $searchHelper = Mage::getResourceHelper('catalogsearch');

        $adapter = $this->_getWriteAdapter();
        $searchType = $object->getSearchType($query->getStoreId());

        $preparedTerms = $searchHelper->prepareTerms($queryText, $query->getMaxQueryWords());

        $bind = [];
        $like = [];
        $likeCond = '';
        if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE
            || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE
        ) {
            $helper = Mage::getResourceHelper('core');
            $words = Mage::helper('core/string')->splitWords($queryText, true, $query->getMaxQueryWords());
            foreach ($words as $word) {
                $like[] = $helper->getCILike('s.data_index', $word, ['position' => 'any']);
            }

            if ($like) {
                $separator = Mage::getStoreConfig(Mage_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_SEPARATOR);
                $likeCond = '(' . implode(' ' . $separator . ' ', $like) . ')';
            }
        }

        $mainTableAlias = 's';
        $fields = ['product_id'];

        $select = $adapter->select()
            ->from([$mainTableAlias => $this->getMainTable()], $fields)
            ->joinInner(
                ['e' => $this->getTable('catalog/product')],
                'e.entity_id = s.product_id',
                [],
            )
            ->where($mainTableAlias . '.store_id = ?', (int) $query->getStoreId());

        $where = '';
        if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_FULLTEXT
            || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE
        ) {
            $bind[':query'] = implode(' ', $preparedTerms[0]);
            $where = $searchHelper->chooseFulltext($this->getMainTable(), $mainTableAlias, $select);
        }
        if ($likeCond != '' && $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
            $where .= ($where ? ' OR ' : '') . $likeCond;
        } elseif ($likeCond != '' && $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE) {
            $select->columns(['relevance' => new Zend_Db_Expr('0')]);
            $where = $likeCond;
        }

        if ($where != '') {
            $select->where($where);
        }

        $this->_foundData = $adapter->fetchPairs($select, $bind);

        return $this;
    }

    /**
     * Retrieve found data
     *
     * @return array
     */
    public function getFoundData()
    {
        return $this->_foundData;
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
     * Retrieve searchable attributes
     *
     * @param string $backendType
     * @return array
     */
    protected function _getSearchableAttributes($backendType = null)
    {
        if (is_null($this->_searchableAttributes)) {
            $this->_searchableAttributes = [];

            $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');

            if ($this->_engine && $this->_engine->allowAdvancedIndex()) {
                $productAttributeCollection->addToIndexFilter(true);
            } else {
                $productAttributeCollection->addSearchableAttributeFilter();
            }
            $attributes = $productAttributeCollection->getItems();

            Mage::dispatchEvent('catalogsearch_searchable_attributes_load_after', [
                'engine' => $this->_engine,
                'attributes' => $attributes,
            ]);

            $entity = $this->getEavConfig()
                ->getEntityType(Mage_Catalog_Model_Product::ENTITY)
                ->getEntity();

            foreach ($attributes as $attribute) {
                $attribute->setEntity($entity);
            }

            $this->_searchableAttributes = $attributes;
        }

        if (!is_null($backendType)) {
            $attributes = [];
            foreach ($this->_searchableAttributes as $attributeId => $attribute) {
                if ($attribute->getBackendType() == $backendType) {
                    $attributes[$attributeId] = $attribute;
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
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected function _getSearchableAttribute($attribute)
    {
        $attributes = $this->_getSearchableAttributes();
        if (is_numeric($attribute)) {
            if (isset($attributes[$attribute])) {
                return $attributes[$attribute];
            }
        } elseif (is_string($attribute)) {
            foreach ($attributes as $attributeModel) {
                if ($attributeModel->getAttributeCode() == $attribute) {
                    return $attributeModel;
                }
            }
        }

        return $this->getEavConfig()->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attribute);
    }

    /**
     * Returns expression for field unification
     *
     * @param string $field
     * @param string $backendType
     * @return string
     */
    protected function _unifyField($field, $backendType = 'varchar')
    {
        /** @var Mage_CatalogSearch_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('catalogsearch');

        if ($backendType === 'datetime') {
            $expr = $helper->castField(
                $this->_getReadAdapter()->getDateFormatSql($field, '%Y-%m-%d %H:%i:%s'),
            );
        } else {
            $expr = $helper->castField($field);
        }
        return $expr;
    }

    /**
     * Load product(s) attributes
     *
     * @param int $storeId
     * @return array
     */
    protected function _getProductAttributes($storeId, array $productIds, array $attributeTypes)
    {
        $result  = [];
        $selects = [];
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        $adapter = $this->_getWriteAdapter();
        $ifStoreValue = $adapter->getCheckSql('t_store.value_id > 0', 't_store.value', 't_default.value');
        foreach ($attributeTypes as $backendType => $attributeIds) {
            if ($attributeIds) {
                $tableName = $this->getTable(['catalog/product', $backendType]);
                $select = $adapter->select()
                    ->from(
                        ['t_default' => $tableName],
                        ['entity_id', 'attribute_id'],
                    )
                    ->joinLeft(
                        ['t_store' => $tableName],
                        $adapter->quoteInto(
                            't_default.entity_id=t_store.entity_id' .
                                ' AND t_default.attribute_id=t_store.attribute_id' .
                                ' AND t_store.store_id=?',
                            $storeId,
                        ),
                        ['value' => $this->_unifyField($ifStoreValue, $backendType)],
                    )
                    ->where('t_default.store_id=?', 0)
                    ->where('t_default.attribute_id IN (?)', $attributeIds)
                    ->where('t_default.entity_id IN (?)', $productIds);

                /**
                 * Add additional external limitation
                 */
                Mage::dispatchEvent('prepare_catalog_product_index_select', [
                    'select'        => $select,
                    'entity_field'  => new Zend_Db_Expr('t_default.entity_id'),
                    'website_field' => $websiteId,
                    'store_field'   => new Zend_Db_Expr('t_store.store_id'),
                ]);

                $selects[] = $select;
            }
        }

        if ($selects) {
            $select = $adapter->select()->union($selects, Zend_Db_Select::SQL_UNION_ALL);
            $query = $adapter->query($select);
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
     * @param int $productId
     * @param int $typeId
     * @param null|int $websiteId
     * @return array|null
     */
    protected function _getProductChildrenIds($productId, $typeId, $websiteId = null)
    {
        $typeInstance = $this->_getProductTypeInstance($typeId);
        $relation = $typeInstance->isComposite()
            ? $typeInstance->getRelationInfo()
            : false;

        if ($relation && $relation->getTable() && $relation->getParentFieldName() && $relation->getChildFieldName()) {
            $select = $this->_getReadAdapter()->select()
                ->from(
                    ['main' => $this->getTable($relation->getTable())],
                    [$relation->getChildFieldName()],
                )
                ->where("main.{$relation->getParentFieldName()} = ?", $productId);
            if (!is_null($relation->getWhere())) {
                $select->where($relation->getWhere());
            }

            Mage::dispatchEvent('prepare_product_children_id_list_select', [
                'select'        => $select,
                'entity_field'  => 'main.product_id',
                'website_field' => $websiteId,
            ]);

            return $this->_getReadAdapter()->fetchCol($select);
        }

        return null;
    }

    /**
     * Return all product children ids
     *
     * @param int $productId Product Entity Id
     * @param string $typeId Super Product Link Type
     * @return array|null
     */
    protected function _getProductChildIds($productId, $typeId)
    {
        return $this->_getProductChildrenIds($productId, $typeId);
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
     * @param int $storeId
     * @return string
     */
    protected function _prepareProductIndex($indexData, $productData, $storeId)
    {
        $index = [];

        foreach ($this->_getSearchableAttributes('static') as $attribute) {
            $attributeCode = $attribute->getAttributeCode();

            if (isset($productData[$attributeCode])) {
                $value = $this->_getAttributeValue($attribute->getId(), $productData[$attributeCode], $storeId);
                if ($value) {
                    //For grouped products
                    if (isset($index[$attributeCode])) {
                        if (!is_array($index[$attributeCode])) {
                            $index[$attributeCode] = [$index[$attributeCode]];
                        }
                        $index[$attributeCode][] = $value;
                    } else { //For other types of products
                        $index[$attributeCode] = $value;
                    }
                }
            }
        }

        foreach ($indexData as $entityId => $attributeData) {
            foreach ($attributeData as $attributeId => $attributeValue) {
                $value = $this->_getAttributeValue($attributeId, $attributeValue, $storeId);
                if (!is_null($value) && $value !== false) {
                    $attributeCode = $this->_getSearchableAttribute($attributeId)->getAttributeCode();

                    if (isset($index[$attributeCode])) {
                        $index[$attributeCode][$entityId] = $value;
                    } else {
                        $index[$attributeCode] = [$entityId => $value];
                    }
                }
            }
        }

        if (!$this->_engine->allowAdvancedIndex()) {
            $product = $this->_getProductEmulator()
                ->setId($productData['entity_id'])
                ->setTypeId($productData['type_id'])
                ->setStoreId($storeId);
            $typeInstance = $this->_getProductTypeInstance($productData['type_id']);
            if ($data = $typeInstance->getSearchableData($product)) {
                $index['options'] = $data;
            }
        }

        if (isset($productData['in_stock'])) {
            $index['in_stock'] = $productData['in_stock'];
        }

        if ($this->_engine) {
            return $this->_engine->prepareEntityIndex($index, $this->_separator);
        }

        return Mage::helper('catalogsearch')->prepareIndexdata($index, $this->_separator);
    }

    /**
     * Retrieve attribute source value for search
     *
     * @param int $attributeId
     * @param mixed $value
     * @param int $storeId
     * @return mixed
     */
    protected function _getAttributeValue($attributeId, $value, $storeId)
    {
        $attribute = $this->_getSearchableAttribute($attributeId);
        if (!$attribute->getIsSearchable()) {
            if ($this->_engine->allowAdvancedIndex()) {
                if ($attribute->getAttributeCode() === 'visibility') {
                    return $value;
                } elseif (!($attribute->getIsVisibleInAdvancedSearch()
                    || $attribute->getIsFilterable()
                    || $attribute->getIsFilterableInSearch()
                    || $attribute->getUsedForSortBy())
                ) {
                    return null;
                }
            } else {
                return null;
            }
        }

        if ($attribute->usesSource()) {
            if ($this->_engine->allowAdvancedIndex()) {
                return $value;
            }

            $attribute->setStoreId($storeId);
            $value = $attribute->getSource()->getIndexOptionText($value);

            if (is_array($value)) {
                $value = implode($this->_separator, $value);
            } elseif (empty($value)) {
                $inputType = $attribute->getFrontend()->getInputType();
                if ($inputType === 'select' || $inputType === 'multiselect') {
                    return null;
                }
            }
        } elseif ($attribute->getBackendType() === 'datetime') {
            $value = $this->_getStoreDate($storeId, $value);
        } else {
            $inputType = $attribute->getFrontend()->getInputType();
            if ($inputType === 'price') {
                $value = Mage::app()->getStore($storeId)->roundPrice($value);
            }
        }

        return $value === null ? '' : preg_replace("#\s+#siu", ' ', trim(strip_tags($value)));
    }

    /**
     * Save Product index
     *
     * @param int $productId
     * @param int $storeId
     * @param string $index
     * @return $this
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
     * @return $this
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
     * @return string|null
     */
    protected function _getStoreDate($storeId, $date = null)
    {
        if (!isset($this->_dates[$storeId])) {
            $timezone = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE, $storeId);
            $locale   = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId);
            $locale   = new Zend_Locale($locale);

            $dateObj = new Zend_Date(null, null, $locale);
            $dateObj->setTimezone($timezone);
            $this->_dates[$storeId] = [$dateObj, $locale::getTranslation(null, 'date', $locale)];
        }

        if (!is_empty_date($date)) {
            [$dateObj, $format] = $this->_dates[$storeId];
            $dateObj->setDate($date, Varien_Date::DATETIME_INTERNAL_FORMAT);

            return $dateObj->toString($format);
        }

        return null;
    }

    // Deprecated methods

    /**
     * Set whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     * @param bool $value
     * @return $this
     */
    public function setAllowTableChanges($value = true)
    {
        $this->_allowTableChanges = $value;
        return $this;
    }

    /**
     * Update category products indexes
     *
     * @deprecated after 1.6.2.0
     *
     * @param array $productIds
     * @param array $categoryIds
     * @return $this
     */
    public function updateCategoryIndex($productIds, $categoryIds)
    {
        return $this;
    }
}
