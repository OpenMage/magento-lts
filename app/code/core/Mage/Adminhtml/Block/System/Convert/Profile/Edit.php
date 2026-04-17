<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Convert profile edit block
 *
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
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => "$('edit_form').action += 'continue/true/'; editForm.submit();",
            'class'     => 'save continue',
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
