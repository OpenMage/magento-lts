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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer groups edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Group_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form for render
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Varien_Data_Form();
        $customerGroup = Mage::registry('current_group');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('customer')->__('Group Information')]);

        $validateClass = sprintf(
            'required-entry validate-length maximum-length-%d',
            Mage_Customer_Model_Group::GROUP_CODE_MAX_LENGTH
        );
        $name = $fieldset->addField(
            'customer_group_code',
            'text',
            [
                'name'  => 'code',
                'label' => Mage::helper('customer')->__('Group Name'),
                'title' => Mage::helper('customer')->__('Group Name'),
                'note'  => Mage::helper('customer')->__('Maximum length must be less then %s symbols', Mage_Customer_Model_Group::GROUP_CODE_MAX_LENGTH),
                'class' => $validateClass,
                'required' => true,
            ]
        );

        if ($customerGroup->getId() == 0 && $customerGroup->getCustomerGroupCode()) {
            $name->setDisabled(true);
        }

        $fieldset->addField(
            'tax_class_id',
            'select',
            [
                'name'  => 'tax_class',
                'label' => Mage::helper('customer')->__('Tax Class'),
                'title' => Mage::helper('customer')->__('Tax Class'),
                'class' => 'required-entry',
                'required' => true,
                'values' => Mage::getSingleton('tax/class_source_customer')->toOptionArray()
            ]
        );

        if (!is_null($customerGroup->getId())) {
            // If edit add id
            $form->addField(
                'id',
                'hidden',
                [
                    'name'  => 'id',
                    'value' => $customerGroup->getId(),
                ]
            );
        }

        if (Mage::getSingleton('adminhtml/session')->getCustomerGroupData()) {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getCustomerGroupData());
            Mage::getSingleton('adminhtml/session')->setCustomerGroupData(null);
        } else {
            $form->addValues($customerGroup->getData());
        }

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $this->setForm($form);
        return $this;
    }
}
