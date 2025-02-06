<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Bundle
 */

/**
 * Extended Attributes Source Model
 *
 * @category   Mage
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
