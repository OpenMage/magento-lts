<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * CMS block edit form container
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cms_Block_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'block_id';
        $this->_controller = 'cms_block';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('cms')->__('Save Block'));
        $this->_updateButton('delete', 'label', Mage::helper('cms')->__('Delete Block'));

        $this->_addButton('saveandcontinue', [
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ], -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                tinymce.execCommand('mceToggleEditor', false, wysiwygblock_content);
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('cms_block')->getId()) {
            return Mage::helper('cms')->__("Edit Block '%s'", $this->escapeHtml(Mage::registry('cms_block')->getTitle()));
        }
        return Mage::helper('cms')->__('New Block');
    }
}
