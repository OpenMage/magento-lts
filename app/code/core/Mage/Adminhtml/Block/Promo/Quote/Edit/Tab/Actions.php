<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Actions extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('salesrule')->__('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('salesrule')->__('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_quote_rule');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', ['legend' => Mage::helper('salesrule')->__('Update prices using the following information')]);

        $fieldset->addField('simple_action', 'select', [
            'label'     => Mage::helper('salesrule')->__('Apply'),
            'name'      => 'simple_action',
            'options'    => [
                Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION => Mage::helper('salesrule')->__('Percent of product price discount'),
                Mage_SalesRule_Model_Rule::BY_FIXED_ACTION => Mage::helper('salesrule')->__('Fixed amount discount'),
                Mage_SalesRule_Model_Rule::CART_FIXED_ACTION => Mage::helper('salesrule')->__('Fixed amount discount for whole cart'),
                Mage_SalesRule_Model_Rule::BUY_X_GET_Y_ACTION => Mage::helper('salesrule')->__('Buy X get Y free (discount amount is Y)'),
            ],
        ]);
        $fieldset->addField('discount_amount', 'text', [
            'name' => 'discount_amount',
            'required' => true,
            'class' => 'validate-not-negative-number',
            'label' => Mage::helper('salesrule')->__('Discount Amount'),
        ]);
        $model->setDiscountAmount($model->getDiscountAmount() * 1);

        $fieldset->addField('discount_qty', 'text', [
            'name' => 'discount_qty',
            'label' => Mage::helper('salesrule')->__('Maximum Qty Discount is Applied To'),
        ]);
        $model->setDiscountQty($model->getDiscountQty() * 1);

        $fieldset->addField('discount_step', 'text', [
            'name' => 'discount_step',
            'label' => Mage::helper('salesrule')->__('Discount Qty Step (Buy X)'),
        ]);

        $fieldset->addField('apply_to_shipping', 'select', [
            'label'     => Mage::helper('salesrule')->__('Apply to Shipping Amount'),
            'title'     => Mage::helper('salesrule')->__('Apply to Shipping Amount'),
            'name'      => 'apply_to_shipping',
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ]);

        $fieldset->addField('simple_free_shipping', 'select', [
            'label'     => Mage::helper('salesrule')->__('Free Shipping'),
            'title'     => Mage::helper('salesrule')->__('Free Shipping'),
            'name'      => 'simple_free_shipping',
            'options'    => [
                0 => Mage::helper('salesrule')->__('No'),
                Mage_SalesRule_Model_Rule::FREE_SHIPPING_ITEM => Mage::helper('salesrule')->__('For matching items only'),
                Mage_SalesRule_Model_Rule::FREE_SHIPPING_ADDRESS => Mage::helper('salesrule')->__('For shipment with matching items'),
            ],
        ]);

        $fieldset->addField('stop_rules_processing', 'select', [
            'label'     => Mage::helper('salesrule')->__('Stop Further Rules Processing'),
            'title'     => Mage::helper('salesrule')->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'    => [
                '1' => Mage::helper('salesrule')->__('Yes'),
                '0' => Mage::helper('salesrule')->__('No'),
            ],
        ]);

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/promo_quote/newActionHtml/form/rule_actions_fieldset'));

        $fieldset = $form->addFieldset('actions_fieldset', [
            'legend' => Mage::helper('salesrule')->__('Apply the rule only to cart items matching the following conditions (leave blank for all items)'),
        ])->setRenderer($renderer);

        $fieldset->addField('actions', 'text', [
            'name' => 'actions',
            'label' => Mage::helper('salesrule')->__('Apply To'),
            'title' => Mage::helper('salesrule')->__('Apply To'),
            'required' => true,
        ])->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/actions'));

        Mage::dispatchEvent('adminhtml_block_salesrule_actions_prepareform', ['form' => $form]);

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
