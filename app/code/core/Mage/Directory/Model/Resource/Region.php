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
 * @package     Mage_Directory
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Directory Region Resource Model
 *
 * @category    Mage
 * @package     Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Resource_Region extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Table with localized region names
     *
     * @var string
     */
    protected $_regionNameTable;

    /**
     * Define main and locale region name tables
     *
     */
    protected function _construct()
    {
        $this->_init('directory/country_region', 'region_id');
        $this->_regionNameTable = $this->getTable('directory/country_region_name');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * 
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select  = parent::_getLoadSelect($field, $value, $object);
        $adapter = $this->_getReadAdapter();

        $locale       = Mage::app()->getLocale()->getLocaleCode();
        $systemLocale = Mage::app()->getDistroLocaleCode();

        $regionField = $adapter->quoteIdentifier($this->getMainTable() . '.' . $this->getIdFieldName());

        $condition = $adapter->quoteInto('lrn.locale = ?', $locale);
        $select->joinLeft(
            array('lrn' => $this->_regionNameTable),
            "{$regionField} = lrn.region_id AND {$condition}",
            array());

        if ($locale != $systemLocale) {
            $nameExpr  = $adapter->getCheckSql('lrn.region_id is null', 'srn.name', 'lrn.name');
            $condition = $adapter->quoteInto('srn.locale = ?', $systemLocale);
            $select->joinLeft(
                array('srn' => $this->_regionNameTable),
                "{$regionField} = srn.region_id AND {$condition}",
                array('name' => $nameExpr));
        } else {
            $select->columns(array('name'), 'lrn');
        }

        return $select;
    }

    /**
     * Load object by country id and code or default name
     *
     * @param Mage_Core_Model_Abstract $object
     * @param int $countryId
     * @param string $value
     * @param string $field
     * 
     * @return Mage_Directory_Model_Resource_Region
     */
    protected function _loadByCountry($object, $countryId, $value, $field)
    {
        $adapter        = $this->_getReadAdapter();
        $locale         = Mage::app()->getLocale()->getLocaleCode();
        $joinCondition  = $adapter->quoteInto('rname.region_id = region.region_id AND rname.locale = ?', $locale);
        $select         = $adapter->select()
            ->from(array('region' => $this->getMainTable()))
            ->joinLeft(
                array('rname' => $this->_regionNameTable),
                $joinCondition,
                array('name'))
            ->where('region.country_id = ?', $countryId)
            ->where("region.{$field} = ?", $value);

        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Loads region by region code and country id
     *
     * @param Mage_Directory_Model_Region $region
     * @param string $regionCode
     * @param string $countryId
     *
     * @return Mage_Directory_Model_Resource_Region
     */
    public function loadByCode(Mage_Directory_Model_Region $region, $regionCode, $countryId)
    {
        return $this->_loadByCountry($region, $countryId, (string)$regionCode, 'code');
    }

    /**
     * Load data by country id and default region name
     *
     * @param Mage_Directory_Model_Region $region
     * @param string $regionName
     * @param string $countryId
     * 
     * @return Mage_Directory_Model_Resource_Region
     */
    public function loadByName(Mage_Directory_Model_Region $region, $regionName, $countryId)
    {
        return $this->_loadByCountry($region, $countryId, (string)$regionName, 'default_name');
    }
}
