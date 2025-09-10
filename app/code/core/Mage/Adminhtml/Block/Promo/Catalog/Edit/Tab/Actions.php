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
class Mage_Adminhtml_Block_Promo_Catalog_Edit_Tab_Actions extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('catalogrule')->__('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('catalogrule')->__('Actions');
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
        $model = Mage::registry('current_promo_catalog_rule');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', [
            'legend' => Mage::helper('catalogrule')->__('Update Prices Using the Following Information'),
        ]);

        $fieldset->addField('simple_action', 'select', [
            'label'     => Mage::helper('catalogrule')->__('Apply'),
            'name'      => 'simple_action',
            'options'   => [
                'by_percent'    => Mage::helper('catalogrule')->__('By Percentage of the Original Price'),
                'by_fixed'      => Mage::helper('catalogrule')->__('By Fixed Amount'),
                'to_percent'    => Mage::helper('catalogrule')->__('To Percentage of the Original Price'),
                'to_fixed'      => Mage::helper('catalogrule')->__('To Fixed Amount'),
            ],
        ]);

        $fieldset->addField('discount_amount', 'text', [
            'name'      => 'discount_amount',
            'required'  => true,
            'class'     => 'validate-not-negative-number',
            'label'     => Mage::helper('catalogrule')->__('Discount Amount'),
        ]);

        $fieldset->addField('sub_is_enable', 'select', [
            'name'      => 'sub_is_enable',
            'label'     => Mage::helper('catalogrule')->__('Enable Discount to Subproducts'),
            'title'     => Mage::helper('catalogrule')->__('Enable Discount to Subproducts'),
            'onchange'  => 'hideShowSubproductOptions(this);',
            'values'    => [
                0 => Mage::helper('catalogrule')->__('No'),
                1 => Mage::helper('catalogrule')->__('Yes'),
            ],
        ]);

        $fieldset->addField('sub_simple_action', 'select', [
            'label'     => Mage::helper('catalogrule')->__('Apply'),
            'name'      => 'sub_simple_action',
            'options'   => [
                'by_percent'    => Mage::helper('catalogrule')->__('By Percentage of the Original Price'),
                'by_fixed'      => Mage::helper('catalogrule')->__('By Fixed Amount'),
                'to_percent'    => Mage::helper('catalogrule')->__('To Percentage of the Original Price'),
                'to_fixed'      => Mage::helper('catalogrule')->__('To Fixed Amount'),
            ],
        ]);

        $fieldset->addField('sub_discount_amount', 'text', [
            'name'      => 'sub_discount_amount',
            'required'  => true,
            'class'     => 'validate-not-negative-number',
            'label'     => Mage::helper('catalogrule')->__('Discount Amount'),
        ]);

        $fieldset->addField('stop_rules_processing', 'select', [
            'label'     => Mage::helper('catalogrule')->__('Stop Further Rules Processing'),
            'title'     => Mage::helper('catalogrule')->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'   => [
                '1' => Mage::helper('catalogrule')->__('Yes'),
                '0' => Mage::helper('catalogrule')->__('No'),
            ],
        ]);

        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
