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
class Mage_Adminhtml_Block_System_Config_Form_Fieldset_Order_Statuses extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected $_dummyElement;

    protected $_fieldRenderer;

    protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = ''; //$this->_getHeaderHtml($element);

        $statuses = Mage::getResourceModel('sales/order_status_collection')->load()->toOptionHash();

        foreach ($statuses as $id => $status) {
            $html .= $this->_getFieldHtml($element, $id, $status);
        }

        #$html .= $this->_getFooterHtml($element);

        return $html;
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

    protected function _getFieldHtml($fieldset, $id, $status)
    {
        $configData = $this->getConfigData();
        $path = 'sales/order_statuses/status_' . $id; //TODO: move as property of form
        $data = $configData[$path] ?? [];

        $e = $this->_getDummyElement();

        $field = $fieldset->addField(
            $id,
            'text',
            [
                'name'          => 'groups[order_statuses][fields][status_' . $id . '][value]',
                'label'         => $status,
                'value'         => $data['value'] ?? $status,
                'default_value' => $data['default_value'] ?? '',
                'old_value'     => $data['old_value'] ?? '',
                'inherit'       => $data['inherit'] ?? '',
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($e),
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($e),
            ],
        )->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }
}
