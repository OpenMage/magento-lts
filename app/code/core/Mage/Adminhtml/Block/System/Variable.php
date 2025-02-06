<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Custom Varieble Block
 *
 * @category   Mage
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
