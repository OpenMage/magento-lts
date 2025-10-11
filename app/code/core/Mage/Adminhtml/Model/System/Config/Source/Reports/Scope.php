<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml source reports event store filter
 *
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
