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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * WYSIWYG widget options form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Cms_Widget_Options extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_defaultElementType = 'text';
    protected $_translationHelper = null;

    /**
     * Prepare Widget Options Form and values according to specified type
     *
     * widget_type must be set in data before
     * widget_values may be set before to render element values
     */
    protected function _prepareForm()
    {
        // prepare form instance
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('options_fieldset', array(
            'legend'    => $this->helper('cms')->__('Widget Options')
        ));
        $form->setUseContainer(false);
        $this->setForm($form);

        // get configuration node and translation helper
        if (!$this->getWidgetType()) {
            Mage::throwException($this->__('Widget Type is not specified'));
        }
        $config = Mage::getSingleton('cms/widget')->getXmlElementByType($this->getWidgetType());
        if (!($config instanceof Varien_Simplexml_Element)) {
            return;
        }
            if (!$config->parameters) {
            return;
        }
        $module = (string)$config->getAttribute('module');
        $this->_translationHelper = Mage::helper($module ? $module : 'cms');

        // sort widget parameters and add them to form
        $sortOrder = 0;
        foreach ($config->parameters->children() as $option) {
            $option->sort_order = $option->sort_order ? (int)$option->sort_order : $sortOrder;
            $options[$option->getName()] = $option;
            $sortOrder++;
        }
        uasort($options, array($this, '_sortParameters'));
        foreach ($options as $option) {
            $this->_addField($option);
        }
    }

    /**
     * Widget parameters sort callback
     *
     * @param $a
     * @param $b
     * @return int
     */
    protected function _sortParameters($a, $b)
    {
        $aOrder = (int)$a->sort_order;
        $bOrder = (int)$b->sort_order;
        return $aOrder < $bOrder ? -1 : ($aOrder > $bOrder ? 1 : 0);
    }

    /**
     * Add field to Options form based on option configuration
     *
     * @param Varien_Simplexml_Element $option
     * @param Mage_Core_Helper_Abstract $helper
     * @return Varien_Data_Form_Element_Abstract
     */
    protected function _addField($config)
    {
        $form = $this->getForm();
        $fieldset = $form->getElement('options_fieldset');

        // prepare element data with values (either from request of from default values)
        $fieldName = (string)$config->getName();
        $data = array(
            'name'      => $form->addSuffixToName($fieldName, 'parameters'),
            'label'     => $this->_translationHelper->__((string)$config->label),
            'required'  => (bool)(int)(string)$config->required,
            'class'     => 'widget-option',
            'note'      => $this->_translationHelper->__((string)$config->description),
        );
        if ($values = $this->getWidgetValues()) {
            $data['value'] = (isset($values[$fieldName]) ? $values[$fieldName] : '');
        }
        else {
            $data['value'] = (string)$config->value;
        }

        // prepare element dropdown values, if any
        if ($config->values) {
            // dropdown options are specified in configuration
            if ($config->values->hasChildren()) {
                $data['values'] = array();
                foreach ($config->values->children() as $option) {
                    $data['values'][] = array(
                        'value' => (string)$option->value,
                        'label' => $this->_translationHelper->__((string)$option->label)
                    );
                }
            }
            // a source model is specified
            elseif ($model = Mage::getModel((string)$config->values)) {
                $data['values'] = $model->toOptionArray();
            }
        }

        // prepare field type and renderers
        $fieldRenderer = false;
        $fieldChooserRenderer = false;
        $fieldType = (string)$config->type;
        // hidden element
        if (!(int)(string)$config->visible) {
            $fieldType = 'hidden';
        }
        // element of specified type with a chooser
        elseif ($config->type->hasChildren()) {
            $fieldType = $config->type->element_type ? (string)$config->type->element_type : $this->_defaultElementType;
            if ($config->type->element_helper) {
                $fieldChooserRenderer = $this->getLayout()->getBlockSingleton((string)$config->type->element_helper);
            }
        }
        // just an element renderer
        elseif (false !== strpos($config->type, '/')) {
            $fieldType = $this->_defaultElementType;
            $fieldRenderer = $this->getLayout()->createBlock((string)$config->type);
        }

        // instantiate field and prepare extra html
        $field = $fieldset->addField('option_' . $fieldName, $fieldType, $data);
        if ($fieldRenderer) {
            $field->setRenderer($fieldRenderer);
        } elseif ($fieldChooserRenderer) {
            $fieldChooserRenderer->setFieldsetId($fieldset->getId())
                ->setTranslationHelper($this->_translationHelper)
                ->setConfig($config->type)
                ->prepareElementHtml($field);
        }

        return $field;
    }
}
