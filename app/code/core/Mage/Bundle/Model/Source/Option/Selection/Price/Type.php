<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Extended Attributes Source Model
 *
 * @package    Mage_Bundle
 */
class Mage_Bundle_Model_Source_Option_Selection_Price_Type
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => Mage::helper('bundle')->__('Fixed')],
            ['value' => '1', 'label' => Mage::helper('bundle')->__('Percent')],
        ];
    }
}
