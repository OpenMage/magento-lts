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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $fieldset = $this->getForm()->addFieldset('options_fieldset', array(
            'legend'    => $this->helper('cms')->__('Widget Options'),
            'class'     => 'fieldset-wide'
        ));
        $this->setData('main_fieldset', $fieldset);
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
        $config = Mage::getSingleton('cms/widget')->getConfigAsObject($this->getWidgetType());
        if (!$config->getParameters()) {
            return $this;
        }
        $module = $config->getModule();
        $this->_translationHelper = Mage::helper($module ? $module : 'cms');
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
        $data = array(
            'name'      => $form->addSuffixToName($fieldName, 'parameters'),
            'label'     => $this->_translationHelper->__($parameter->getLabel()),
            'required'  => $parameter->getRequired(),
            'class'     => 'widget-option',
            'note'      => $this->_translationHelper->__($parameter->getDescription()),
        );

        if ($values = $this->getWidgetValues()) {
            $data['value'] = (isset($values[$fieldName]) ? $values[$fieldName] : '');
        }
        else {
            $data['value'] = $parameter->getValue();
            //prepare unique id value
            if ($fieldName == 'unique_id' && $data['value'] == '') {
                $data['value'] = md5(microtime(1));
            }
        }

        // prepare element dropdown values
        if ($values  = $parameter->getValues()) {
            // dropdown options are specified in configuration
            $data['values'] = array();
            foreach ($values as $option) {
                $data['values'][] = array(
                    'label' => $this->_translationHelper->__($option['label']),
                    'value' => $option['value']
                );
            }
        }
        // otherwise, a source model is specified
        elseif ($sourceModel = $parameter->getSourceModel()) {
            $data['values'] = Mage::getModel($sourceModel)->toOptionArray();
        }

        // prepare field type and renderers
        $fieldRenderer = 'adminhtml/cms_widget_options_renderer_element';
        $fieldType = $parameter->getType();

        // hidden element
        if (!$parameter->getVisible()) {
            $fieldType = 'hidden';
        }
        // just an element renderer
        elseif (false !== strpos($fieldType, '/')) {
            $fieldType = $this->_defaultElementType;
            $fieldRenderer = $fieldType;
        }

        // instantiate field and render html
        $field = $fieldset->addField('option_' . $fieldName, $fieldType, $data)
            ->setRenderer($this->getLayout()->createBlock($fieldRenderer));

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

        return $field;
    }
}
