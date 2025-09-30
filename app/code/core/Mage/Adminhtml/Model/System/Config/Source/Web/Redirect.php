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
