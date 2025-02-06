<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Used in creating options for Yes|No|Specified config value selection
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Yesnocustom
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => Mage::helper('adminhtml')->__('Yes')],
            ['value' => 0, 'label' => Mage::helper('adminhtml')->__('No')],
            ['value' => 2, 'label' => Mage::helper('adminhtml')->__('Specified')],
        ];
    }
}
