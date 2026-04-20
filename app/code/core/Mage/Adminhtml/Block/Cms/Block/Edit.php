<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * CMS block edit form container
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cms_Block_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'block_id';
        $this->_controller = 'cms_block';

        parent::__construct();

        if ($this->_isAllowedAction('save')) {
            $this->_addButton('saveandcontinue', [
                'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save continue',
            ], -100);
        } else {
            $this->_removeButton('save');
        }

        if (!$this->_isAllowedAction('delete')) {
            $this->_removeButton('delete');
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                tinymce.execCommand('mceToggleEditor', false, wysiwygblock_content);
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";

        return parent::_prepareLayout();
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    #[Override]
    public function getHeaderText()
    {
        if (Mage::registry('cms_block')->getId()) {
            return Mage::helper('cms')->__("Edit Block '%s'", $this->escapeHtml(Mage::registry('cms_block')->getTitle()));
        }

        return Mage::helper('cms')->__('New Block');
    }

    /**
     * Check permission for passed action
     */
    protected function _isAllowedAction(string $action): bool
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/block/' . $action);
    }
}
