<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Admin form widget
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Form extends Mage_Adminhtml_Block_Widget
{
    /**
     * Form Object
     *
     * @var Varien_Data_Form
     */
    protected $_form;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/form.phtml');
        $this->setDestElementId('edit_form');
        $this->setShowGlobalIcon(false);
    }

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changin layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
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

        return parent::_prepareLayout();
    }

    /**
     * Get form object
     *
     * @return Varien_Data_Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Get form object
     *
     * @return Varien_Data_Form
     * @deprecated deprecated since version 1.2
     * @see getForm()
     */
    public function getFormObject()
    {
        return $this->getForm();
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        if (is_object($this->getForm())) {
            return $this->getForm()->getHtml();
        }

        return '';
    }

    /**
     * Set form object
     *
     * @return $this
     */
    public function setForm(Varien_Data_Form $form)
    {
        $this->_form = $form;
        $this->_form->setParent($this);
        $this->_form->setBaseUrl(Mage::getBaseUrl());
        return $this;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        return $this;
    }

    /**
     * This method is called before rendering HTML
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_prepareForm();
        $this->_initFormValues();
        Mage::dispatchEvent(
            'adminhtml_block_widget_form_init_form_values_after',
            [
                'block' => $this,
                'form' => $this->getForm(),
            ],
        );
        return parent::_beforeToHtml();
    }

    /**
     * Initialize form fields values
     * Method will be called after prepareForm and can be used for field values initialization
     *
     * @return $this
     */
    protected function _initFormValues()
    {
        return $this;
    }

    /**
     * Set Fieldset to Form
     *
     * @param array                             $attributes attributes that are to be added
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array                             $exclude    attributes that should be skipped
     */
    protected function _setFieldset($attributes, $fieldset, $exclude = [])
    {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            /** @var Mage_Eav_Model_Entity_Attribute $attribute */
            if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
                continue;
            }

            if (($inputType = $attribute->getFrontend()->getInputType())
                 && !in_array($attribute->getAttributeCode(), $exclude)
                 && ($inputType != 'media_image')
            ) {
                $fieldType      = $inputType;
                $rendererClass  = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType  = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                $element = $fieldset->addField(
                    $attribute->getAttributeCode(),
                    $fieldType,
                    [
                        'name'      => $attribute->getAttributeCode(),
                        'label'     => $attribute->getFrontend()->getLabel(),
                        'class'     => $attribute->getFrontend()->getClass(),
                        'required'  => $attribute->getIsRequired(),
                        'note'      => $this->escapeHtml($attribute->getNote()),
                    ],
                )
                ->setEntityAttribute($attribute);

                $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));

                if ($inputType == 'select') {
                    $element->setValues($attribute->getSource()->getAllOptions(true, true));
                } elseif ($inputType == 'multiselect') {
                    $element->setValues($attribute->getSource()->getAllOptions(false, true));
                    $element->setCanBeEmpty(true);
                } elseif ($inputType == 'date') {
                    $element->setImage($this->getSkinUrl('images/grid-cal.gif'));
                    $element->setFormat(Mage::app()->getLocale()->getDateFormatWithLongYear());
                } elseif ($inputType == 'datetime') {
                    $element->setImage($this->getSkinUrl('images/grid-cal.gif'));
                    $element->setTime(true);
                    $element->setStyle('width:50%;');
                    $element->setFormat(
                        Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                    );
                } elseif ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
            }
        }
    }

    /**
     * Add new element type
     */
    protected function _addElementTypes(Varien_Data_Form_Abstract $baseElement)
    {
        $types = $this->_getAdditionalElementTypes();
        foreach ($types as $code => $className) {
            $baseElement->addType($code, $className);
        }
    }

    /**
     * Retrieve predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return [];
    }

    /**
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        return '';
    }

    protected function getStoreSwitcherRenderer(): Mage_Adminhtml_Block_Store_Switcher_Form_Renderer_Fieldset_Element
    {
        /** @var Mage_Adminhtml_Block_Store_Switcher_Form_Renderer_Fieldset_Element $renderer */
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        return $renderer;
    }
}
