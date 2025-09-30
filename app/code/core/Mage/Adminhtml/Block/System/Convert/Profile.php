<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml convert profiles list block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Convert_Profile extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'system_convert_profile';
        $this->_headerText = Mage::helper('adminhtml')->__('Advanced Profiles');
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add New Profile');

        parent::__construct();
    }
}
