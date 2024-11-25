<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Fieldset renderer for PayPal global settings
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @deprecated  since 1.7.0.1
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Global extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Associative array of PayPal product selection elements
     *
     * @var array
     */
    protected $_elements = [];

    /**
     * Custom template
     *
     * @var string
     */
    protected $_template = 'paypal/system/config/fieldset/global.phtml';

    /**
     * Render fieldset html
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $fieldset)
    {
        foreach ($fieldset->getSortedElements() as $element) {
            $htmlId = $element->getHtmlId();
            $this->_elements[$htmlId] = $element;
        }
        $originalData = $fieldset->getOriginalData();
        $this->addData([
            'fieldset_label' => $fieldset->getLegend(),
            'fieldset_help_url' => $originalData['help_url'] ?? '',
        ]);
        return $this->toHtml();
    }

    /**
     * Get array of element objects
     *
     * @return array
     */
    public function getElements()
    {
        return $this->_elements;
    }

    /**
     * Get element by id
     *
     * @param string $elementId
     * @return Varien_Data_Form_Element_Abstract|false
     */
    public function getElement($elementId)
    {
        return $this->_elements[$elementId] ?? false;
    }

    /**
     * Return checkbox html with hidden field for correct config values
     *
     * @return string
     */
    public function getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $configValue = (string) $element->getValue();
        if ($configValue) {
            $element->setChecked(true);
        } else {
            $element->setValue('1');
        }
        if ($element->getCanUseDefaultValue() && $element->getInherit()) {
            $element->setDisabled(true);
        }

        $hidden = new Varien_Data_Form_Element_Hidden([
            'html_id' => $element->getHtmlId() . '_value',
            'name' => $element->getName(),
            'value' => '0',
        ]);
        $hidden->setForm($element->getForm());
        return $hidden->getElementHtml() . $element->getElementHtml();
    }

    /**
     * Whether element should be rendered in "simplified" mode
     *
     * @return bool
     */
    public function getIsElementSimplified(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        return isset($originalData['is_simplified']) && $originalData['is_simplified'] == 1;
    }

    /**
     * Getter for element label
     *
     * @return string
     */
    public function getElementLabel(Varien_Data_Form_Element_Abstract $element)
    {
        return $element->getLabel();
    }

    /**
     * Getter for element comment
     *
     * @return string
     */
    public function getElementComment(Varien_Data_Form_Element_Abstract $element)
    {
        return $element->getComment();
    }

    /**
     * Getter for element comment
     *
     * @return string
     */
    public function getElementOriginalData(Varien_Data_Form_Element_Abstract $element, $key)
    {
        $data = $element->getOriginalData();
        return $data[$key] ?? '';
    }

    /**
     * Check whether checkbox has "Use default" option or not
     *
     * @return bool
     */
    public function hasInheritElement(Varien_Data_Form_Element_Abstract $element)
    {
        return (bool) $element->getCanUseDefaultValue();
    }

    /**
     * Return "Use default" checkbox html
     *
     * @return string
     */
    public function getInheritElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $elementId = $element->getHtmlId();
        $inheritCheckbox = new Varien_Data_Form_Element_Checkbox([
            'html_id' => $elementId . '_inherit',
            'name' => preg_replace('/\[value\](\[\])?$/', '[inherit]', $element->getName()),
            'value' => '1',
            'class' => 'checkbox config-inherit',
            'onclick' => 'toggleValueElements(this, $(\'' . $elementId . '\').up())',
        ]);
        if ($element->getInherit()) {
            $inheritCheckbox->setChecked(true);
        }

        $inheritCheckbox->setForm($element->getForm());
        return $inheritCheckbox->getElementHtml();
    }

    /**
     * Return label for "Use default" checkbox
     *
     * @return string
     */
    public function getInheritElementLabelHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return sprintf(
            '<label for="%s" class="inherit" title="%s">%s</label>',
            $element->getHtmlId() . '_inherit',
            $element->getDefaultValue(),
            Mage::helper('adminhtml')->__('Use Default'),
        );
    }

    /**
     * Return element label with tag SPAN
     *
     * @return string
     */
    public function getElementLabelTextHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return sprintf(
            '<span id="%s">%s</span>',
            $element->getHtmlId() . '_label_text',
            $this->escapeHtml($this->getElementLabel($element)),
        );
    }

    /**
     * Return backend config for element like JSON
     *
     * @return string
     */
    public function getElementBackendConfig(Varien_Data_Form_Element_Abstract $element)
    {
        return Mage::helper('paypal')->getElementBackendConfig($element);
    }
}
