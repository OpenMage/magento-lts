<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product attribute add/edit form main tab
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_attribute = null;

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return $this
     */
    public function setAttributeObject($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttributeObject()
    {
        if ($this->_attribute === null) {
            return Mage::registry('entity_attribute');
        }
        return $this->_attribute;
    }

    /**
     * Preparing default form elements for editing attribute
     *
     * @inheritDoc
     */
    protected function _prepareForm()
    {
        $attributeObject = $this->getAttributeObject();

        $form = new Varien_Data_Form([
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ]);

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend'=>Mage::helper('eav')->__('Attribute Properties')]
        );
        if ($attributeObject->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', [
                'name' => 'attribute_id',
            ]);
        }

        $this->_addElementTypes($fieldset);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $validateClass = sprintf(
            'validate-code validate-length maximum-length-%d',
            Mage_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH
        );
        $fieldset->addField('attribute_code', 'text', [
            'name'  => 'attribute_code',
            'label' => Mage::helper('eav')->__('Attribute Code'),
            'title' => Mage::helper('eav')->__('Attribute Code'),
            'note'  => Mage::helper('eav')->__('For internal use. Must be unique with no spaces. Maximum length of attribute code must be less then %s symbols', Mage_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH),
            'class' => $validateClass,
            'required' => true,
        ]);

        $inputTypes = Mage::getModel('eav/adminhtml_system_config_source_inputtype')->toOptionArray();

        $fieldset->addField('frontend_input', 'select', [
            'name' => 'frontend_input',
            'label' => Mage::helper('eav')->__('Catalog Input Type for Store Owner'),
            'title' => Mage::helper('eav')->__('Catalog Input Type for Store Owner'),
            'value' => 'text',
            'values'=> $inputTypes
        ]);

        $fieldset->addField('default_value_text', 'text', [
            'name' => 'default_value_text',
            'label' => Mage::helper('eav')->__('Default Value'),
            'title' => Mage::helper('eav')->__('Default Value'),
            'value' => $attributeObject->getDefaultValue(),
        ]);

        $fieldset->addField('default_value_yesno', 'select', [
            'name' => 'default_value_yesno',
            'label' => Mage::helper('eav')->__('Default Value'),
            'title' => Mage::helper('eav')->__('Default Value'),
            'values' => $yesno,
            'value' => $attributeObject->getDefaultValue(),
        ]);

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('default_value_date', 'date', [
            'name'   => 'default_value_date',
            'label'  => Mage::helper('eav')->__('Default Value'),
            'title'  => Mage::helper('eav')->__('Default Value'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'value'  => $attributeObject->getDefaultValue(),
            'format'       => $dateFormatIso
        ]);

        $fieldset->addField('default_value_textarea', 'textarea', [
            'name' => 'default_value_textarea',
            'label' => Mage::helper('eav')->__('Default Value'),
            'title' => Mage::helper('eav')->__('Default Value'),
            'value' => $attributeObject->getDefaultValue(),
        ]);

        $fieldset->addField('is_unique', 'select', [
            'name' => 'is_unique',
            'label' => Mage::helper('eav')->__('Unique Value'),
            'title' => Mage::helper('eav')->__('Unique Value (not shared with other products)'),
            'note'  => Mage::helper('eav')->__('Not shared with other products'),
            'values' => $yesno,
        ]);

        $fieldset->addField('is_required', 'select', [
            'name' => 'is_required',
            'label' => Mage::helper('eav')->__('Values Required'),
            'title' => Mage::helper('eav')->__('Values Required'),
            'values' => $yesno,
        ]);

        $fieldset->addField('frontend_class', 'select', [
            'name'  => 'frontend_class',
            'label' => Mage::helper('eav')->__('Input Validation for Store Owner'),
            'title' => Mage::helper('eav')->__('Input Validation for Store Owner'),
            'values'=> Mage::helper('eav')->getFrontendClasses($attributeObject->getEntityType()->getEntityTypeCode())
        ]);

        if ($attributeObject->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            $form->getElement('frontend_input')->setDisabled(1);
            if (!$attributeObject->getIsUserDefined()) {
                $form->getElement('is_unique')->setDisabled(1);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Initialize form fileds values
     *
     * @inheritDoc
     */
    protected function _initFormValues()
    {
        Mage::dispatchEvent('adminhtml_block_eav_attribute_edit_form_init', ['form' => $this->getForm()]);
        $this->getForm()
            ->addValues($this->getAttributeObject()->getData());
        return parent::_initFormValues();
    }

    /**
     * This method is called before rendering HTML
     *
     * @return Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $attributeObject = $this->getAttributeObject();
        if ($attributeObject->getId()) {
            $form = $this->getForm();
            $disableAttributeFields = Mage::helper('eav')
                ->getAttributeLockedFields($attributeObject->getEntityType()->getEntityTypeCode());
            if (isset($disableAttributeFields[$attributeObject->getAttributeCode()])) {
                foreach ($disableAttributeFields[$attributeObject->getAttributeCode()] as $field) {
                    if ($elm = $form->getElement($field)) {
                        $elm->setDisabled(1);
                        $elm->setReadonly(1);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Processing block html after rendering
     * Adding js block to the end of this block
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $jsScripts = $this->getLayout()
            ->createBlock('eav/adminhtml_attribute_edit_js')->toHtml();
        return $html.$jsScripts;
    }
}
