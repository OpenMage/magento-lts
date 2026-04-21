<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Catalog rule edit form block
 *
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
            'class'   => 'save apply',
            'label'   => Mage::helper('catalogrule')->__('Save and Apply'),
            'onclick' => "$('rule_auto_apply').value=1; editForm.submit()",
        ]);

        $this->_addPreparedButton(
            id: self::BUTTON_TYPE_SAVE_EDIT,
            level: 10,
            module: 'catalogrule',
            onClick: 'editForm.submit($(\'edit_form\').action + \'back/edit/\')',
        );
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    #[Override]
    public function getHeaderText()
    {
        $rule = Mage::registry('current_promo_catalog_rule');
        if ($rule->getRuleId()) {
            return Mage::helper('catalogrule')->__("Edit Rule '%s'", $this->escapeHtml($rule->getName()));
        }

        return Mage::helper('catalogrule')->__('New Rule');
    }
}
