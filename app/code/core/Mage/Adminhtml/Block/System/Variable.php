<?php
/**
 * Custom Varieble Block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Variable extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'system_variable';
        $this->_headerText = Mage::helper('adminhtml')->__('Custom Variables');
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('adminhtml')->__('Add New Variable'));
    }
}
