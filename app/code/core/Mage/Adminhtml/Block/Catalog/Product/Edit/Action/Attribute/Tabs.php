<?php
/**
 * Adminhtml catalog product edit action attributes update tabs block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected function _construct()
    {
        parent::_construct();

        $this->setId('attributes_update_tabs');
        $this->setDestElementId('attributes_edit_form');
        $this->setTitle(Mage::helper('catalog')->__('Products Information'));
    }
}
