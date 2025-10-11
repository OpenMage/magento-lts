<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * Roles grid container block
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Block_Adminhtml_Roles extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'api2';
        $this->_controller = 'adminhtml_roles';
        $this->_headerText = Mage::helper('adminhtml')->__('REST Roles');

        //check allow edit
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        if ($session->isAllowed('system/api/roles/add')) {
            $this->_updateButton('add', 'label', $this->__('Add Admin Role'));
        } else {
            $this->_removeButton('add');
        }
    }
}
