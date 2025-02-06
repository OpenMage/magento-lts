<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Price types mode source
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Product_Options_Price
{
    public function toOptionArray()
    {
        return [
            ['value' => 'fixed', 'label' => Mage::helper('adminhtml')->__('Fixed')],
            ['value' => 'percent', 'label' => Mage::helper('adminhtml')->__('Percent')],
        ];
    }
}
