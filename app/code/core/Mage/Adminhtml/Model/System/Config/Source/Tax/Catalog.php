<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
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
