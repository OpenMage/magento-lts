<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Tax_Basedon
{
    public function toOptionArray()
    {
        return [
            ['value' => 'shipping', 'label' => Mage::helper('adminhtml')->__('Shipping Address')],
            ['value' => 'billing', 'label' => Mage::helper('adminhtml')->__('Billing Address')],
            ['value' => 'origin', 'label' => Mage::helper('adminhtml')->__('Shipping Origin')],
        ];
    }
}
