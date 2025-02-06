<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Eav
 */

/**
 * EAV Entity Attribute Boolean Data Model
 *
 * @category   Mage
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
