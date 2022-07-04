<?php

class Mage_Adminhtml_Block_Catalog_Category_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'catalog_category_attribute';
        $this->_headerText = Mage::helper('catalog')->__('Manage Category Attributes');
        $this->_addButtonLabel = Mage::helper('catalog')->__('Add New Attribute');
        parent::__construct();
    }
}
