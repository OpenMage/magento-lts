<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * @package    Mage_Directory
 *
 * @method Mage_Directory_Model_Resource_Region _getResource()
 * @method Mage_Directory_Model_Resource_Region getResource()
 * @method Mage_Directory_Model_Resource_Region_Collection getCollection()
 * @method Mage_Directory_Model_Resource_Region_Collection getResourceCollection()
 *
 * @method string getCode()
 * @method $this setCode(string $value)
 * @method string getCountryId()
 * @method $this setCountryId(string $value)
 * @method string getDefaultName()
 * @method $this setDefaultName(string $value)
 * @method int getRegionId()
 */
class Mage_Directory_Model_Region extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('directory/region');
    }

    /**
     * Retrieve region name
     *
     * If name is no declared, then default_name is used
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->getData('name');
        if (is_null($name)) {
            $name = $this->getData('default_name');
        }
        return $name;
    }

    /**
     * @param string $code
     * @param string $countryId
     * @return $this
     */
    public function loadByCode($code, $countryId)
    {
        if ($code) {
            $this->_getResource()->loadByCode($this, $code, $countryId);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param string $countryId
     * @return $this
     */
    public function loadByName($name, $countryId)
    {
        $this->_getResource()->loadByName($this, $name, $countryId);
        return $this;
    }
}
