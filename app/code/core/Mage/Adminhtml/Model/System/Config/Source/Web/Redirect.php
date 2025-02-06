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
class Mage_Adminhtml_Model_System_Config_Source_Web_Redirect
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => Mage::helper('adminhtml')->__('No')],
            ['value' => 1, 'label' => Mage::helper('adminhtml')->__('Yes (302 Found)')],
            ['value' => 301, 'label' => Mage::helper('adminhtml')->__('Yes (301 Moved Permanently)')],
        ];
    }
}
