<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Price index resource model
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Resource_Price extends Mage_CatalogIndex_Model_Resource_Abstract
{
    /**
     * @var float
     */
    protected $_rate               = 1;

    /**
     * @var int
     */
    protected $_customerGroupId;

    /**
     * @var array
     */
    protected $_taxRates           = null;

    protected function _construct()
    {
        $this->_init('catalogindex/price', 'index_id');
    }

    /**
     * @param float $rate
     */
    public function setRate($rate)
    {
        $this->_rate = $rate;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        if (!$this->_rate) {
            $this->_rate = 1;
        }
        return $this->_rate;
    }

    /**
     * @param int $customerGroupId
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->_customerGroupId = $customerGroupId;
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->_customerGroupId;
    }

    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param Zend_Db_Select $entitySelect
     * @return float|int
     */
    public function getMaxValue($attribute, $entitySelect)
    {
        $select = clone $entitySelect;
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $response = new Varien_Object();
        $response->setAdditionalCalculations([]);

        $select->join(['price_table' => $this->getMainTable()], 'price_table.entity_id=e.entity_id', []);

        if ($attribute->getAttributeCode() == 'price') {
            $select->where('price_table.customer_group_id = ?', $this->getCustomerGroupId());
            $args = [
                'select' => $select,
                'table' => 'price_table',
                'store_id' => $this->getStoreId(),
                'response_object' => $response,
            ];
            Mage::dispatchEvent('catalogindex_prepare_price_select', $args);
        }

        $select
            ->columns('MAX(price_table.value' . implode('', $response->getAdditionalCalculations()) . ')')
            ->where('price_table.website_id = ?', $this->getWebsiteId())
            ->where('price_table.attribute_id = ?', $attribute->getId());

        return $this->_getReadAdapter()->fetchOne($select) * $this->getRate();
    }

    /**
     * @param int $range
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param Zend_Db_Select $entitySelect
     * @return array
     */
    public function getCount($range, $attribute, $entitySelect)
    {
        $select = clone $entitySelect;
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $select->join(['price_table' => $this->getMainTable()], 'price_table.entity_id=e.entity_id', []);
        $response = new Varien_Object();
        $response->setAdditionalCalculations([]);

        if ($attribute->getAttributeCode() == 'price') {
            $select->where('price_table.customer_group_id = ?', $this->getCustomerGroupId());
            $args = [
                'select' => $select,
                'table' => 'price_table',
                'store_id' => $this->getStoreId(),
                'response_object' => $response,
            ];
            Mage::dispatchEvent('catalogindex_prepare_price_select', $args);
        }

        $fields = ['count' => 'COUNT(DISTINCT price_table.entity_id)', 'range' => 'FLOOR(((price_table.value' . implode('', $response->getAdditionalCalculations()) . ")*{$this->getRate()})/{$range})+1"];

        $select->columns($fields)
            ->group('range')
            ->where('price_table.website_id = ?', $this->getWebsiteId())
            ->where('price_table.attribute_id = ?', $attribute->getId());

        $result = $this->_getReadAdapter()->fetchAll($select);

        $counts = [];
        foreach ($result as $row) {
            $counts[$row['range']] = $row['count'];
        }

        return $counts;
    }

    /**
     * @param int $range
     * @param int $index
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param array $entityIdsFilter
     * @param string $tableName
     * @return array
     */
    public function getFilteredEntities($range, $index, $attribute, $entityIdsFilter, $tableName = 'price_table')
    {
        $select = $this->_getReadAdapter()->select();
        $select->from([$tableName => $this->getMainTable()], $tableName . '.entity_id');

        $response = new Varien_Object();
        $response->setAdditionalCalculations([]);

        $select
            ->distinct(true)
            ->where($tableName . '.entity_id in (?)', $entityIdsFilter)
            ->where($tableName . '.website_id = ?', $this->getWebsiteId())
            ->where($tableName . '.attribute_id = ?', $attribute->getId());

        if ($attribute->getAttributeCode() == 'price') {
            $select->where($tableName . '.customer_group_id = ?', $this->getCustomerGroupId());
            $args = [
                'select' => $select,
                'table' => $tableName,
                'store_id' => $this->getStoreId(),
                'response_object' => $response,
            ];
            Mage::dispatchEvent('catalogindex_prepare_price_select', $args);
        }

        $select->where("(({$tableName}.value" . implode('', $response->getAdditionalCalculations()) . ")*{$this->getRate()}) >= ?", ($index - 1) * $range);
        $select->where("(({$tableName}.value" . implode('', $response->getAdditionalCalculations()) . ")*{$this->getRate()}) < ?", $index * $range);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param int $range
     * @param int $index
     * @param string $tableName
     * @return $this
     */
    public function applyFilterToCollection($collection, $attribute, $range, $index, $tableName = 'price_table')
    {
        /**
         * Distinct required for removing duplicates in case when we have grouped products
         * which contain multiple rows for one product id
         */
        $collection->getSelect()->distinct(true);
        $tableName = $tableName . '_' . $attribute->getAttributeCode();
        $collection->getSelect()->joinLeft(
            [$tableName => $this->getMainTable()],
            $tableName . '.entity_id=e.entity_id',
            [],
        );

        $response = new Varien_Object();
        $response->setAdditionalCalculations([]);

        $collection->getSelect()
            ->where($tableName . '.website_id = ?', $this->getWebsiteId())
            ->where($tableName . '.attribute_id = ?', $attribute->getId());

        if ($attribute->getAttributeCode() == 'price') {
            $collection->getSelect()->where($tableName . '.customer_group_id = ?', $this->getCustomerGroupId());
            $args = [
                'select' => $collection->getSelect(),
                'table' => $tableName,
                'store_id' => $this->getStoreId(),
                'response_object' => $response,
            ];

            Mage::dispatchEvent('catalogindex_prepare_price_select', $args);
        }

        $collection->getSelect()->where("(({$tableName}.value" . implode('', $response->getAdditionalCalculations()) . ")*{$this->getRate()}) >= ?", ($index - 1) * $range);
        $collection->getSelect()->where("(({$tableName}.value" . implode('', $response->getAdditionalCalculations()) . ")*{$this->getRate()}) < ?", $index * $range);

        return $this;
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getMinimalPrices($ids)
    {
        if (!$ids) {
            return [];
        }
        $select = $this->_getReadAdapter()->select();
        $select->from(
            ['price_table' => $this->getTable('catalogindex/minimal_price')],
            ['price_table.entity_id', 'value' => '(price_table.value)', 'tax_class_id' => '(price_table.tax_class_id)'],
        )
            ->where('price_table.entity_id in (?)', $ids)
            ->where('price_table.website_id = ?', $this->getWebsiteId())
            ->where('price_table.customer_group_id = ?', $this->getCustomerGroupId());
        return $this->_getReadAdapter()->fetchAll($select);
    }
}
