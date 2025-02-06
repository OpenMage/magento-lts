<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Directory
 */

/**
 * Directory Country Resource Model
 *
 * @category   Mage
 * @package    Mage_Directory
 */
class Mage_Directory_Model_Resource_Country extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('directory/country', 'country_id');
    }

    /**
     * Load country by ISO code
     *
     * @param string $code
     *
     * @throws Mage_Core_Exception
     * @return $this
     */
    public function loadByCode(Mage_Directory_Model_Country $country, $code)
    {
        switch (strlen($code)) {
            case 2:
                $field = 'iso2_code';
                break;

            case 3:
                $field = 'iso3_code';
                break;

            default:
                Mage::throwException(Mage::helper('directory')->__('Invalid country code: %s', $code));
        }

        return $this->load($country, $code, $field);
    }
}
