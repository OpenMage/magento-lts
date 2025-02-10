<?php
/**
 * Widget Instance edit tabs container
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Widget
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('widget_instace_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('widget')->__('Widget Instance'));
    }
}
