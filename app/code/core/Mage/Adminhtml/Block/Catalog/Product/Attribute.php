<?php
/**
 * Adminhtml catalog product attributes block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'catalog_product_attribute';
        $this->_headerText = Mage::helper('catalog')->__('Manage Attributes');
        $this->_addButtonLabel = Mage::helper('catalog')->__('Add New Attribute');
        parent::__construct();
    }
}
