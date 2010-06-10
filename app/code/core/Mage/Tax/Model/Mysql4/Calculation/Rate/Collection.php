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
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax rate collection
 *
 * @category   Mage
 * @package    Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tax_Model_Mysql4_Calculation_Rate_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/calculation_rate');
    }

    public function joinCountryTable()
    {
        $this->_select->join(
            array('country_table' => $this->getTable('directory/country')),
            'main_table.tax_country_id=country_table.country_id',
            array('country_name' => 'iso2_code')
        );
        return $this;
    }

    /**
     * Join Region Table
     *
     * @return object
     */
    public function joinRegionTable()
    {
        $this->_select->joinLeft(
            array('region_table' => $this->getTable('directory/country_region')),
            'main_table.tax_region_id=region_table.region_id',
            array('region_name' => 'code')
        );
        return $this;
    }

    /**
     * Join rate title for specified store
     *
     * @param mixed store
     * @return object
     */
    public function joinTitle($store = null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        $this->_select->joinLeft(
            array('title_table' => $this->getTable('tax/tax_calculation_rate_title')),
            "main_table.tax_calculation_rate_id=title_table.tax_calculation_rate_id AND title_table.store_id = '{$storeId}'",
            array('title' => 'value')
        );
        return $this;
    }

    /**
     * Joins store titles for rates
     *
     * @return object
     */
    public function joinStoreTitles()
    {
        $storeCollection = Mage::getModel('core/store')->getCollection()->setLoadDefault(true);
        foreach ($storeCollection as $store) {
            $this->_select->joinLeft(
                array('title_table_' . $store->getId() => $this->getTable('tax/tax_calculation_rate_title')),
                "main_table.tax_calculation_rate_id=title_table_{$store->getId()}.tax_calculation_rate_id AND title_table_{$store->getId()}.store_id = '{$store->getId()}'",
                array('title_' . $store->getId() => 'value')
            );
        }
        return $this;
    }

    public function addRateFilter($rateId)
    {
        if (is_int($rateId) && $rateId > 0) {
            return $this->_select->where('main_table.tax_rate_id=?', $rateId);
        }
        else {
            return $this;
        }
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('tax_calculation_rate_id', 'code');
    }

    public function toOptionHash()
    {
        return $this->_toOptionHash('tax_calculation_rate_id', 'code');
    }

}
