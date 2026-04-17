<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer country attribute source
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Address_Attribute_Source_Country extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Retrieve all options
     *
     * @param  bool  $withEmpty     Argument has no effect, included for PHP 7.2 method signature compatibility
     * @param  bool  $defaultValues Argument has no effect, included for PHP 7.2 method signature compatibility
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('directory/country_collection')
                ->loadByStore($this->getAttribute()->getStoreId())->toOptionArray();
        }

        return $this->_options;
    }
}
