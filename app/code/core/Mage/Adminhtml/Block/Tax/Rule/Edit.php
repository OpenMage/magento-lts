<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml tax rule Edit Container
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tax_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'rule';
        $this->_controller = 'tax_rule';
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->_addButton('save_and_continue', [
            'label'     => Mage::helper('tax')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save continue',
        ], 10);

        $this->_formScripts[] = " function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'back/edit/') } ";

        return parent::_prepareLayout();
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('tax_rule')->getId()) {
            return Mage::helper('tax')->__('Edit Rule');
        }

        return Mage::helper('tax')->__('New Rule');
    }
}
