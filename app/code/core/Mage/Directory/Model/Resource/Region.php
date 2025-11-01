<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Directory Region Resource Model
 *
 * @package    Mage_Directory
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
            ['lrn' => $this->_regionNameTable],
            "{$regionField} = lrn.region_id AND {$condition}",
            [],
        );

        if ($locale != $systemLocale) {
            $nameExpr  = $adapter->getCheckSql('lrn.region_id is null', 'srn.name', 'lrn.name');
            $condition = $adapter->quoteInto('srn.locale = ?', $systemLocale);
            $select->joinLeft(
                ['srn' => $this->_regionNameTable],
                "{$regionField} = srn.region_id AND {$condition}",
                ['name' => $nameExpr],
            );
        } else {
            $select->columns(['name'], 'lrn');
        }

        return $select;
    }

    /**
     * Load object by country id and code or default name
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $countryId
     * @param string $value
     * @param string $field
     *
     * @return $this
     */
    protected function _loadByCountry($object, $countryId, $value, $field)
    {
        $adapter        = $this->_getReadAdapter();
        $locale         = Mage::app()->getLocale()->getLocaleCode();
        $joinCondition  = $adapter->quoteInto('rname.region_id = region.region_id AND rname.locale = ?', $locale);
        $select         = $adapter->select()
            ->from(['region' => $this->getMainTable()])
            ->joinLeft(
                ['rname' => $this->_regionNameTable],
                $joinCondition,
                ['name'],
            )
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
     * @param string $regionCode
     * @param string $countryId
     * @return $this
     */
    public function loadByCode(Mage_Directory_Model_Region $region, $regionCode, $countryId)
    {
        return $this->_loadByCountry($region, $countryId, (string) $regionCode, 'code');
    }

    /**
     * Load data by country id and default region name
     *
     * @param string $regionName
     * @param string $countryId
     * @return $this
     */
    public function loadByName(Mage_Directory_Model_Region $region, $regionName, $countryId)
    {
        return $this->_loadByCountry($region, $countryId, (string) $regionName, 'default_name');
    }
}
