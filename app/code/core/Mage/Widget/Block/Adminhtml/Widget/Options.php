<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/**
 * WYSIWYG widget options form
 *
 * @package    Mage_Widget
 *
 * @method string getMainFieldsetHtmlId()
 * @method string getWidgetType()
 * @method array getWidgetValues()
 * @method $this setMainFieldsetHtmlId(string $value)
 */
class Mage_Widget_Block_Adminhtml_Widget_Options extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Element type used by default if configuration is omitted
     * @var string
     */
    protected $_defaultElementType = 'text';

    /**
     * Translation helper instance, defined by the widget type declaration root config node
     * @var Mage_Core_Helper_Abstract
     */
    protected $_translationHelper = null;

    /**
     * Prepare Widget Options Form and values according to specified type
     *
     * widget_type must be set in data before
     * widget_values may be set before to render element values
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $this->getForm()->setUseContainer(false);
        $this->addFields();
        return $this;
    }

    /**
     * Form getter/instantiation
     *
     * @return Varien_Data_Form
     */
    public function getForm()
    {
        if ($this->_form instanceof Varien_Data_Form) {
            return $this->_form;
        }

        $form = new Varien_Data_Form();
        $this->setForm($form);
        return $form;
    }

    /**
     * Fieldset getter/instantiation
     *
     * @return Varien_Data_Form_Element_Fieldset
     */
    public function getMainFieldset()
    {
        if ($this->_getData('main_fieldset') instanceof Varien_Data_Form_Element_Fieldset) {
            return $this->_getData('main_fieldset');
        }

        $mainFieldsetHtmlId = 'options_fieldset' . md5($this->getWidgetType());
        $this->setMainFieldsetHtmlId($mainFieldsetHtmlId);
        $fieldset = $this->getForm()->addFieldset($mainFieldsetHtmlId, [
            'legend'    => $this->helper('widget')->__('Widget Options'),
            'class'     => 'fieldset-wide',
        ]);
        $this->setData('main_fieldset', $fieldset);

        // add dependence javascript block
        $block = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
        $this->setChild('form_after', $block);

        return $fieldset;
    }

    /**
     * Add fields to main fieldset based on specified widget type
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    public function addFields()
    {
        // get configuration node and translation helper
        if (!$this->getWidgetType()) {
            Mage::throwException($this->__('Widget Type is not specified'));
        }

        $config = Mage::getSingleton('widget/widget')->getConfigAsObject($this->getWidgetType());
        if (!$config->getParameters()) {
            return $this;
        }

        $module = $config->getModule();
        $this->_translationHelper = Mage::helper($module ? $module : 'widget');
        foreach ($config->getParameters() as $parameter) {
            $this->_addField($parameter);
        }

        return $this;
    }

    /**
     * Add field to Options form based on parameter configuration
     *
     * @param Varien_Object $parameter
     * @return Varien_Data_Form_Element_Abstract
     */
    protected function _addField($parameter)
    {
        $form = $this->getForm();
        $fieldset = $this->getMainFieldset(); //$form->getElement('options_fieldset');

        // prepare element data with values (either from request of from default values)
        $fieldName = $parameter->getKey();
        $data = [
            'name'      => $form->addSuffixToName($fieldName, 'parameters'),
            'label'     => $this->_translationHelper->__($parameter->getLabel()),
            'required'  => $parameter->getRequired(),
            'class'     => 'widget-option',
            'note'      => $this->_translationHelper->__($parameter->getDescription()),
        ];

        if ($values = $this->getWidgetValues()) {
            $data['value'] = $values[$fieldName] ?? '';
        } else {
            $data['value'] = $parameter->getValue();
            //prepare unique id value
            if ($fieldName == 'unique_id' && $data['value'] == '') {
                $data['value'] = md5((string) microtime(true));
            }
        }

        // prepare element dropdown values
        if ($values  = $parameter->getValues()) {
            // dropdown options are specified in configuration
            $data['values'] = [];
            foreach ($values as $option) {
                $data['values'][] = [
                    'label' => $this->_translationHelper->__($option['label']),
                    'value' => $option['value'],
                ];
            }
        } elseif ($sourceModel = $parameter->getSourceModel()) { // otherwise, a source model is specified
            $model = Mage::getModel($sourceModel);
            if (method_exists($model, 'toOptionArray')) {
                $data['values'] = $model->toOptionArray();
            }
        }

        // prepare field type or renderer
        $fieldRenderer = null;
        $fieldType = $parameter->getType();
        // hidden element
        if (!$parameter->getVisible()) {
            $fieldType = 'hidden';
        } elseif (str_contains($fieldType, '/')) { // just an element renderer
            $fieldRenderer = $this->getLayout()->createBlock($fieldType);
            $fieldType = $this->_defaultElementType;
        }

        // instantiate field and render html
        $field = $fieldset->addField($this->getMainFieldsetHtmlId() . '_' . $fieldName, $fieldType, $data);
        if ($fieldRenderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
            $field->setRenderer($fieldRenderer);
        }

        // extra html preparations
        if ($helper = $parameter->getHelperBlock()) {
            $helperBlock = $this->getLayout()->createBlock($helper->getType(), '', $helper->getData());
            if ($helperBlock instanceof Varien_Object) {
                $helperBlock->setConfig($helper->getData())
                    ->setFieldsetId($fieldset->getId())
                    ->setTranslationHelper($this->_translationHelper)
                    ->prepareElementHtml($field);
            }
        }

        // dependencies from other fields
        $dependenceBlock = $this->getChild('form_after');
        $dependenceBlock->addFieldMap($field->getId(), $fieldName);
        if ($parameter->getDepends()) {
            foreach ($parameter->getDepends() as $from => $row) {
                $values = isset($row['values']) ? array_values($row['values']) : (string) $row['value'];
                $dependenceBlock->addFieldDependence($fieldName, $from, $values);
            }
        }

        return $field;
    }
}
