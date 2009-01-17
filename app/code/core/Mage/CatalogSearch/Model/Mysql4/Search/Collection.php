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
        if (($attribute->getIsSearchable() && $attribute->getFrontendInput() != 'select')
            && (in_array($attribute->getBackendType(), array('varchar', 'text')) || $attribute->getBackendType() == 'static')) {
            return true;
        }
        return false;
    }

    protected function _hasAttributeOptionsAndSearchable($attribute)
    {
        if ($attribute->getIsSearchable() && $attribute->getFrontendInput() == 'select') {
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
        //echo "<pre>";
        foreach ($this->_getAttributesCollection() as $attribute) {
            if ($this->_isAttributeTextAndSearchable($attribute)) {
                //echo $attribute->getAttributeCode()."\n";
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
            $selects[] = $sql;
        }
        //die(print_r($selects));
        $sql = implode(' UNION ', $selects);
        return $sql;
    }

    /**
     * Retrieve SQL for search entities by option
     *
     * @param unknown_type $query
     * @return unknown
     */
    protected function _getSearchInOptionSql($query)
    {
        $attributeIds = array();
        $table = '';

        /**
         * Collect attributes with options
         */
        foreach ($this->_getAttributesCollection() as $attribute) {
            if ($this->_hasAttributeOptionsAndSearchable($attribute)) {
                $table = $attribute->getBackend()->getTable();
                $attributeIds[] = $attribute->getId();
            }
        }
        if (empty($attributeIds)) {
            return false;
        }

        $optionTable = Mage::getSingleton('core/resource')->getTableName('eav/attribute_option');
        $optionValueTable = Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value');

        /**
         * Select option Ids
         */
        $select = $this->getConnection()->select()
            ->from(array('default'=>$optionValueTable), array('option_id','option.attribute_id', 'store_id'=>'IFNULL(store.store_id, default.store_id)'))
            ->joinLeft(array('store'=>$optionValueTable),
                $this->getConnection()->quoteInto('store.option_id=default.option_id AND store.store_id=?', $this->getStoreId()),
                array())
            ->join(array('option'=>$optionTable),
                'option.option_id=default.option_id',
                array())
            ->where('default.store_id=0')
            ->where('option.attribute_id IN (?)', $attributeIds);

        $searchCondition = 'IFNULL(store.value, default.value) LIKE :search_query';
        $select->where($searchCondition);

        $options = $this->getConnection()->fetchAll($select, array('search_query'=>$this->_searchQuery));

        if (empty($options)) {
            return false;
        }

        $cond = array();
        foreach ($options as $option) {
            $cond[] = "attribute_id = '{$option['attribute_id']}' AND value = '{$option['option_id']}' AND store_id = '{$option['store_id']}'";
        }

        return $this->getConnection()->select()
            ->from($table, 'entity_id')
//            ->where('store_id=?', $this->getStoreId())
            ->where(implode(' OR ', $cond));
    }
}