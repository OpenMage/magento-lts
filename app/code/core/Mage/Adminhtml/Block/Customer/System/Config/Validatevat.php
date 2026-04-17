<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml VAT ID validation block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_System_Config_Validatevat extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('customer/system/config/validatevat.phtml');
        }

        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData([
            'button_label' => Mage::helper('customer')->__($originalData['button_label']),
            'html_id' => $element->getHtmlId(),
            'ajax_url' => Mage::getSingleton('adminhtml/url')->getUrl('*/customer_system_config_validatevat/validate'),
        ]);

        return $this->_toHtml();
    }
}
