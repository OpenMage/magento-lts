<?php

class Mage_Adminhtml_Model_System_Config_Source_Cookie_Samesite
{
    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'None', 'label' => Mage::helper('adminhtml')->__('None')],
            ['value' => 'Strict', 'label' => Mage::helper('adminhtml')->__('Strict')],
            ['value' => 'Lax', 'label' => Mage::helper('adminhtml')->__('Lax')]
        ];
    }
}
