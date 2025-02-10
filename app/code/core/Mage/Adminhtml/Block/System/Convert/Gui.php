<?php
/**
 * Adminhtml convert profiles list block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Convert_Gui extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'system_convert_gui';
        $this->_headerText = Mage::helper('adminhtml')->__('Profiles');
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add New Profile');

        parent::__construct();
    }
}
