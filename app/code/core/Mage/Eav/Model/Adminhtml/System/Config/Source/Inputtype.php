<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'text', 'label' => Mage::helper('eav')->__('Text Field')],
            ['value' => 'textarea', 'label' => Mage::helper('eav')->__('Text Area')],
            ['value' => 'date', 'label' => Mage::helper('eav')->__('Date')],
            ['value' => 'boolean', 'label' => Mage::helper('eav')->__('Yes/No')],
            ['value' => 'multiselect', 'label' => Mage::helper('eav')->__('Multiple Select')],
            ['value' => 'select', 'label' => Mage::helper('eav')->__('Dropdown')],
        ];
    }
}
