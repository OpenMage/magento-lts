<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * @param Mage_Directory_Model_Country $country
     * @param string $code
     *
     * @throws Mage_Core_Exception
     *
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
