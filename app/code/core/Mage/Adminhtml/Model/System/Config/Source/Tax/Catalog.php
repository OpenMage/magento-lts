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
class Mage_Adminhtml_Model_System_Config_Source_Tax_Catalog
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => Mage::helper('adminhtml')->__('No (price without tax)')],
            ['value' => 1, 'label' => Mage::helper('adminhtml')->__('Yes (only price with tax)')],
            ['value' => 2, 'label' => Mage::helper('adminhtml')->__('Both (without and with tax)')],
        ];
    }
}
