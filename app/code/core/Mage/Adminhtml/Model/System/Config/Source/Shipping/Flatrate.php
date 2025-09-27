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
class Mage_Adminhtml_Model_System_Config_Source_Shipping_Flatrate
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => Mage::helper('adminhtml')->__('None')],
            ['value' => 'O', 'label' => Mage::helper('adminhtml')->__('Per Order')],
            ['value' => 'I', 'label' => Mage::helper('adminhtml')->__('Per Item')],
        ];
    }
}
