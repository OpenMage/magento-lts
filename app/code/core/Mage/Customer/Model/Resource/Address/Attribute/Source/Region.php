<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Customer
 */

/**
 * Customer region attribute source
 *
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Address_Attribute_Source_Region extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Retrieve all region options
     *
     * @param bool $withEmpty       Argument has no effect, included for PHP 7.2 method signature compatibility
     * @param bool $defaultValues   Argument has no effect, included for PHP 7.2 method signature compatibility
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('directory/region_collection')->load()->toOptionArray();
        }
        return $this->_options;
    }
}
