<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Yesno
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => Mage::helper('adminhtml')->__('Yes')],
            ['value' => 0, 'label' => Mage::helper('adminhtml')->__('No')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            0 => Mage::helper('adminhtml')->__('No'),
            1 => Mage::helper('adminhtml')->__('Yes'),
        ];
    }
}
