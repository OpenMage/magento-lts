<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml permissions variable edit page
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Variable_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'variable_id';
        $this->_controller = 'permissions_variable';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save Variable'));
        $this->_updateButton('delete', 'label', Mage::helper('adminhtml')->__('Delete Variable'));
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('permissions_variable')->getId()) {
            return Mage::helper('adminhtml')->__("Edit Variable '%s'", $this->escapeHtml(Mage::registry('permissions_variable')->getVariableName()));
        }
        return Mage::helper('adminhtml')->__('New Variable');
    }
}
