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
class Mage_Adminhtml_Model_System_Config_Source_Price_Scope
{
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => Mage::helper('core')->__('Global')],
            ['value' => '1', 'label' => Mage::helper('core')->__('Website')],
        ];
    }
}
