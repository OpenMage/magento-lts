<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Form_Fieldset_Modules_DisableOutput extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);

        $modules = array_keys((array) Mage::getConfig()->getNode('modules')->children());

        $dispatchResult = new Varien_Object($modules);
        Mage::dispatchEvent(
            'adminhtml_system_config_advanced_disableoutput_render_before',
            ['modules' => $dispatchResult],
        );
        $modules = $dispatchResult->toArray();

        sort($modules);

        foreach ($modules as $moduleName) {
            if ($moduleName === 'Mage_Adminhtml') {
                continue;
            }
            $html .= $this->_getFieldHtml($element, $moduleName);
        }

        return $html . $this->_getFooterHtml($element);
    }

    protected function _getDummyElement()
    {
        if (empty($this->_dummyElement)) {
            $this->_dummyElement = new Varien_Object(['show_in_default' => 1, 'show_in_website' => 1]);
        }
        return $this->_dummyElement;
    }

    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }
        return $this->_fieldRenderer;
    }

    protected function _getValues()
    {
        if (empty($this->_values)) {
            $this->_values = [
                ['label' => Mage::helper('adminhtml')->__('Enable'), 'value' => 0],
                ['label' => Mage::helper('adminhtml')->__('Disable'), 'value' => 1],
            ];
        }
        return $this->_values;
    }

    protected function _getFieldHtml($fieldset, $moduleName)
    {
        $configData = $this->getConfigData();
        $path = 'advanced/modules_disable_output/' . $moduleName; //TODO: move as property of form
        if (isset($configData[$path])) {
            $data = $configData[$path];
            $inherit = false;
        } else {
            $data = (int) (string) $this->getForm()->getConfigRoot()->descend($path);
            $inherit = true;
        }

        $e = $this->_getDummyElement();

        $field = $fieldset->addField(
            $moduleName,
            'select',
            [
                'name'          => 'groups[modules_disable_output][fields][' . $moduleName . '][value]',
                'label'         => $moduleName,
                'value'         => $data,
                'values'        => $this->_getValues(),
                'inherit'       => $inherit,
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($e),
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($e),
                'scope'         => true,
                'scope_label'   => Mage::helper('adminhtml')->__('[STORE VIEW]'),
            ],
        )->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }
}
