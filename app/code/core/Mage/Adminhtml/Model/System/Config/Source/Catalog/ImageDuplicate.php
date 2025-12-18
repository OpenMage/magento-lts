<?php

class Mage_Adminhtml_Model_System_Config_Source_Catalog_ImageDuplicate
{
    public function toOptionArray()
    {
        return [
            ['value' => Mage_Catalog_Model_Product_Image::ON_DUPLICATE_ASK, 'label' => Mage::helper('adminhtml')->__('Always ask')],
            ['value' => Mage_Catalog_Model_Product_Image::ON_DUPLICATE_COPY, 'label' => Mage::helper('adminhtml')->__('Copy images to the new product')],
            ['value' => Mage_Catalog_Model_Product_Image::ON_DUPLICATE_SKIP, 'label' => Mage::helper('adminhtml')->__('Duplicate product without images')],
        ];
    }
}
