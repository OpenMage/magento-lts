<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml edit admin user account
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Account_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_controller = 'system_account';
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save Account'));
        $this->_removeButton('delete');
        $this->_removeButton('back');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('My Account');
    }
}
