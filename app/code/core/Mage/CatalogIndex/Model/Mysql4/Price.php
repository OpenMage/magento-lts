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
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Price index resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Mysql4_Price extends Mage_CatalogIndex_Model_Mysql4_Abstract
{
    protected $_rate = 1;
    protected $_taxRates = null;

    protected function _construct()
    {
        $this->_init('catalogindex/price', 'index_id');
    }

    public function setRate($rate)
    {
        $this->_rate = $rate;
    }

    public function getRate()
    {
        if (!$this->_rate) {
            $this->_rate = 1;
        }
        return $this->_rate;
    }

    public function setCustomerGroupId($customerGroupId)
    {
        $this->_customerGroupId = $customerGroupId;
    }

    public function getCustomerGroupId()
    {
        return $this->_customerGroupId;
    }

    protected function _getTaxRateConditions($tableName = 'main_table')
    {
        return Mage::helper('tax')->getPriceTaxSql($tableName . '.value', 'IFNULL(tax_class_c.value, tax_class_d.value)');
    }

    public function getMaxValue($attribute = null, $entitySelect)
    {
        $select = clone $entitySelect;
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $select->from('', "MAX(price_table.value{$this->_getTaxRateConditions('price_table')})")
            ->join(array('price_table'=>$this->getMainTable()), 'price_table.entity_id=e.entity_id', array())
            ->where('price_table.store_id = ?', $this->getStoreId())
            ->where('price_table.attribute_id = ?', $attribute->getId());
        Mage::helper('tax')->joinTaxClass($select, $this->getStoreId(), 'price_table');

        if ($attribute->getAttributeCode() == 'price') {
            $select->where('price_table.customer_group_id = ?', $this->getCustomerGroupId());
        }

        return $this->_getReadAdapter()->fetchOne($select)*$this->getRate();
    }

//    public function getCount($range, $attribute, $entityIdsFilter)
//    {
//        $select = $this->_getReadAdapter()->select();
//
//        $fields = array('count'=>'COUNT(DISTINCT main_table.entity_id)', 'range'=>"FLOOR(((main_table.value{$this->_getTaxRateConditions()})*{$this->getRate()})/{$range})+1");
//
//        $select->from(array('main_table'=>$this->getMainTable()), $fields)
//            ->group('range')
//            ->where('main_table.entity_id in (?)', $entityIdsFilter)
//            ->where('main_table.store_id = ?', $this->getStoreId())
//            ->where('main_table.attribute_id = ?', $attribute->getId());
//        $this->_joinTaxClass($select);
//
//        if ($attribute->getAttributeCode() == 'price')
//            $select->where('main_table.customer_group_id = ?', $this->getCustomerGroupId());
//
//        $result = $this->_getReadAdapter()->fetchAll($select);
//
//        $counts = array();
//        foreach ($result as $row) {
//            $counts[$row['range']] = $row['count'];
//        }
//
//        return $counts;
//    }

    public function getCount($range, $attribute, $entitySelect)
    {
        $select = clone $entitySelect;
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $fields = array('count'=>'COUNT(DISTINCT price_table.entity_id)', 'range'=>"FLOOR(((price_table.value{$this->_getTaxRateConditions('price_table')})*{$this->getRate()})/{$range})+1");

        $select->from('', $fields)
            ->join(array('price_table'=>$this->getMainTable()), 'price_table.entity_id=e.entity_id', array())
            ->group('range')
            ->where('price_table.store_id = ?', $this->getStoreId())
            ->where('price_table.attribute_id = ?', $attribute->getId());
        Mage::helper('tax')->joinTaxClass($select, $this->getStoreId(), 'price_table');

        if ($attribute->getAttributeCode() == 'price')
            $select->where('price_table.customer_group_id = ?', $this->getCustomerGroupId());

        $result = $this->_getReadAdapter()->fetchAll($select);

        $counts = array();
        foreach ($result as $row) {
            $counts[$row['range']] = $row['count'];
        }

        return $counts;
    }

    public function getFilteredEntities($range, $index, $attribute, $entityIdsFilter, $tableName = 'price_table')
    {
        $select = $this->_getReadAdapter()->select();

        $select->from(array($tableName=>$this->getMainTable()), $tableName . '.entity_id')
            ->distinct(true)
            ->where($tableName . '.entity_id in (?)', $entityIdsFilter)
            ->where($tableName . '.store_id = ?', $this->getStoreId())
            ->where($tableName . '.attribute_id = ?', $attribute->getId());

        Mage::helper('tax')->joinTaxClass($select, $this->getStoreId(), $tableName);
        if ($attribute->getAttributeCode() == 'price')
            $select->where($tableName . '.customer_group_id = ?', $this->getCustomerGroupId());

        $select->where("(({$tableName}.value{$this->_getTaxRateConditions($tableName)})*{$this->getRate()}) >= ?", ($index-1)*$range);
        $select->where("(({$tableName}.value{$this->_getTaxRateConditions($tableName)})*{$this->getRate()}) < ?", $index*$range);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    public function getMinimalPrices($ids)
    {
        if (!$ids) {
            return array();
        }
        $select = $this->_getReadAdapter()->select();
        $select->from(array('price_table'=>$this->getTable('catalogindex/minimal_price')), array('price_table.entity_id', 'value'=>"(price_table.value)", 'tax_class_id'=>'(price_table.tax_class_id)'))
            ->where('price_table.entity_id in (?)', $ids)
            ->where('price_table.store_id = ?', $this->getStoreId())
            ->where('price_table.customer_group_id = ?', $this->getCustomerGroupId());
        return $this->_getReadAdapter()->fetchAll($select);
    }
}