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
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Search collection
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Resource_Search_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * Attribute collection
     *
     * @var Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    protected $_attributesCollection;

    /**
     * Search query
     *
     * @var string
     */
    protected $_searchQuery;

    /**
     * Add search query filter
     *
     * @param string $query
     * @return $this
     */
    public function addSearchFilter($query)
    {
        $this->_searchQuery = $query;
        $this->addFieldToFilter('entity_id', ['in'=>new Zend_Db_Expr($this->_getSearchEntityIdsSql($query))]);
        return $this;
    }

    /**
     * Retrieve collection of all attributes
     *
     * @return Varien_Data_Collection_Db
     */
    protected function _getAttributesCollection()
    {
        if (!$this->_attributesCollection) {
            $this->_attributesCollection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->load();

            foreach ($this->_attributesCollection as $attribute) {
                $attribute->setEntity($this->getEntity());
            }
        }
        return $this->_attributesCollection;
    }

    /**
     * Check attribute is Text and is Searchable
     *
     * @param Mage_Catalog_Model_Entity_Attribute $attribute
     * @return boolean
     */
    protected function _isAttributeTextAndSearchable($attribute)
    {
        if (($attribute->getIsSearchable()
            && !in_array($attribute->getFrontendInput(), ['select', 'multiselect']))
            && (in_array($attribute->getBackendType(), ['varchar', 'text'])
                || $attribute->getBackendType() == 'static')) {
            return true;
        }
        return false;
    }

    /**
     * Check attributes has options and searchable
     *
     * @param Mage_Catalog_Model_Entity_Attribute $attribute
     * @return boolean
     */
    protected function _hasAttributeOptionsAndSearchable($attribute)
    {
        if ($attribute->getIsSearchable()
            && in_array($attribute->getFrontendInput(), ['select', 'multiselect'])) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve SQL for search entities
     *
     * @param string $query
     * @return string
     */
    protected function _getSearchEntityIdsSql($query)
    {
        $tables = [];
        $selects = [];

        /** @var Mage_Core_Model_Resource_Helper_Abstract $resHelper */
        $resHelper = Mage::getResourceHelper('core');
        $likeOptions = ['position' => 'any'];

        /**
         * Collect tables and attribute ids of attributes with string values
         */
        foreach ($this->_getAttributesCollection() as $attribute) {
            /** @var Mage_Catalog_Model_Entity_Attribute $attribute */
            $attributeCode = $attribute->getAttributeCode();
            if ($this->_isAttributeTextAndSearchable($attribute)) {
                $table = $attribute->getBackendTable();
                if (!isset($tables[$table]) && $attribute->getBackendType() != 'static') {
                    $tables[$table] = [];
                }

                if ($attribute->getBackendType() == 'static') {
                    $selects[] = $this->getConnection()->select()
                        ->from($table, 'entity_id')
                        ->where($resHelper->getCILike($attributeCode, $this->_searchQuery, $likeOptions));
                } else {
                    $tables[$table][] = $attribute->getId();
                }
            }
        }

        $ifValueId = $this->getConnection()->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');
        foreach ($tables as $table => $attributeIds) {
            $selects[] = $this->getConnection()->select()
                ->from(['t1' => $table], 'entity_id')
                ->joinLeft(
                    ['t2' => $table],
                    $this->getConnection()->quoteInto(
                        't1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id = ?',
                        $this->getStoreId()),
                    []
                )
                ->where('t1.attribute_id IN (?)', $attributeIds)
                ->where('t1.store_id = ?', 0)
                ->where($resHelper->getCILike($ifValueId, $this->_searchQuery, $likeOptions));
        }

        $sql = $this->_getSearchInOptionSql($query);
        if ($sql) {
            $selects[] = "SELECT * FROM ({$sql}) AS inoptionsql"; // inheritant unions may be inside
        }

        $sql = $this->getConnection()->select()->union($selects, Zend_Db_Select::SQL_UNION_ALL);
        return $sql;
    }

    /**
     * Retrieve SQL for search entities by option
     *
     * @param string $query
     * @return string
     */
    protected function _getSearchInOptionSql($query)
    {
        $attributeIds    = [];
        $attributeTables = [];
        $storeId = (int)$this->getStoreId();

        /**
         * Collect attributes with options
         */
        foreach ($this->_getAttributesCollection() as $attribute) {
            if ($this->_hasAttributeOptionsAndSearchable($attribute)) {
                $attributeTables[$attribute->getFrontendInput()] = $attribute->getBackend()->getTable();
                $attributeIds[] = $attribute->getId();
            }
        }
        if (empty($attributeIds)) {
            return false;
        }

        $resource = Mage::getSingleton('core/resource');
        $optionTable      = $resource->getTableName('eav/attribute_option');
        $optionValueTable = $resource->getTableName('eav/attribute_option_value');
        $attributesTable  = $resource->getTableName('eav/attribute');

        /**
         * Select option Ids
         */
        $resHelper = Mage::getResourceHelper('core');
        $ifStoreId = $this->getConnection()->getIfNullSql('s.store_id', 'd.store_id');
        $ifValue   = $this->getConnection()->getCheckSql('s.value_id > 0', 's.value', 'd.value');
        $select = $this->getConnection()->select()
            ->from(['d'=>$optionValueTable],
                   ['option_id',
                         'o.attribute_id',
                         'store_id' => $ifStoreId,
                         'a.frontend_input'])
            ->joinLeft(['s'=>$optionValueTable],
                $this->getConnection()->quoteInto('s.option_id = d.option_id AND s.store_id=?', $storeId),
                [])
            ->join(['o'=>$optionTable],
                'o.option_id=d.option_id',
                [])
            ->join(['a' => $attributesTable], 'o.attribute_id=a.attribute_id', [])
            ->where('d.store_id=0')
            ->where('o.attribute_id IN (?)', $attributeIds)
            ->where($resHelper->getCILike($ifValue, $this->_searchQuery, ['position' => 'any']));

        $options = $this->getConnection()->fetchAll($select);
        if (empty($options)) {
            return false;
        }

        // build selects of entity ids for specified options ids by frontend input
        $selects = [];
        foreach ([
            'select'      => 'eq',
            'multiselect' => 'finset']
                 as $frontendInput => $condition) {
            if (isset($attributeTables[$frontendInput])) {
                $where = [];
                foreach ($options as $option) {
                    if ($frontendInput === $option['frontend_input']) {
                        $findSet = $this->getConnection()
                            ->prepareSqlCondition('value', [$condition => $option['option_id']]);
                        $whereCond = "(attribute_id=%d AND store_id=%d AND {$findSet})";
                        $where[] = sprintf($whereCond, $option['attribute_id'], $option['store_id']);
                    }
                }
                if ($where) {
                    $selects[$frontendInput] = (string)$this->getConnection()->select()
                        ->from($attributeTables[$frontendInput], 'entity_id')
                        ->where(implode(' OR ', $where));
                }
            }
        }

        // search in catalogindex for products as part of configurable/grouped/bundle products (current store)
        $where = [];
        foreach ($options as $option) {
            $where[] = sprintf('(attribute_id=%d AND value=%d)', $option['attribute_id'], $option['option_id']);
        }
        if ($where) {
            $selects[] = (string)$this->getConnection()->select()
                ->from($resource->getTableName('catalogindex/eav'), 'entity_id')
                ->where(implode(' OR ', $where))
                ->where("store_id={$storeId}");
        }
        return $this->getConnection()->select()->union($selects, Zend_Db_Select::SQL_UNION_ALL);
    }
}
