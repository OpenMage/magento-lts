<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_Block_Catalog_Category_Attribute_Edit_Tab_Main
    extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
{
    /**
     * Adding product form elements for editing attribute
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        /* @var $form Varien_Data_Form */
        $form = $this->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');

        $fieldset->getElements()
            ->searchById('attribute_code')
            ->setData(
                'class',
                'validate-code-event ' . $fieldset->getElements()->searchById('attribute_code')->getData('class')
            )->setData(
                'note',
                $fieldset->getElements()->searchById('attribute_code')->getData('note')
                . Mage::helper('eav')->__('. Do not use "event" for an attribute code, it is a reserved keyword.')
            );

        $frontendInputElm = $form->getElement('frontend_input');
        $additionalTypes = array(
            array(
                'value' => 'price',
                'label' => Mage::helper('catalog')->__('Price')
            ),
            array(
                'value' => 'media_image',
                'label' => Mage::helper('catalog')->__('Media Image')
            )
        );
        if ($attributeObject->getFrontendInput() == 'gallery') {
            $additionalTypes[] = array(
                'value' => 'gallery',
                'label' => Mage::helper('catalog')->__('Gallery')
            );
        }

        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_category_attribute_types', array('response'=>$response));
        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $additionalTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }
        Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        Mage::register('attribute_type_disabled_types', $_disabledTypes);

        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues);

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $scopes = array(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('catalog')->__('Store View'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('catalog')->__('Website'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('catalog')->__('Global'),
        );

        if (
            $attributeObject->getAttributeCode() == 'status'
            || $attributeObject->getAttributeCode() == 'tax_class_id'
        ) {
            unset($scopes[Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE]);
        }

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => Mage::helper('catalog')->__('Scope'),
            'title' => Mage::helper('catalog')->__('Scope'),
            'note'  => Mage::helper('catalog')->__('Declare attribute value saving scope'),
            'values'=> $scopes
        ), 'attribute_code');

        // frontend properties fieldset
        $fieldset = $form->addFieldset('front_fieldset', array('legend'=>Mage::helper('catalog')->__('Frontend Properties')));

        $fieldset->addField('position', 'text', array(
            'name' => 'position',
            'label' => Mage::helper('catalog')->__('Position'),
            'title' => Mage::helper('catalog')->__('Position in Layered Navigation'),
            'note' => Mage::helper('catalog')->__('Position of attribute in layered navigation block'),
            'class' => 'validate-digits',
        ));

        $fieldset->addField('is_wysiwyg_enabled', 'select', array(
            'name' => 'is_wysiwyg_enabled',
            'label' => Mage::helper('catalog')->__('Enable WYSIWYG'),
            'title' => Mage::helper('catalog')->__('Enable WYSIWYG'),
            'values' => $yesnoSource,
        ));

        // define field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap("is_wysiwyg_enabled", 'wysiwyg_enabled')
            ->addFieldMap("frontend_input", 'frontend_input_type')
            ->addFieldDependence('wysiwyg_enabled', 'frontend_input_type', 'textarea')
        );

        Mage::dispatchEvent('adminhtml_catalog_category_attribute_edit_prepare_form', array(
            'form'      => $form,
            'attribute' => $attributeObject
        ));

        return $this;
    }
}
