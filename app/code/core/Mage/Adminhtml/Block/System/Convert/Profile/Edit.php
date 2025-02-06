<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Convert profile edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'system_convert_profile';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save Profile'));
        $this->_updateButton('delete', 'label', Mage::helper('adminhtml')->__('Delete Profile'));
        $this->_addButton('savecontinue', [
            'label' => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick' => "$('edit_form').action += 'continue/true/'; editForm.submit();",
            'class' => 'save',
        ], -100);
    }

    public function getProfileId()
    {
        return Mage::registry('current_convert_profile')->getId();
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_convert_profile')->getId()) {
            return $this->escapeHtml(Mage::registry('current_convert_profile')->getName());
        }
        return Mage::helper('adminhtml')->__('New Profile');
    }
}
