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
 */
class Mage_Directory_Model_Resource_Country_Format_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('directory/country_format');
    }

    /**
     * Set country filter
     *
     * @param string|Mage_Directory_Model_Country $country
     * @return $this
     */
    public function setCountryFilter($country)
    {
        if ($country instanceof Mage_Directory_Model_Country) {
            $countryId = $country->getId();
        } else {
            $countryId = $country;
        }

        return $this->addFieldToFilter('country_id', $countryId);
    }
}
