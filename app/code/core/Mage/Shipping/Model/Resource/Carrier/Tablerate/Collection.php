<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shipping table rates collection
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shipping_Model_Resource_Carrier_Tablerate_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * main table name
     *
     * @deprecated since 1.4.1.0
     * @var string
     */
    protected $_shipTable;

    /**
     * directory/country table name
     *
     * @var string
     */
    protected $_countryTable;

    /**
     * directory/country_region table name
     *
     * @var string
     */
    protected $_regionTable;

    /**
     * Define resource model and item
     *
     */
    protected function _construct()
    {
        $this->_init('shipping/carrier_tablerate');
        $this->_shipTable       = $this->getMainTable();
        $this->_countryTable    = $this->getTable('directory/country');
        $this->_regionTable     = $this->getTable('directory/country_region');
    }

    /**
     * Initialize select, add country iso3 code and region name
     *
     * @return $this
     */
    public function _initSelect()
    {
        parent::_initSelect();

        $this->_select
            ->joinLeft(
                ['country_table' => $this->_countryTable],
                'country_table.country_id = main_table.dest_country_id',
                ['dest_country' => 'iso3_code']
            )
            ->joinLeft(
                ['region_table' => $this->_regionTable],
                'region_table.region_id = main_table.dest_region_id',
                ['dest_region' => 'code']
            );

        $this->addOrder('dest_country', self::SORT_ORDER_ASC);
        $this->addOrder('dest_region', self::SORT_ORDER_ASC);
        $this->addOrder('dest_zip', self::SORT_ORDER_ASC);
        $this->addOrder('condition_value', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Add website filter to collection
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteFilter($websiteId)
    {
        return $this->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * Add condition name (code) filter to collection
     *
     * @param string $conditionName
     * @return $this
     */
    public function setConditionFilter($conditionName)
    {
        return $this->addFieldToFilter('condition_name', $conditionName);
    }

    /**
     * Add country filter to collection
     *
     * @param string $countryId
     * @return $this
     */
    public function setCountryFilter($countryId)
    {
        return $this->addFieldToFilter('dest_country_id', $countryId);
    }
}
