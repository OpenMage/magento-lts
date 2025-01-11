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
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Design_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('general', ['legend' => Mage::helper('core')->__('General Settings')]);

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', [
                'label'    => Mage::helper('core')->__('Store'),
                'title'    => Mage::helper('core')->__('Store'),
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                'name'     => 'store_id',
                'required' => true,
            ]);
            $renderer = $this->getStoreSwitcherRenderer();
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', [
                'name'      => 'store_id',
                'value'     => Mage::app()->getStore(true)->getId(),
            ]);
        }

        $fieldset->addField('design', 'select', [
            'label'    => Mage::helper('core')->__('Custom Design'),
            'title'    => Mage::helper('core')->__('Custom Design'),
            'values'   => Mage::getSingleton('core/design_source_design')->getAllOptions(),
            'name'     => 'design',
            'required' => true,
        ]);

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('date_from', 'date', [
            'label'    => Mage::helper('core')->__('Date From'),
            'title'    => Mage::helper('core')->__('Date From'),
            'name'     => 'date_from',
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'format'   => $dateFormatIso,
            //'required' => true,
        ]);
        $fieldset->addField('date_to', 'date', [
            'label'    => Mage::helper('core')->__('Date To'),
            'title'    => Mage::helper('core')->__('Date To'),
            'name'     => 'date_to',
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'format'   => $dateFormatIso,
            //'required' => true,
        ]);

        $formData = Mage::getSingleton('adminhtml/session')->getDesignData(true);
        if (!$formData) {
            $formData = Mage::registry('design')->getData();
        } else {
            $formData = $formData['design'];
        }

        $form->addValues($formData);
        $form->setFieldNameSuffix('design');
        $this->setForm($form);
        return $this;
    }
}
