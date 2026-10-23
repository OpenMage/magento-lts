<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Directory Country Resource Model
 *
 * @package    Mage_Directory
 */
class Mage_Directory_Model_Resource_Country extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('directory/country', 'country_id');
    }

    /**
     * Load country by ISO code
     *
     * @param string $code
     *
     * @return $this
     * @throws Mage_Core_Exception
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
