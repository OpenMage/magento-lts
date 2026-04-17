<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * EAV Entity Attribute Boolean Data Model
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Attribute_Data_Boolean extends Mage_Eav_Model_Attribute_Data_Select
{
    /**
     * Return a text for option value
     *
     * @param  int    $value
     * @return string
     */
    protected function _getOptionText($value)
    {
        return match ($value) {
            '0' => Mage::helper('eav')->__('No'),
            '1' => Mage::helper('eav')->__('Yes'),
            default => '',
        };
    }
}
