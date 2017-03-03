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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Price index resource model
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Resource_Price extends Mage_CatalogIndex_Model_Resource_Abstract
{
    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_rate               = 1;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_customerGroupId;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_taxRates           = null;

    /**
     * Enter description here ...
     *
     */
    protected function _construct()
    {
        $this->_init('catalogindex/price', 'index_id');
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $rate
     */
    public function setRate($rate)
    {
        $this->_rate = $rate;
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getRate()
    {
        if (!$this->_rate) {
            $this->_rate = 1;
        }
        return $this->_rate;
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $customerGroupId
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->_customerGroupId = $customerGroupId;
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getCustomerGroupId()
    {
        return $this->_customerGroupId;
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $attribute
     * @param unknown_type $entitySelect
     * @return unknown
     */
    public function getMaxValue($attribute, $entitySelect)
    {
        $select = clone $entitySelect;
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $response = new Varien_Object();
        $response->setAdditionalCalculations(array());

        $select->join(array('price_table'=>$this->getMainTable()), 'price_table.entity_id=e.entity_id', array());

        if ($attribute->getAttributeCode() == 'price') {
            $select->where('price_table.customer_group_id = ?', $this->getCustomerGroupId());
            $args = array(
                'select'=>$select,
                'table'=>'price_table',
                'store_id'=>$this->getStoreId(),
                'response_object'=>$response,
            );
            Mage::dispatchEvent('catalogindex_prepare_price_select', $args);
        }

        $select
            ->columns("MAX(price_table.value".implode('', $response->getAdditionalCalculations()).")")
            ->where('price_table.website_id = ?', $this->getWebsiteId())
            ->where('price_table.attribute_id = ?', $attribute->getId());

        return $this->_getReadAdapter()->fetchOne($select)*$this->getRate();
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $range
     * @param unknown_type $attribute
     * @param unknown_type $entitySelect
     * @return unknown
     */
    public function getCount($range, $attribute, $entitySelect)
    {
        $select = clone $entitySelect;
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $select->join(array('price_table'=>$this->getMainTable()), 'price_table.entity_id=e.entity_id', array());
        $response = new Varien_Object();
        $response->setAdditionalCalculations(array());

        if ($attribute->getAttributeCode() == 'price') {
            $select->where('price_table.customer_group_id = ?', $this->getCustomerGroupId());
            $args = array(
                'select'=>$select,
                'table'=>'price_table',
                'store_id'=>$this->getStoreId(),
                'response_object'=>$response,
            );
            Mage::dispatchEvent('catalogindex_prepare_price_select', $args);
        }


        $fields = array('count'=>'COUNT(DISTINCT price_table.entity_id)', 'range'=>"FLOOR(((price_table.value".implode('', $response->getAdditionalCalculations()).")*{$this->getRate()})/{$range})+1");

        $select->columns($fields)
            ->group('range')
            ->where('price_table.website_id = ?', $this->getWebsiteId())
            ->where('price_table.attribute_id = ?', $attribute->getId());


        $result = $this->_getReadAdapter()->fetchAll($select);

        $counts = array();
        foreach ($result as $row) {
            $counts[$row['range']] = $row['count'];
        }

        return $counts;
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $range
     * @param unknown_type $index
     * @param unknown_type $attribute
     * @param unknown_type $entityIdsFilter
     * @param unknown_type $tableName
     * @return unknown
     */
    public function getFilteredEntities($range, $index, $attribute, $entityIdsFilter, $tableName = 'price_table')
    {
        $select = $this->_getReadAdapter()->select();
        $select->from(array($tableName=>$this->getMainTable()), $tableName . '.entity_id');

        $response = new Varien_Object();
        $response->setAdditionalCalculations(array());

        $select
            ->distinct(true)
            ->where($tableName . '.entity_id in (?)', $entityIdsFilter)
            ->where($tableName . '.website_id = ?', $this->getWebsiteId())
            ->where($tableName . '.attribute_id = ?', $attribute->getId());

        if ($attribute->getAttributeCode() == 'price') {
            $select->where($tableName . '.customer_group_id = ?', $this->getCustomerGroupId());
            $args = array(
                'select'=>$select,
                'table'=>$tableName,
                'store_id'=>$this->getStoreId(),
                'response_object'=>$response,
            );
            Mage::dispatchEvent('catalogindex_prepare_price_select', $args);
        }

        $select->where("(({$tableName}.value".implode('', $response->getAdditionalCalculations()).")*{$this->getRate()}) >= ?", ($index-1)*$range);
        $select->where("(({$tableName}.value".implode('', $response->getAdditionalCalculations()).")*{$this->getRate()}) < ?", $index*$range);


        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $collection
     * @param unknown_type $attribute
     * @param unknown_type $range
     * @param unknown_type $index
     * @param unknown_type $tableName
     * @return Mage_CatalogIndex_Model_Resource_Price
     */
    public function applyFilterToCollection($collection, $attribute, $range, $index, $tableName = 'price_table')
    {
        /**
         * Distinct required for removing duplicates in case when we have grouped products
         * which contain multiple rows for one product id
         */
        $collection->getSelect()->distinct(true);
        $tableName = $tableName.'_'.$attribute->getAttributeCode();
        $collection->getSelect()->joinLeft(
            array($tableName => $this->getMainTable()),
            $tableName .'.entity_id=e.entity_id',
            array()
        );

        $response = new Varien_Object();
        $response->setAdditionalCalculations(array());

        $collection->getSelect()
            ->where($tableName . '.website_id = ?', $this->getWebsiteId())
            ->where($tableName . '.attribute_id = ?', $attribute->getId());

        if ($attribute->getAttributeCode() == 'price') {
            $collection->getSelect()->where($tableName . '.customer_group_id = ?', $this->getCustomerGroupId());
            $args = array(
                'select'=>$collection->getSelect(),
                'table'=>$tableName,
                'store_id'=>$this->getStoreId(),
                'response_object'=>$response,
            );

            Mage::dispatchEvent('catalogindex_prepare_price_select', $args);
        }

        $collection->getSelect()->where("(({$tableName}.value".implode('', $response->getAdditionalCalculations()).")*{$this->getRate()}) >= ?", ($index-1)*$range);
        $collection->getSelect()->where("(({$tableName}.value".implode('', $response->getAdditionalCalculations()).")*{$this->getRate()}) < ?", $index*$range);

        return $this;
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $ids
     * @return unknown
     */
    public function getMinimalPrices($ids)
    {
        if (!$ids) {
            return array();
        }
        $select = $this->_getReadAdapter()->select();
        $select->from(array('price_table'=>$this->getTable('catalogindex/minimal_price')),
            array('price_table.entity_id', 'value'=>"(price_table.value)", 'tax_class_id'=>'(price_table.tax_class_id)'))
            ->where('price_table.entity_id in (?)', $ids)
            ->where('price_table.website_id = ?', $this->getWebsiteId())
            ->where('price_table.customer_group_id = ?', $this->getCustomerGroupId());
        return $this->_getReadAdapter()->fetchAll($select);
    }
}
