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
     * @param int $value
     * @return string
     */
    protected function _getOptionText($value)
    {
        switch ($value) {
            case '0':
                $text = Mage::helper('eav')->__('No');
                break;
            case '1':
                $text = Mage::helper('eav')->__('Yes');
                break;
            default:
                $text = '';
                break;
        }
        return $text;
    }
}
