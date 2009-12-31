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


class Mage_CatalogSearch_Model_Mysql4_Search_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    protected $_attributesCollection;
    protected $_searchQuery;

    /**
     * Add search query filter
     *
     * @param   string $query
     * @return  Mage_CatalogSearch_Model_Mysql4_Search_Collection
     */
    public function addSearchFilter($query)
    {
        $this->_searchQuery = '%'.$query.'%';
        $this->addFieldToFilter('entity_id', array('in'=>new Zend_Db_Expr($this->_getSearchEntityIdsSql($query))));
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
            $this->_attributesCollection = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($this->getEntity()->getTypeId())
                ->load();

            foreach ($this->_attributesCollection as $attribute) {
                $attribute->setEntity($this->getEntity());
            }
        }
        return $this->_attributesCollection;
    }

    protected function _isAttributeTextAndSearchable($attribute)
    {
        if (($attribute->getIsSearchable() && !in_array($attribute->getFrontendInput(), array('select', 'multiselect')))
            && (in_array($attribute->getBackendType(), array('varchar', 'text')) || $attribute->getBackendType() == 'static')) {
            return true;
        }
        return false;
    }

    protected function _hasAttributeOptionsAndSearchable($attribute)
    {
        if ($attribute->getIsSearchable() && in_array($attribute->getFrontendInput(), array('select', 'multiselect'))) {
            return true;
        }

        return false;
    }

    protected function _getSearchEntityIdsSql($query)
    {
        $tables = array();
        $selects = array();
        /**
         * Collect tables and attribute ids of attributes with string values
         */
        foreach ($this->_getAttributesCollection() as $attribute) {
            if ($this->_isAttributeTextAndSearchable($attribute)) {
                $table = $attribute->getBackend()->getTable();
                if (!isset($tables[$table]) && $attribute->getBackendType() != 'static') {
                    $tables[$table] = array();
                }

                if ($attribute->getBackendType() == 'static') {
                    $param = $attribute->getAttributeCode().'_search_query';
                    $selects[] = $this->getConnection()->select()
                        ->from($table, 'entity_id')
                        ->where($attribute->getAttributeCode().' LIKE :'.$param);
                    $this->addBindParam($param, $this->_searchQuery);
                } else {
                    $tables[$table][] = $attribute->getId();
                }
            }
        }

        foreach ($tables as $table => $attributeIds) {
            $param = $table.'_search_query';
            $selects[] = $this->getConnection()->select()
                ->from(array('t1' => $table), 'entity_id')
                ->joinLeft(
                    array('t2' => $table),
                    $this->getConnection()->quoteInto('t1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id=?', $this->getStoreId()),
                    array()
                )
                ->where('t1.attribute_id IN (?)', $attributeIds)
                ->where('t1.store_id = ?', 0)
                ->where('IFNULL(t2.value, t1.value) LIKE :'.$param);
                $this->addBindParam($param, $this->_searchQuery);
        }

        if ($sql = $this->_getSearchInOptionSql($query)) {
            $selects[] = "SELECT * FROM ({$sql}) AS inoptionsql"; // inheritant unions may be inside
        }
        $sql = implode(' UNION ', $selects);
        return $sql;
    }

    /**
     * Retrieve SQL for search entities by option
     *
     * @param unknown_type $query
     * @return string
     */
    protected function _getSearchInOptionSql($query)
    {
        $attributeIds    = array();
        $attributeTables = array();
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
        $select = $this->getConnection()->select()
            ->from(array('default'=>$optionValueTable), array('option_id','option.attribute_id', 'store_id'=>'IFNULL(store.store_id, default.store_id)', 'a.frontend_input'))
            ->joinLeft(array('store'=>$optionValueTable),
                $this->getConnection()->quoteInto('store.option_id=default.option_id AND store.store_id=?', $storeId),
                array())
            ->join(array('option'=>$optionTable),
                'option.option_id=default.option_id',
                array())
            ->join(array('a' => $attributesTable), 'option.attribute_id=a.attribute_id', array())
            ->where('default.store_id=0')
            ->where('option.attribute_id IN (?)', $attributeIds)
            ->where('IFNULL(store.value, default.value) LIKE :search_query');
        $options = $this->getConnection()->fetchAll($select, array('search_query'=>$this->_searchQuery));
        if (empty($options)) {
            return false;
        }

        // build selects of entity ids for specified options ids by frontend input
        $select = array();
        foreach (array(
            'select'      => 'value = %d',
            'multiselect' => 'FIND_IN_SET(%d, value)')
            as $frontendInput => $condition) {
            if (isset($attributeTables[$frontendInput])) {
                $where = array();
                foreach ($options as $option) {
                    if ($frontendInput === $option['frontend_input']) {
                        $where[] = sprintf("attribute_id=%d AND store_id=%d AND {$condition}", $option['attribute_id'], $option['store_id'], $option['option_id']);
                    }
                }
                if ($where) {
                    $select[$frontendInput] = (string)$this->getConnection()->select()
                        ->from($attributeTables[$frontendInput], 'entity_id')
                        ->where(implode(' OR ', $where));
                }
            }
        }

        // search in catalogindex for products as part of configurable/grouped/bundle products (current store)
        $where = array();
        foreach ($options as $option) {
            $where[] = sprintf('attribute_id=%d AND value=%d', $option['attribute_id'], $option['option_id']);
        }
        if ($where) {
            $select[] = (string)$this->getConnection()->select()
                ->from($resource->getTableName('catalogindex/eav'), 'entity_id')
                ->where(implode(' OR ', $where))
                ->where("store_id={$storeId}");
        }

        return implode(' UNION ', $select);
    }
}
