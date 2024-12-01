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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom Variable Edit Form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Variable_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Getter
     *
     * @return Mage_Core_Model_Variable
     */
    public function getVariable()
    {
        return Mage::registry('current_variable');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form([
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ]);

        $fieldset = $form->addFieldset('base', [
            'legend' => Mage::helper('adminhtml')->__('Variable'),
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('code', 'text', [
            'name'     => 'code',
            'label'    => Mage::helper('adminhtml')->__('Variable Code'),
            'title'    => Mage::helper('adminhtml')->__('Variable Code'),
            'required' => true,
            'class'    => 'validate-xml-identifier'
        ]);

        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => Mage::helper('adminhtml')->__('Variable Name'),
            'title'    => Mage::helper('adminhtml')->__('Variable Name'),
            'required' => true
        ]);

        $useDefault = false;
        if ($this->getVariable()->getId() && $this->getVariable()->getStoreId()) {
            $useDefault = !(
                (bool)$this->getVariable()->getStoreHtmlValue()
                || (bool)$this->getVariable()->getStorePlainValue()
            );
            $this->getVariable()->setUseDefaultValue((int)$useDefault);
            $fieldset->addField('use_default_value', 'select', [
                'name'   => 'use_default_value',
                'label'  => Mage::helper('adminhtml')->__('Use Default Variable Values'),
                'title'  => Mage::helper('adminhtml')->__('Use Default Variable Values'),
                'onchange' => 'toggleValueElement(this);',
                'values' => [
                    0 => Mage::helper('adminhtml')->__('No'),
                    1 => Mage::helper('adminhtml')->__('Yes')
                ]
            ]);
        }

        $fieldset->addField('html_value', 'textarea', [
            'name'     => 'html_value',
            'label'    => Mage::helper('adminhtml')->__('Variable HTML Value'),
            'title'    => Mage::helper('adminhtml')->__('Variable HTML Value'),
            'disabled' => $useDefault
        ]);

        $fieldset->addField('plain_value', 'textarea', [
            'name'     => 'plain_value',
            'label'    => Mage::helper('adminhtml')->__('Variable Plain Value'),
            'title'    => Mage::helper('adminhtml')->__('Variable Plain Value'),
            'disabled' => $useDefault
        ]);

        $form->setValues($this->getVariable()->getData())
            ->addFieldNameSuffix('variable')
            ->setUseContainer(true);

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
