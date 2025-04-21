<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/**
 * Directory country format model
 *
 * @package    Mage_Directory
 *
 * @method Mage_Directory_Model_Resource_Country_Format _getResource()
 * @method Mage_Directory_Model_Resource_Country_Format getResource()
 * @method Mage_Directory_Model_Resource_Country_Format_Collection getCollection()
 * @method string getCountryId()
 * @method $this setCountryId(string $value)
 * @method string getType()
 * @method $this setType(string $value)
 * @method string getFormat()
 * @method $this setFormat(string $value)
 */
class Mage_Directory_Model_Country_Format extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('directory/country_format');
    }
}
