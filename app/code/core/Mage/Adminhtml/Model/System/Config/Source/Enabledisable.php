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
class Mage_Adminhtml_Model_System_Config_Source_Enabledisable
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => Mage::helper('adminhtml')->__('Enable')],
            ['value' => 0, 'label' => Mage::helper('adminhtml')->__('Disable')],
        ];
    }
}
