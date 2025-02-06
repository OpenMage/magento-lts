<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Catalog rule edit form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Promo_Catalog_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Apply" button
     * Add "Save and Continue" button
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'promo_catalog';

        parent::__construct();

        $this->_addButton('save_apply', [
            'class'   => 'save',
            'label'   => Mage::helper('catalogrule')->__('Save and Apply'),
            'onclick' => "$('rule_auto_apply').value=1; editForm.submit()",
        ]);

        $this->_addButton('save_and_continue_edit', [
            'class'   => 'save',
            'label'   => Mage::helper('catalogrule')->__('Save and Continue Edit'),
            'onclick' => 'editForm.submit($(\'edit_form\').action + \'back/edit/\')',
        ], 10);
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $rule = Mage::registry('current_promo_catalog_rule');
        if ($rule->getRuleId()) {
            return Mage::helper('catalogrule')->__("Edit Rule '%s'", $this->escapeHtml($rule->getName()));
        }
        return Mage::helper('catalogrule')->__('New Rule');
    }
}
