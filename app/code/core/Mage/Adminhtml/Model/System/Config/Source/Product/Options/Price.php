<?php
/**
 * Price types mode source
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
