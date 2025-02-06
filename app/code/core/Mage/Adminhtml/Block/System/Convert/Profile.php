<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml convert profiles list block
 *
 * @category   Mage
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
