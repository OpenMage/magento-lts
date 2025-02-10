<?php
/**
 * Adminhtml source reports event store filter
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Reports_Scope
{
    public function toOptionArray()
    {
        return [
            ['value' => 'website', 'label' => Mage::helper('adminhtml')->__('Website')],
            ['value' => 'group', 'label' => Mage::helper('adminhtml')->__('Store')],
            ['value' => 'store', 'label' => Mage::helper('adminhtml')->__('Store View')],
        ];
    }
}
