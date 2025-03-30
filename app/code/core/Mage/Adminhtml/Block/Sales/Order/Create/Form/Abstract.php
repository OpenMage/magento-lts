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
 * Sales Order Create Form Abstract Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
abstract class Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Data Form object
     *
     * @var Varien_Data_Form|null
     */
    protected $_form;

    /**
     * Prepare global layout
     * Add renderers to Varien_Data_Form
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element');
        if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
            Varien_Data_Form::setElementRenderer($renderer);
        }

        $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset');
        if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
            Varien_Data_Form::setFieldsetRenderer($renderer);
        }

        $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element');
        if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
            Varien_Data_Form::setFieldsetElementRenderer($renderer);
        }

        return $this;
    }

    /**
     * Return Form object
     *
     * @return Varien_Data_Form
     */
    public function getForm()
    {
        if (is_null($this->_form)) {
            $this->_form = new Varien_Data_Form();
            $this->_prepareForm();
        }

        return $this->_form;
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract
     */
    abstract protected function _prepareForm();

    /**
     * Return array of additional form element types by type
     *
     * @return array
     */
    protected function _getAdditionalFormElementTypes()
    {
        return [
            'file'      => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_file'),
            'image'     => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_image'),
            'boolean'   => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_boolean'),
        ];
    }

    /**
     * Return array of additional form element renderers by element id
     *
     * @return array
     */
    protected function _getAdditionalFormElementRenderers()
    {
        return [
            'region'    => $this->getLayout()->createBlock('adminhtml/customer_edit_renderer_region'),
        ];
    }

    /**
     * Add additional data to form element
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract
     */
    protected function _addAdditionalFormElementData(Varien_Data_Form_Element_Abstract $element)
    {
        return $this;
    }

    /**
     * Add rendering EAV attributes to Form element
     *
     * @param array|Varien_Data_Collection $attributes
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract
     */
    protected function _addAttributesToForm($attributes, Varien_Data_Form_Abstract $form)
    {
        // add additional form types
        $types = $this->_getAdditionalFormElementTypes();
        foreach ($types as $type => $className) {
            $form->addType($type, $className);
        }
        $renderers = $this->_getAdditionalFormElementRenderers();

        foreach ($attributes as $attribute) {
            /** @var Mage_Customer_Model_Attribute $attribute */
            $attribute->setStoreId(Mage::getSingleton('adminhtml/session_quote')->getStoreId());
            $inputType = $attribute->getFrontend()->getInputType();

            if ($inputType) {
                $element = $form->addField($attribute->getAttributeCode(), $inputType, [
                    'name'      => $attribute->getAttributeCode(),
                    'label'     => $this->__($attribute->getStoreLabel()),
                    'class'     => $attribute->getFrontend()->getClass(),
                    'required'  => $attribute->getIsRequired(),
                    'note'      => $this->escapeHtml($this->__($attribute->getNote())),
                ]);
                if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
                $element->setEntityAttribute($attribute);
                $this->_addAdditionalFormElementData($element);

                if (!empty($renderers[$attribute->getAttributeCode()])) {
                    $element->setRenderer($renderers[$attribute->getAttributeCode()]);
                }

                if ($inputType == 'select' || $inputType == 'multiselect') {
                    $element->setValues($attribute->getFrontend()->getSelectOptions());
                } elseif ($inputType == 'date') {
                    $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
                    $element->setImage($this->getSkinUrl('images/grid-cal.gif'));
                    $element->setFormat($format);
                }
            }
        }

        return $this;
    }

    /**
     * Return Form Elements values
     *
     * @return array
     */
    public function getFormValues()
    {
        return [];
    }
}
