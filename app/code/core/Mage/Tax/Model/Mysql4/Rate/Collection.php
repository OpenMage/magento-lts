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
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax rate collection
 *
 * @category   Mage
 * @package    Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tax_Model_Mysql4_Rate_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/rate');
    }

    /**
     * Join Type Data
     *
     * @return object
     */
    public function joinTypeData()
    {
        $typeCollection = Mage::getModel('tax/rate_type')->getCollection();

        foreach($typeCollection as $type) {
            $typeId = (int) $type->getId();
            if (!$typeId) {
                continue;
            }
            $alias = 'trd_'.$typeId;
            $this->_select->joinLeft(
                array($alias => $this->getTable('tax/tax_rate_data')),
                "main_table.tax_rate_id={$alias}.tax_rate_id AND {$alias}.rate_type_id={$typeId}",
                array('rate_value_'.$type->getId() => 'rate_value')
            );
        }

        return $this;
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

    public function addRateFilter($rateId)
    {
        if (is_int($rateId) && $rateId > 0) {
            return $this->_select->where('main_table.tax_rate_id=?', $rateId);
        }
        else {
            return $this;
        }
    }
}