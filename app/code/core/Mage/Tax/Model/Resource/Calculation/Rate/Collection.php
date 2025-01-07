<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax rate collection
 *
 * @category   Mage
 * @package    Mage_Tax
 *
 * @method Mage_Tax_Model_Calculation_Rate[] getItems()
 */
class Mage_Tax_Model_Resource_Calculation_Rate_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Value of fetched from DB of rules per cycle
     */
    public const TAX_RULES_CHUNK_SIZE = 1000;

    protected function _construct()
    {
        $this->_init('tax/calculation_rate');
    }

    /**
     * Join country table to result
     *
     * @return $this
     */
    public function joinCountryTable()
    {
        $this->_select->join(
            ['country_table' => $this->getTable('directory/country')],
            'main_table.tax_country_id = country_table.country_id',
            ['country_name' => 'iso2_code'],
        );

        return $this;
    }

    /**
     * Join Region Table
     *
     * @return $this
     */
    public function joinRegionTable()
    {
        $this->_select->joinLeft(
            ['region_table' => $this->getTable('directory/country_region')],
            'main_table.tax_region_id = region_table.region_id',
            ['region_name' => 'code'],
        );
        return $this;
    }

    /**
     * Join rate title for specified store
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return $this
     */
    public function joinTitle($store = null)
    {
        $storeId = (int) Mage::app()->getStore($store)->getId();
        $this->_select->joinLeft(
            ['title_table' => $this->getTable('tax/tax_calculation_rate_title')],
            $this->getConnection()->quoteInto('main_table.tax_calculation_rate_id = title_table.tax_calculation_rate_id'
                . ' AND title_table.store_id = ?', $storeId),
            ['title' => 'value'],
        );

        return $this;
    }

    /**
     * Joins store titles for rates
     *
     * @return $this
     */
    public function joinStoreTitles()
    {
        $storeCollection =  Mage::app()->getStores(true);
        foreach ($storeCollection as $store) {
            $tableAlias    = sprintf('title_table_%s', $store->getId());
            $joinCondition = implode(' AND ', [
                "main_table.tax_calculation_rate_id = {$tableAlias}.tax_calculation_rate_id",
                $this->getConnection()->quoteInto($tableAlias . '.store_id = ?', $store->getId()),
            ]);
            $this->_select->joinLeft(
                [$tableAlias => $this->getTable('tax/tax_calculation_rate_title')],
                $joinCondition,
                [$tableAlias => 'value'],
            );
        }
        return $this;
    }

    /**
     * Add rate filter
     *
     * @param int $rateId
     * @return $this
     */
    public function addRateFilter($rateId)
    {
        if (is_int($rateId) && $rateId > 0) {
            return $this->addFieldToFilter('main_table.tax_rate_id', $rateId);
        }

        return $this;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('tax_calculation_rate_id', 'code');
    }

    /**
     * Retrieve option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('tax_calculation_rate_id', 'code');
    }

    /**
     * Convert items array to hash for select options
     * unsing fetchItem method
     *
     * @see     _toOptionHashOptimized()
     *
     * @return  array
     */
    public function toOptionHashOptimized()
    {
        return $this->_toOptionHashOptimized('tax_calculation_rate_id', 'code');
    }

    /**
     * Get rates array without memory leak
     *
     * @return array
     */
    public function getOptionRates()
    {
        $size = self::TAX_RULES_CHUNK_SIZE;
        $page = 1;
        $rates = [];
        do {
            $offset = $size * ($page - 1);
            $this->getSelect()->reset();
            $this->getSelect()
                ->from(
                    ['rates' => $this->getMainTable()],
                    ['tax_calculation_rate_id', 'code'],
                )
                ->limit($size, $offset);

            $rates = array_merge($rates, $this->toOptionArray());
            $this->clear();
            $page++;
        } while ($this->getSize() > $offset);

        return $rates;
    }
}
