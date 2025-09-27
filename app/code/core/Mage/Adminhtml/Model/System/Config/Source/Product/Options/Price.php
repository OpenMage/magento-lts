<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Price types mode source
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Product_Options_Price
{
    public function toOptionArray()
    {
        return [
            ['value' => 'fixed', 'label' => Mage::helper('adminhtml')->__('Fixed')],
            ['value' => 'percent', 'label' => Mage::helper('adminhtml')->__('Percent')],
        ];
    }
}
