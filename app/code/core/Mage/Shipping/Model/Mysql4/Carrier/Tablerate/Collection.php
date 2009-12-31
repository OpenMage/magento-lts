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
 * @package     Mage_Shipping
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shipping table rates collection
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shipping_Model_Mysql4_Carrier_Tablerate_Collection extends Varien_Data_Collection_Db
{
    protected $_shipTable;
    protected $_countryTable;
    protected $_regionTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('shipping_read'));
        $this->_shipTable = Mage::getSingleton('core/resource')->getTableName('shipping/tablerate');
        $this->_countryTable = Mage::getSingleton('core/resource')->getTableName('directory/country');
        $this->_regionTable = Mage::getSingleton('core/resource')->getTableName('directory/country_region');
        $this->_select->from(array("s" => $this->_shipTable))
            ->joinLeft(array("c" => $this->_countryTable), 'c.country_id = s.dest_country_id', 'iso3_code AS dest_country')
            ->joinLeft(array("r" => $this->_regionTable), 'r.region_id = s.dest_region_id', 'code AS dest_region')
            ->order(array("dest_country", "dest_region", "dest_zip"));
        $this->_setIdFieldName('pk');
        return $this;
    }

    public function setWebsiteFilter($websiteId)
    {
        $this->_select->where("website_id = ?", $websiteId);

        return $this;
    }

    public function setConditionFilter($conditionName)
    {
        $this->_select->where("condition_name = ?", $conditionName);

        return $this;
    }

    public function setCountryFilter($countryId)
    {
        $this->_select->where("dest_country_id = ?", $countryId);

        return $this;
    }
}
