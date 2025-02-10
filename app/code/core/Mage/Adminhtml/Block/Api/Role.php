<?php
/**
 * Adminhtml permissioms role block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Api_Role extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'api_role';
        $this->_headerText = Mage::helper('adminhtml')->__('Roles');
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add New Role');
        parent::__construct();
    }
}
