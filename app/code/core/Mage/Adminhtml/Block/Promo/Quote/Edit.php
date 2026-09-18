<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Shopping cart rule edit form block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'promo_quote';

        parent::__construct();

        $this->_addPreparedButton(
            id: self::BUTTON_TYPE_SAVE_EDIT,
            level: 10,
            module: 'salesrule',
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
        $rule = Mage::registry('current_promo_quote_rule');
        if ($rule->getRuleId()) {
            return Mage::helper('salesrule')->__("Edit Rule '%s'", $this->escapeHtml($rule->getName()));
        }

        return Mage::helper('salesrule')->__('New Rule');
    }

    /**
     * Retrieve products JSON
     *
     * @return string
     */
    public function getProductsJson()
    {
        return '{}';
    }
}
