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
 * Create order status form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Status_New_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('new_order_status');
    }

    /**
     * Prepare form fields and structure
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model  = Mage::registry('current_status');
        $labels = $model ? $model->getStoreLabels() : [];

        $form   = new Varien_Data_Form([
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post',
        ]);

        $fieldset   = $form->addFieldset('base_fieldset', [
            'legend'    => Mage::helper('sales')->__('Order Status Information'),
        ]);

        $fieldset->addField('is_new', 'hidden', ['name' => 'is_new', 'value' => 1]);

        $fieldset->addField(
            'status',
            'text',
            [
                'name'      => 'status',
                'label'     => Mage::helper('sales')->__('Status Code'),
                'class'     => 'required-entry validate-code',
                'required'  => true,
            ],
        );

        $fieldset->addField(
            'label',
            'text',
            [
                'name'      => 'label',
                'label'     => Mage::helper('sales')->__('Status Label'),
                'class'     => 'required-entry',
                'required'  => true,
            ],
        );

        $fieldset = $form->addFieldset('store_labels_fieldset', [
            'legend'       => Mage::helper('sales')->__('Store View Specific Labels'),
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
                    $fieldset->addField("store_label_{$store->getId()}", 'text', [
                        'name'      => 'store_labels[' . $store->getId() . ']',
                        'required'  => false,
                        'label'     => $store->getName(),
                        'value'     => $labels[$store->getId()] ?? '',
                        'fieldset_html_class' => 'store',
                    ]);
                }
            }
        }

        if ($model) {
            $form->addValues($model->getData());
        }
        $form->setAction($this->getUrl('*/sales_order_status/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
