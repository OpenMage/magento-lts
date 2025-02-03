<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Labels extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('salesrule')->__('Labels');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('salesrule')->__('Labels');
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
        $rule = Mage::registry('current_promo_quote_rule');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('default_label_fieldset', [
            'legend' => Mage::helper('salesrule')->__('Default Label'),
        ]);
        $labels = $rule->getStoreLabels();
        $fieldset->addField('store_default_label', 'text', [
            'name'      => 'store_labels[0]',
            'required'  => false,
            'label'     => Mage::helper('salesrule')->__('Default Rule Label for All Store Views'),
            'value'     => $labels[0] ?? '',
        ]);

        $fieldset = $form->addFieldset('store_labels_fieldset', [
            'legend'       => Mage::helper('salesrule')->__('Store View Specific Labels'),
            'table_class'  => 'form-list stores-tree',
        ]);

        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset');
        if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
            $fieldset->setRenderer($renderer);
        }

        foreach (Mage::app()->getWebsites() as $website) {
            $fieldset->addField("w_{$website->getId()}_label", 'note', [
                'label'    => $website->getName(),
                'fieldset_html_class' => 'website',
            ]);
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField("sg_{$group->getId()}_label", 'note', [
                    'label'    => $group->getName(),
                    'fieldset_html_class' => 'store-group',
                ]);
                foreach ($stores as $store) {
                    $fieldset->addField("s_{$store->getId()}", 'text', [
                        'name'      => 'store_labels[' . $store->getId() . ']',
                        'required'  => false,
                        'label'     => $store->getName(),
                        'value'     => $labels[$store->getId()] ?? '',
                        'fieldset_html_class' => 'store',
                    ]);
                }
            }
        }

        if ($rule->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
