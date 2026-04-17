<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Directory country format resource model
 *
 * @package    Mage_Directory
 *
 * @extends Mage_Core_Model_Resource_Db_Collection_Abstract<Mage_Directory_Model_Country_Format>
 */
class Mage_Directory_Model_Resource_Country_Format_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('directory/country_format');
    }

    /**
     * Set country filter
     *
     * @param  Mage_Directory_Model_Country|string $country
     * @return $this
     */
    public function setCountryFilter($country)
    {
        $countryId = $country instanceof Mage_Directory_Model_Country ? $country->getId() : $country;

        return $this->addFieldToFilter('country_id', $countryId);
    }
}
