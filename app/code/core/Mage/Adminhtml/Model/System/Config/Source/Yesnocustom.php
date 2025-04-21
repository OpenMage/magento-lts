<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Used in creating options for Yes|No|Specified config value selection
 *
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
