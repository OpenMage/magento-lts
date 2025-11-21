<?php

class Mage_Adminhtml_Model_System_Config_Source_Catalog_ImageDuplicate {


    public function toOptionArray()
    {
        return [
            ['value' => -1, 'label' => Mage::helper('adminhtml')->__('Always ask')],
            ['value' => 0, 'label' => Mage::helper('adminhtml')->__('Copy images to the new product')],
            ['value' => 1, 'label' => Mage::helper('adminhtml')->__('Duplicate product without images')],
        ];
    }
}
